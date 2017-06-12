<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");

/**
 * 小树时代GearmanClient
 * User: Administrator
 * Date: 2017/6/8
 * Time: 10:05
 */
class XSGearmanClient
{
    private $client;
    private $taskName;
    private $taskArgs;
    private $hmvc;
    private $uniqueId;
    private $sync;
    private $serverOnlineCount = 0;
    private $errMsg = '';
    private $syncResult;

    public function __construct()
    {
        $this->uniqueId = $this->genUniqueId();
        //加载配置文件
        $serverList = [];
        $servers = explode(',', C('GEARMAN_SERVERS'));
        foreach ($servers as $host) {
            $serverList[] = explode(':', $host);
        }

        if (count($serverList) == 0) {
            $this->errMsg = 'No configuration gearman server';
        } else {
            $this->client = new GearmanClient();
            foreach ($serverList as $server) {
                $flag = $this->client->addServer($server[0], $server[1]);
                if ($flag) {
                    $this->serverOnlineCount++;
                }
            }
        }
    }

    /**
     * 设置worker执行函数以及参数
     * @param $taskName
     * @param array $taskArgs
     * @param string $hmvc
     */
    public function setFunctionNameAndArgs($taskName, $taskArgs = [], $hmvc = '')
    {
        $this->taskName = $taskName;
        $this->taskArgs = $taskArgs;
        $this->hmvc = $hmvc;
    }

    /**
     * 发送task
     * @param bool $sync boolean  是否同步，默认false
     * @return bool
     */
    public function send($sync = false)
    {
        $this->sync = $sync;
        if ($this->errMsg != '') {
            $this->log($this->errMsg, 'SEND_TASK_FINISHED');
            return false;
        }
        if ($this->serverOnlineCount == 0) {
            $this->errMsg = 'No gearman server online';
            $this->log($this->errMsg, 'SEND_TASK_FINISHED');
            return false;
        }

        $data = [
            'task_name' => $this->taskName,
            'task_args' => $this->taskArgs,
            'unique_id' => $this->uniqueId,
            'hmvc' => $this->hmvc,
            'sync' => $sync ? 1 : 0
        ];
        $workload = serialize($data);
        if ($sync) {
            $this->syncResult = $this->client->doNormal(C('GEARMAN_FUNCTION_NAME'), $workload);
            $this->log('Sending job', 'SEND_TASK_FINISHED');
        } else {
            $this->client->doBackground(C('GEARMAN_FUNCTION_NAME'), $workload);
            $this->log('Sending job', 'SEND_TASK_FINISHED');

        }

        return true;
    }

    /**
     * 获取同步任务执行结果
     * @return mixed
     */
    public function getSyncResult()
    {
        return $this->syncResult;
    }

    /**
     * 生成一个唯一字符串
     * @return string
     */
    private function genUniqueId($prefix='')
    {
        return md5($prefix . microtime() . mt_rand());
    }

    /**
     * 返回当前任务ID，用来查询任务结果
     * @return string
     */
    public function getTaskId()
    {
        return $this->uniqueId;
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

    public function getErrMsg()
    {
        return $this->errMsg;
    }

}