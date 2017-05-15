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
class task_CronService extends Task_Single {
	
	private $disableHmvcList, $enableHmvcList, $disableAllHmvc = false, $disableApplication = false;

	public function execute(\CliArgs $args) {
		Core::functions('cron');
		$f = $args->get('c');
		$this->disableHmvcList = $args->get('hmvc-disable') ? explode(',', $args->get('hmvc-disable')) : array();
		$this->enableHmvcList = $args->get('hmvc-enable') ? explode(',', $args->get('hmvc-enable')) : array();
		$this->disableAllHmvc = $args->get('hmvc-disable-all');
		$this->disableApplication = $args->get('disable-application');
		$configFilename = empty($f) ? 'task' : $f;
		$last = array();
		echo "Cron Service stared\n";
		$currentSecond = 0;
		while (true) {
			if ($currentSecond == time()) {
				continue;
			}
			try {
				$tasks = $this->scan($configFilename);
				foreach ($last as $_t) {
					if (!isset($tasks[$_t['id']])) {
						$this->kill($_t['pidfile']);
					}
				}
				foreach ($tasks as $task) {
					$this->start($task);
				}
				$last = $tasks;
			} catch (Throwable $t) {
				$this->_log($t->getTraceAsString());
			} catch (Exception $exc) {
				$this->_log($exc->getTraceAsString());
			}
			$currentSecond = time();
			usleep(1000);
		}
	}

	private function kill($pidfile) {
		if (@file_exists($pidfile) && ($pid = @file_get_contents($pidfile))) {
			$pid = trim($pid, "\n");
			if (PATH_SEPARATOR == ':') {
				$this->exec("kill -9 $pid");
			} else {
				$this->exec("tskill $pid");
			}
		}
	}

	private function exec($cmd) {
		pclose(popen($cmd . " &", 'r'));
	}

	private function start($task) {
		try {
			$cron = SimpleCron::factory($task['cron']);
		} catch (Exception $ex) {
			$this->_log("ERROR , cron expression is not valid. Reason:" . $ex->getTraceAsString() . "  ,Task:" . json_encode($task));
			return;
		}
		if ($cron->isDue()) {
			if ($task['log']) {
				if (@file_exists($task['log_path']) && @filesize($task['log_path']) > $task['log_size']) {
					$this->exec("echo \"$(tail -n 50 {$task['log_path']})\" > {$task['log_path']}", 'r');
					$this->_log("{$task['log_path']} resized");
				}
				$this->exec("{$task['cmd']} >>{$task['log_path']} 2>&1", 'r');
				$this->_log("task {$task['file']} stared and logged");
			} else {
				$this->exec($task['cmd'], 'r');
				$this->_log("task {$task['id']} stared");
			}
		}
	}

	private function scan($configFilename) {
		$packages = array();
		if (!$this->disableApplication) {
			foreach (Core::config()->getPackages() as $package) {
				$packages[] = array(
				    'hmvc' => false,
				    'path' => $package
				);
			}
		}

		if (!$this->disableAllHmvc) {
			$hmvcPath = Core::realPath(Core::config()->getApplicationDir()) . '/' . Core::config()->getHmvcDirName();
			foreach (Core::config()->getHmvcModules() as $key => $value) {
				if (!empty($this->enableHmvcList)) {
					if (!in_array($key, $this->enableHmvcList)) {
						continue;
					} elseif (in_array($key, $this->disableHmvcList)) {
						continue;
					}
				}
				if (!empty($this->disableHmvcList) && in_array($key, $this->disableHmvcList)) {
					continue;
				}
				$packages[] = array(
				    'hmvc' => array('key' => $key, 'folder' => $value),
				    'path' => $hmvcPath . '/' . $value . '/'
				);
			}
		}
		$taskConfig = array();
		$environment = Core::config()->getEnvironment();
		foreach ($packages as $package) {
			$taskDirectory = Core::realPath($package['path']) . '/' . Core::config()->getClassesDirName() . '/' . Core::config()->getTaskDirName();
			$configPath = Core::realPath($package['path']) . '/' . Core::config()->getConfigDirName();
			$item = array(
			    'root' => $taskDirectory,
			    'config' => '',
			    'hmvc' => $package['hmvc']
			);
			if (@file_exists($f = $configPath . '/' . $environment . '/' . $configFilename . '.php')) {
				$item['config'] = $f;
			} elseif (@file_exists($f = $configPath . '/default/' . $configFilename . '.php')) {
				$item['config'] = $f;
			}
			if (!empty($item['config'])) {
				$taskConfig[] = $item;
			}
		}

		$index = $GLOBALS['argv'][0];
		$tasks = array();
		foreach ($taskConfig as $config) {
			if (!$this->checkConfig($config['config'], $cfg)) {
				$this->_log("config file content error  : " . $config['config']);
				continue;
			}
			$this->_log("config loaded  : " . $config['config']);
			if (!$cfg['enable']) {
				continue;
			}
			foreach ($cfg['tasks'] as $task) {
				if (!$task['enable']) {
					continue;
				}
				$taskItem['class'] = $task['class'];
				$taskItem['file'] = $config['root'] . '/' . str_replace('_', '/', $task['class']) . '.php';
				if (!@file_exists($taskItem['file'])) {
					$this->_log("WARN , task file not found. {$taskItem['file']}");
					continue;
				}
				$taskItem['pid'] = empty($task['pidfile']) ? Core::config()->getStorageDirPath() . 'TaskAutoExecutor' . md5($taskItem['file']) . '.pid' : $task['pidfile'];
				$taskItem['args'] = $task['args'];
				$taskItem['env'] = $environment;
				$taskItem['php'] = $cfg['php_bin'];
				$taskItem['cron'] = $task['cron'];
				$taskItem['log'] = $task['log'];
				$taskItem['log_path'] = $task['log_path'];
				$taskItem['log_size'] = $task['log_size'];
				$taskItem['env'] = $environment;
				$taskItem['hmvc'] = $config['hmvc'] ? $config['hmvc']['key'] : false;
				$taskItem['cmd'] = "{$taskItem['php']} $index --task={$taskItem['class']}  --env={$taskItem['env']} {$taskItem['args']}" . ($taskItem['hmvc'] ? " --hmvc={$taskItem['hmvc']}" : '');
				$taskItem['lastModify'] = @filemtime($taskItem['file']);
				$taskItem['id'] = md5($taskItem['file'] . $taskItem['pid']);
				$tasks[$taskItem['id']] = $taskItem;
			}
		}
		//$this->_log("task scaned , count : " . count($tasks));
		return $tasks;
	}

	private function checkConfig($config, &$cfg) {
		try {
			$cfg = @eval('?>' . @file_get_contents($config));
		} catch (Throwable $t) {
			
		} catch (Exception $e) {
			
		}
		if (!is_array($cfg) || (is_array($cfg) && (empty($cfg['php_bin']) || !isset($cfg['php_bin']) || !isset($cfg['tasks']) || !is_array($cfg['tasks'])))) {
			return false;
		}
		foreach ($cfg['tasks'] as $v) {
			if (empty($v['class']) || !isset($v['enable']) || !isset($v['args']) || !isset($v['pidfile']) || empty($v['cron']) || !SimpleCron::isValidExpression($v['cron']) || !isset($v['log']) || !isset($v['log_path']) || !isset($v['log_size']) || $v['log_size'] <= 0) {
				return false;
			}
		}
		return true;
	}
}
