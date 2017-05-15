<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_database extends Business {
	//分卷大小
	private $volumesize = 2048;
	private $tables = array();
	private $name = '';
	private $error = '';
	private $volnext = true;
	private $restorenext = true;
	private $restorefiles = array();
	
	public function getCurrentRestoreFile(){
		$files=$this->getRestoreFiles();
		if(!$files || empty($files)){
			return false;
		}
		return str_replace('.sql','',substr($files[0],strpos($files[0], '_')+1));
	}

	public function setRestoreFiles($files) {
		$this -> restorefiles = $files;
		\Core::sessionSet('restore_files', $this -> restorefiles);
		return $this;
	}

	public function getRestoreFiles() {
		$this -> restorefiles = \Core::session('restore_files');
		return $this -> restorefiles;
	}

	public function clearRestoreFiles() {
		\Core::sessionUnset('restore_files');
	}

	public function getRestoreNext() {
		return $this -> restorenext;
	}

	public function setRestoreNext($value) {
		if ($value == FALSE) {
			$this -> clearRestoreFiles();
		}
		$this -> restorenext = $value;
	}

	public function setName($name) {
		$this -> name = $name;
		\Core::sessionSet('backup_name', $this -> name);
		return $this;
	}

	public function getName() {
		$this -> name = \Core::session('backup_name');
		return $this -> name;
	}

	public function clearName() {
		\Core::sessionUnset('backup_name');
	}

	public function getVolNext() {
		return $this -> volnext;
	}

	public function setVolNext($value) {
		if ($value == FALSE) {
			$this -> clearName();
			$this -> clearTables();
			$this -> clearVolumeSize();
		}
		$this -> volnext = $value;
	}

	public function getError() {
		return $this -> error;
	}

	public function setError($err) {
		$this -> error = $err;
	}

	public function setVolumeSize($size) {
		if (!is_numeric($size) || $size < 20) {
			$this -> volumesize = 20;
		} else {
			$this -> volumesize = $size;
		}
		\Core::sessionSet('backup_volume', $this -> volumesize);
		return $this;
	}

	public function getVolumeSize() {
		$this -> volumesize = \Core::session('backup_volume');
		return $this -> volumesize;
	}

	public function clearVolumeSize() {
		\Core::sessionUnset('backup_volume');
	}

	public function setTables(Array $tables) {
		$this -> tables = $tables;
		\Core::sessionSet('backup_tables', $this -> tables);
		return $this;
	}

	public function getTables() {
		$this -> tables = \Core::session('backup_tables');
		return $this -> tables;
	}

	public function clearTables() {
		\Core::sessionUnset('backup_tables');
	}

	private function query($sql = '') {
		$list = \Core::db() -> execute($sql) -> rows();
		return $list;
	}

	public function getPdoTables() {
		$rs = $this -> query("show tables");

		$tables = array();
		foreach ($rs as $v) {
			$tables[] = current($v);
		}
		return $tables;
	}

	public function getTableStruct($table) {
		$sql = "SHOW CREATE TABLE `{$table}`";
		$struct = $this -> query($sql);
		$struct = $struct[0]['Create Table'] . ';';

		return $struct;
	}

	public function getData($table) {
		$sql = "SHOW COLUMNS FROM `{$table}`";
		$list = $this -> query($sql);
		//字段
		$columns = '';
		//需要返回的SQL
		$query = '';

		foreach ($list as $value) {
			$columns .= "`{$value['Field']}`,";
		}

		$columns = substr($columns, 0, -1);
		$data = $this -> query("SELECT * FROM `{$table}`");
		foreach ($data as $value) {
			$dataSql = '';
			foreach ($value as $v) {
				$dataSql .= "'{$v}',";
			}
			$dataSql = substr($dataSql, 0, -1);
			$query .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$dataSql});\r\n";
		}
		return $query;
	}

	public function mkdirs($dir) {
		if (!is_dir($dir)) {
			if (!$this -> mkdirs(dirname($dir))) {
				return false;
			}
			if (!mkdir($dir, 0777)) {
				return false;
			}
		}
		return true;
	}

	/*
	 * 备份表
	 * @param $name 备份名称
	 * @param $vol 分卷号
	 */
	public function backup($vol = 1) {
		//存储表定义语句的数组
		$struct = array();
		//存储数据的数组
		$data = array();
		//table
		$backuptables = array();
		$i = 0;
		$len = 0;
		$tables = $this -> getTables();

		if ($vol == 1 && !$tables) {
			$this -> setError(\Core::L('backup_tables_null'));
			return FALSE;
		}

		if ($tables != null && !empty($tables)) {
			foreach ($tables as $table) {
				$tb_struct = $this -> getTableStruct($table);
				$tb_data = $this -> getData($table);
				$struct[] = $tb_struct;
				$data[] = $tb_data;
				$backuptables[] = $table;
				$i++;
				$len += strlen($tb_struct) + strlen($tb_data);
				if ($len > $this -> getVolumeSize() * 1024) {
					break;
				}
			}
			array_splice($tables, 0, $i);
			$this -> setTables($tables);

			//开始写入
			$this -> writeToFile($this -> getName(), $vol, $backuptables, $struct, $data);

			if (empty($tables)) {
				$this -> setVolNext(false);
			}
		}
	}

	public function writeToFile($name, $vol, $tables, $struct, $data) {
		$str = "/*\r\nDZ MySQL Database Backup \r\n";
		$str .= "Data:" . date('Y-m-d H:i:s', time()) . "\r\n*/\r\n";
		$str .= "SET FOREIGN_KEY_CHECKS=0;\r\n";
		$i = 0;
		foreach ($tables as $table) {
			$str .= "-- ----------------------------\r\n";
			$str .= "-- Table structure for {$table}\r\n";
			$str .= "-- ----------------------------\r\n";
			$str .= "DROP TABLE IF EXISTS `{$table}`;\r\n";
			$str .= $struct[$i] . "\r\n";
			$str .= "-- ----------------------------\r\n";
			$str .= "-- Records of {$table}\r\n";
			$str .= "-- ----------------------------\r\n";
			$str .= $data[$i] . "\r\n";
			$i++;
		}
		//创建文件夹和文件
		$dirpath = STORAGE_PATH . 'backup' . DIRECTORY_SEPARATOR . $name;
		if (!is_dir($dirpath)) {
			if (!$this -> mkdirs($dirpath, 0755)) {
				$this -> setError(\Core::L('backup_mkdir_fail'));
				return false;
			}
			$fp = @fopen($dirpath . DIRECTORY_SEPARATOR . 'index.html', 'w+');
			@fclose($fp);
		}
		$filepath = $dirpath . DIRECTORY_SEPARATOR . \Core::encrypt($name) . '_' . $vol . '.sql';
		$fp = @fopen($filepath, 'w+');
		if (@fwrite($fp, $str) === false) {
			$this -> setError(\Core::L('backup_mkfile_fail'));
		}
		@fclose($fp);
	}

	//还原表
	public function restore() {
		if (empty($this -> getRestoreFiles())) {
			$this -> setError(\Core::L('restore_file_no_exsits'));
			return false;
		} else {
			$files = $this -> getRestoreFiles();
			$path = $files[0];
			$sql = $this -> parseSQL($path);
			try {
				\Core::db() -> execute($sql);
				array_splice($files, 0, 1);
				$this -> setRestoreFiles($files);
				if (empty($files)) {
					$this -> setRestoreNext(false);
				}
			} catch (PDOException $e) {
				$this -> setError($e -> getMessage());
				return false;
			}
		}
	}

	private function parseSQL($path = '') {
		$sql = file_get_contents($path);
		$sql = explode("\r\n", $sql);
		//先消除--注释
		$sql = array_filter($sql, function($data) {
			if (empty($data) || preg_match('/^--.*/', $data)) {
				return false;
			} else {
				return true;
			}
		});
		$sql = implode('', $sql);
		//删除/**/注释
		$sql = preg_replace('/\/\*.*\*\//', '', $sql);
		return $sql;
	}

}
