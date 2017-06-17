<?php
namespace XSQueue\Client;

use XSQueue\Client\LoggerInterface;

class Logger implements LoggerInterface {
	
	private $logsDirPath;
	
	public function __construct($logsDirPath='') {
		$dir=$logsDirPath?$logsDirPath:dirname(__DIR__);
		$this -> logsDirPath = $dir . '/' . date("Y-m-d/H") . '/';
	}

	public function write($info) {
		if(!$info){
			return;
		}
		if (!is_dir($this -> logsDirPath)) {
			mkdir($this -> logsDirPath, 0700, true);
		}
		if (!file_exists($logsFilePath = $this -> logsDirPath . 'logs.php')) {
			$info = '<?php defined("IN_XIAOSHU") or exit("Access Invalid!");?>' . "\n" . $info;
		}
		file_put_contents($logsFilePath, $info, LOCK_EX | FILE_APPEND);
	}

}
?>