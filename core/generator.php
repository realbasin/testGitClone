<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");

/*
 * 默认控制台操作
 */
class Generator_Default extends Task {

	public function execute(CliArgs $args) {
		$config = \Core::config();
		$name = $args -> get('name');
		$type = $args -> get('type');
		$force = $args -> get('overwrite');
		$hmvc = $args -> get('hmvc');
		if (empty($name)) {
			exit('name required , please use : --name=<Name>');
		}
		if (empty($type)) {
			exit('type required , please use : --type=<Type>');
		}
		$classesDir = $config -> getPrimaryApplicationDir() . $config -> getClassesDirName() . '/';
		if ($hmvc) {
			$classesDir = $config -> getPrimaryApplicationDir() . $config -> getHmvcDirName() . '/' . $hmvc . '/' . $config -> getClassesDirName() . '/';
		}
		$info = array('controller' => array('dir' => $config -> getControllerDirName(), 'parentClass' => 'Controller', 'methodName' => \Core::config() -> getMethodPrefix() . 'index()', 'nameTip' => 'Controller'), 'business' => array('dir' => $config -> getBusinessDirName(), 'parentClass' => 'Business', 'methodName' => 'business()', 'nameTip' => 'Business'), 'model' => array('dir' => $config -> getModelDirName(), 'parentClass' => 'Model', 'methodName' => 'model()', 'nameTip' => 'Model'), 'task' => array('dir' => $config -> getTaskDirName(), 'parentClass' => 'Task', 'methodName' => 'execute(CliArgs $args)', 'nameTip' => 'Task'));
		if (!\Core::arrayKeyExists($type, $info)) {
			exit('[ Error ]' . "\n" . 'Type : [ ' . $type . ' ]');
		}
		$classname = $info[$type]['dir'] . '_' . $name;
		$file = $classesDir . str_replace('_', '/', $classname) . '.php';
		$method = $info[$type]['methodName'];
		$parentClass = $info[$type]['parentClass'];
		$tip = $info[$type]['nameTip'];
		if (file_exists($file)) {
			if ($force) {
				$this -> writeFile($classname, $method, $parentClass, $file, $tip);
			} else {
				exit('[ Error ]' . "\n" . $tip . ' [ ' . $classname . ' ] already exists , ' . "{$file}\n" . 'you can use --overwrite to overwrite the file.');
			}
		} else {
			$this -> writeFile($classname, $method, $parentClass, $file, $tip);
		}
	}

	private function writeFile($classname, $method, $parentClass, $file, $tip) {
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		$code = "<?php\ndefined('IN_XIAOSHU') or exit('Access Invalid!');\nclass  {$classname} extends {$parentClass} {\n	public function {$method} {\n		\n	}\n}";
		if (file_put_contents($file, $code)) {
			echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
		}
	}

}

/*
 * 控制台生成数据类
 */
class Generator_Mysql extends Task {

	public function execute(CliArgs $args) {
		$config = \Core::config();
		$name = $args -> get('name');
		$type = $args -> get('type');
		$force = $args -> get('overwrite');
		$table = $args -> get('table');
		$dbGroup = $args -> get('db');
		$hmvc = $args -> get('hmvc');
		if (empty($name)) {
			exit('name required , please use : --name=<Name>');
		}
		if (empty($table)) {
			exit('table name required , please use : --table=<Table Name>');
		}
		if (empty($type)) {
			exit('type required , please use : --type=<Type>');
		}
		$columns = self::getTableFieldsInfo($table, $dbGroup);
		$primaryKey = '';
		$classesDir = $config -> getPrimaryApplicationDir() . $config -> getClassesDirName() . '/';
		if ($hmvc) {
			$classesDir = $config -> getPrimaryApplicationDir() . $config -> getHmvcDirName() . '/' . $hmvc . '/' . $config -> getClassesDirName() . '/';
		}
		$info = array('bean' => array('dir' => $config -> getBeanDirName(), 'parentClass' => 'Bean', 'nameTip' => 'Bean'), 'dao' => array('dir' => $config -> getDaoDirName(), 'parentClass' => 'Dao', 'nameTip' => 'Dao'), );
		if (!\Core::arrayKeyExists($type, $info)) {
			exit('[ Error ]' . "\n" . 'Type : [ ' . $type . ' ]');
		}
		$classname = $info[$type]['dir'] . '_' . $name;
		$file = $classesDir . str_replace('_', '/', $classname) . '.php';
		$parentClass = $info[$type]['parentClass'];
		$tip = $info[$type]['nameTip'];
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		if ($type == 'bean') {
			$methods = array();
			$fields = array();
			$fieldTemplate = "	//{comment}\n	private \${column0};";
			$methodTemplate = "	public function get{column}() {\n		return \$this->{column0};\n	}\n\n	public function set{column}(\${column1}) {\n		\$this->{column0} = \${column1};\n		return \$this;\n	}";
			foreach ($columns as $value) {
				$column = str_replace(' ', '', ucwords(str_replace('_', ' ', $value['name'])));
				$column0 = $value['name'];
				$column1 = lcfirst($column);
				$fields[] = str_replace(array('{column0}', '{comment}'), array($column0, $value['comment']), $fieldTemplate);
				$methods[] = str_replace(array('{column}', '{column0}', '{column1}'), array($column, $column0, $column1), $methodTemplate);
			}
			$code = "<?php\ndefined('IN_XIAOSHU') or exit('Access Invalid!');\n\nclass {$classname} extends {$parentClass} {\n\n{fields}\n\n{methods}\n\n}";
			$code = str_replace(array('{fields}', '{methods}'), array(implode("\n\n", $fields), implode("\n\n", $methods)), $code);
		} else {
			$columnsString = '';
			$_columns = array();
			foreach ($columns as $value) {
				if ($value['primary']) {
					$primaryKey = $value['name'];
				}
				$_columns[] = '\'' . $value['name'] . "'//" . $value['comment'] . "\n				";
			}
			$columnsString = "array(\n				" . implode(',', $_columns) . ')';
			$code = "<?php\ndefined('IN_XIAOSHU') or exit('Access Invalid!');\n\nclass {$classname} extends {$parentClass} {\n\n	public function getColumns() {\n		return {columns};\n	}\n\n	public function getPrimaryKey() {\n		return '{primaryKey}';\n	}\n\n	public function getTable() {\n		return '{table}';\n	}\n\n}\n";
			$code = str_replace(array('{columns}', '{primaryKey}', '{table}'), array($columnsString, $primaryKey, $table), $code);
		}
		if (file_exists($file)) {
			if ($force) {
				if (file_put_contents($file, $code)) {
					echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
				}
			} else {
				exit('[ Error ]' . "\n" . $tip . ' [ ' . $classname . ' ] already exists , ' . "{$file}\n" . 'you can use --overwrite to overwrite the file.');
			}
		} else {
			if (file_put_contents($file, $code)) {
				echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
			}
		}
	}

	private static function getTableFieldsInfo($tableName, $db) {
		if (!is_object($db)) {
			$db = \Core::db($db);
		}
		if (strtolower($db -> getDriverType()) != 'mysql') {
			throw new \Xs_Exception_500('getTableFieldsInfo() only for mysql database');
		}
		$info = array();
		$result = $db -> execute('SHOW FULL COLUMNS FROM ' . $db -> getTablePrefix() . $tableName) -> rows();
		if ($result) {
			foreach ($result as $val) {
				$info[$val['Field']] = array('name' => $val['Field'], 'type' => $val['Type'], 'comment' => $val['Comment'] ? $val['Comment'] : $val['Field'], 'notnull' => $val['Null'] == 'NO' ? 1 : 0, 'default' => $val['Default'], 'primary' => (strtolower($val['Key']) == 'pri'), 'autoinc' => (strtolower($val['Extra']) == 'auto_increment'), );
			}
		}
		return $info;
	}

}
?>