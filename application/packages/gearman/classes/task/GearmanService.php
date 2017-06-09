<?php

/*
 * task自动执行服务
 * 需要使用自动服务的服务器还必须配置addPackage或addPackages将包自动加入主包中
 * ->addPackage(PACKAGES_PATH.cron)
 * 控制台参数
 * --task 计划任务服务名称,固定是CronService
 * --evn 环境参数,可以是的development,testing,production,对应配置文件夹下面的子目录名称
 * -c task配置文件名称,默认是task.
 * --disable-application 设置了此参数那么就会禁用主项目的计划任务.
 * --hmvc-disable-all 设置了此参数那么就会禁用所有hmvc项目的计划任务.
 * --hmvc-enable 设置只启用这些hmvc项目的计划任务,值是hmvc项目的模块名称,多个用逗号分割.比如:admin,api
 * --hmvc-disable 设置需要禁用的hmvc项目的计划任务,,值是hmvc项目的模块名称,多个用逗号分割.比如:admin,api
 * 在控制台输入 php /home/wwwroot/admin.xiaoshushidai.cn/index.php task=task_CronService
 * 就可以启用定时任务功能
 */

class task_GearmanService extends Task_Single
{

    private $worker;
    private $taskName;
    private $taskArgs;
    private $uniqueId;
    private $sync;

    private function init()
    {
        $serverList = [];
        $servers = explode(',', C('GEARMAN_SERVERS'));
        foreach ($servers as $host) {
            $serverList[] = explode(':', $host);
        }

        $this->worker = new GearmanWorker();
        foreach ($serverList as $server) {
            $this->worker->addServer($server[0], $server[1]);
        }
    }

    /**
     * 写入日志表
     * @param $content
     * @param $status
     * @param null $result
     */
    private function log($content, $status, $result = null)
    {
        $data = [
            'task_name' => $this->taskName,
            'task_args' => serialize($this->taskArgs),
            'content' => $content,
            'add_time' => time(),
            'unique_id' => $this->uniqueId,
            'status' => $status,
            'sync' => $this->sync
        ];

        if ($result !== null) {
            $data['result'] = $result;
        }

        $gearmanLogDao = \Core::dao('gearmanlog');
        $gearmanLogDao->insert($data);
    }

    public function execute(\CliArgs $args)
    {
        echo "Gearmen Service stared\n";
        try {
            $this->init();
            $this->worker->addFunction(C('GEARMAN_FUNCTION_NAME'), function ($job) {
                $workload = $job->workload();

                $params = unserialize($workload);
                $this->taskName = $params['task_name'];
                $this->taskArgs = $params['task_args'];
                $this->uniqueId = $params['unique_id'];
                $this->sync = $params['sync'];

                $this->log('任务开始处理', 'TASK_START_EXECUTE', null);

                //对参数进行处理，如果是
                $args = $this->processArgs($this->taskArgs);
                $hmvc = $params['hmvc'];

                //执行一个shell命令,取得shell返回结果
                $cfg = $this->getConfig('task');
                if ($cfg == null) {
                    $this->log('获取配置信息失败', 'TASK_FAILED', null);
                    return false;
                }

                $index = $GLOBALS['argv'][0];
                $cfg['cmd'] = "{$cfg['php_bin']} -c {$cfg['task_ini_path']} $index --task={$this->taskName} {$args}" . ($hmvc ? " --hmvc={$hmvc}" : '');
                $result = $this->start($cfg);

                if ($result !== null) {
                    $this->log('任务执行完成', 'TASK_FINISHED', $result);
                } else {
                    $this->log('任务执行失败', 'TASK_FAILED');
                }
                return $result;
            });

            while (true) {
                //等待job提交的任务
                $ret = $this->worker->work();
                if ($this->worker->returnCode() != GEARMAN_SUCCESS) {
                    break;
                }
            }
        } catch (Throwable $t) {
            $this->log($t->getTraceAsString(), 'TASK_FAILED');
        } catch (Exception $exc) {
            $this->log($exc->getTraceAsString(), 'TASK_FAILED');
        }
    }

    /**
     * 处理参数
     * @param $args
     * @return string
     */
    private function processArgs($args)
    {
        $arr = [];

        $index = 1;
        foreach ($args as $arg) {
            $arr[] = '--arg' . $index . '=' . $arg;
            $index++;
        }

        return implode(' ', $arr);
    }


    private function exec($cmd)
    {
        return shell_exec($cmd);
    }

    private function start($task)
    {

        echo "run cmd before:" . $task['cmd'] . "\n";
        $result = $this->exec($task['cmd']);
        echo "run cmd after,result" . $result . "\n";
        return $result;
    }

    private function getConfig($configFilename)
    {
        $environment = Core::config()->getEnvironment();
        $packagePath = Core::realPath(dirname(dirname(dirname(__FILE__))));
        $configPath = $packagePath . '/' . Core::config()->getConfigDirName();

        $realConfigFilePath = '';
        if (@file_exists($f = $configPath . '/' . $environment . '/' . $configFilename . '.php')) {
            $realConfigFilePath = $f;
        } elseif (@file_exists($f = $configPath . '/default/' . $configFilename . '.php')) {
            $realConfigFilePath = $f;
        }

        $cfg = [];
        if (!$this->checkConfig($realConfigFilePath, $cfg)) {
            return null;
        }

        return $cfg;
    }

    private function checkConfig($config, &$cfg)
    {
        try {
            $cfg = @eval('?>' . @file_get_contents($config));
        } catch (Throwable $t) {

        } catch (Exception $e) {

        }
        if (!is_array($cfg) || (is_array($cfg) && empty($cfg['php_bin']) && empty($cfg['task_ini_path']))) {
            return false;
        }

        return true;
    }
}
