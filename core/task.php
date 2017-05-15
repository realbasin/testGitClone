<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
abstract class Task{
	
	protected $debug = false, $debugError = false;
	
	public function __construct() {
		if (!\Core::isCli()) {
			throw new \Xs_Exception_500('Task only in cli mode');
		}
		if (!function_exists('shell_exec')) {
			throw new \Xs_Exception_500('Function [ shell_exec ] was disabled , run task must be enabled it .');
		}
	}
	
	public function _execute(CliArgs $args) {
		$this->debug = $args->get('debug');
		$this->debugError = $args->get('debug-error');
		$startTime = \Core::microtime();
		$class = get_class($this);
		if ($this->debugError) {
			$_startTime = date('Y-m-d H:i:s.') . substr($startTime . '', strlen($startTime . '') - 3);
			$error = $this->execute($args);
			if ($error) {
				$this->_log('Task [ ' . $class . ' ] execute failed , started at [ ' . $_startTime . ' ], use time ' . (\Core::microtime() - $startTime) . ' ms , exited with error : [ ' . $error . ' ]');
				$this->_log('', false);
			}
		} else {
			$this->_log('Task [ ' . $class . ' ] start');
			$this->execute($args);
			$this->_log('Task [ ' . $class . ' ] end , use time ' . (\Core::microtime() - $startTime) . ' ms');
			$this->_log('', false);
		}
	}

	public function _log($msg, $time = true) {
		if ($this->debug || $this->debugError) {
			$nowTime = '' . \Core::microtime();
			echo ($time ? date('[Y-m-d H:i:s.' . substr($nowTime, strlen($nowTime) - 3) . ']') . ' [PID:' . sprintf('%- 5d', getmypid()) . '] ' : '') . $msg . "\n";
		}
	}
	public final function pidIsExists($pid) {
		if (PATH_SEPARATOR == ':') {
			//linux
			return trim(shell_exec("ps ax | awk '{ print $1 }' | grep -e \"^{$pid}$\""), "\n") == $pid;
		} else {
			//windows
			return preg_match("/\t?\s?$pid\t?\s?/", shell_exec('tasklist /NH /FI "PID eq ' . $pid . '"'));
		}
	}

	abstract function execute(CliArgs $args);
	
}

abstract class Task_Single extends Task {
	public function _execute(CliArgs $args) {
		$this->debug = $args->get('debug');
		$class = get_class($this);
		$startTime = \Core::microtime();
		$this->_log('Single Task [ ' . $class . ' ] start');
		$lockFilePath = $args->get('pid');
		if (!$lockFilePath) {
			$tempDirPath = \Core::config()->getStorageDirPath();
			$key = md5(\Core::config()->getApplicationDir() .
				\Core::config()->getClassesDirName() . '/'
				. \Core::config()->getTaskDirName() . '/'
				. str_replace('_', '/', get_class($this)) . '.php');
			$lockFilePath = \Core::realPath($tempDirPath) . '/' . $key . '.pid';
		}
		if (file_exists($lockFilePath)) {
			$pid = file_get_contents($lockFilePath);
			//lockfile进程pid存在，直接返回
			if ($this->pidIsExists($pid)) {
				$this->_log('Single Task [ ' . $class . ' ] is running with pid ' . $pid . ' , now exiting...');
				$this->_log('Single Task [ ' . $class . ' ] end , use time ' . (\Core::microtime() - $startTime) . ' ms');
				$this->_log('', false);
				return;
			}
		}
		//写入进程pid到lockfile
		if (file_put_contents($lockFilePath, getmypid()) === false) {
			throw new \Xs_Exception_500('can not create file : [ ' . $lockFilePath . ' ]');
		}
		$this->_log('update pid file [ ' . $lockFilePath . ' ]');
		$this->execute($args);
		@unlink($lockFilePath);
		$this->_log('clean pid file [ ' . $lockFilePath . ' ]');
		$this->_log('Single Task [ ' . $class . ' ] end , use time ' . (\Core::microtime() - $startTime) . ' ms');
		$this->_log('', false);
	}
}

abstract class Task_Multiple extends Task {
	protected abstract function getMaxCount();
	public function _execute(CliArgs $args) {
		$this->debug = $args->get('debug');
		$class = get_class($this);
		$startTime = \Core::microtime();
		$this->_log('Multiple Task [ ' . $class . ' ] start');
		$lockFilePath = $args->get('pid');
		if (!$lockFilePath) {
			$tempDirPath = \Core::config()->getStorageDirPath();
			$key = md5(\Core::config()->getApplicationDir() .
				\Core::config()->getClassesDirName() . '/'
				. \Core::config()->getTaskDirName() . '/'
				. str_replace('_', '/', get_class($this)) . '.php');
			$lockFilePath = \Core::realPath($tempDirPath) . '/' . $key . '.pid';
		}
		$alivedPids = array();
		if (file_exists($lockFilePath)) {
			$count = 0;
			$pids = explode("\n", file_get_contents($lockFilePath));
			foreach ($pids as $pid) {
				if ($pid = (int) $pid) {
					if ($this->pidIsExists($pid)) {
						$alivedPids[] = $pid;
						if (++$count > $this->getMaxCount() - 1) {
							//进程数达到最大值，直接返回
							$this->_log('Multiple Task [ ' . $class . ' ] reach max count : ' . $this->getMaxCount() . ' , now exiting...');
							$this->_log('Multiple Task [ ' . $class . ' ] end , use time ' . (\Core::microtime() - $startTime) . ' ms');
							$this->_log('', false);
							return;
						}
					}
				}
			}
		}
		$alivedPids[] = getmypid();
		//写入存活进程pid到lockfile
		if (file_put_contents($lockFilePath, implode("\n", $alivedPids)) === false) {
			throw new \Xs_Exception_500('can not create file : [ ' . $lockFilePath . ' ]');
		}
		$this->_log('update pid file [ ' . $lockFilePath . ' ]');
		$this->execute($args);
		$this->_log('clean pid file [ ' . $lockFilePath . ' ]');
		$this->_log('Multiple Task [ ' . $class . ' ] end , use time ' . (\Core::microtime() - $startTime) . ' ms');
		$this->_log('', false);
	}
}
?>