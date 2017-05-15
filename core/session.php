<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
abstract class Xs_Session {

	protected $config;
	public function __construct($configFileName) {
		if (is_array($configFileName)) {
			$this -> config = $configFileName;
		} else {
			$this -> config = \Core::config($configFileName);
		}
	}

	public abstract function init();

}

class Xs_Session_Redis extends Xs_Session {
	public function init() {
		ini_set('session.save_handler', 'redis');
		ini_set('session.save_path', $this -> config['path']);
	}

}

class Xs_Session_Memcached extends Xs_Session {
	public function init() {
		ini_set('session.save_handler', 'memcached');
		ini_set('session.save_path', $this -> config['path']);
	}

}

class Xs_Session_Memcache extends Xs_Session {
	public function init() {
		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', $this -> config['path']);
	}

}

class Xs_Session_Mongodb extends Xs_Session {
	private $__mongo_collection = NULL;
	private $__current_session = NULL;
	private $__mongo_conn = NULL;
	public function __construct($configFileName) {
		parent::__construct($configFileName);
		$cfg = \Core::config() -> getSessionConfig();
		$this -> config['lifetime'] = $cfg['lifetime'];
	}

	public function connect() {
		if (is_object($this -> __mongo_collection)) {
			return;
		}
		$connection_string = sprintf('mongodb://%s:%s', $this -> config['host'], $this -> config['port']);
		if ($this -> config['user'] != null && $this -> config['password'] != null) {
			$connection_string = sprintf('mongodb://%s:%s@%s:%s/%s', $this -> config['user'], $this -> config['password'], $this -> config['host'], $this -> config['port'], $this -> config['database']);
		}
		$opts = array('connect' => true);
		if ($this -> config['persistent'] && !empty($this -> config['persistentId'])) {
			$opts['persist'] = $this -> config['persistentId'];
		}
		if ($this -> config['replicaSet']) {
			$opts['replicaSet'] = $this -> config['replicaSet'];
		}
		$class = '\MongoClient';
		if (!class_exists($class)) {
			$class = '\Mongo';
		}
		$this -> __mongo_conn = $object_conn = new $class($connection_string, $opts);
		$object_mongo = $object_conn -> $this -> config['database'];
		$this -> __mongo_collection = $object_mongo -> $this -> config['collection'];
		if ($this -> __mongo_collection == NULL) {
			throw new \Xs_Exception_500('can not connect to mongodb server');
		}
	}

	public function init() {
		session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
	}

	public function open($session_path, $session_name) {
		$this -> connect();
		return true;
	}

	public function close() {
		$this -> __mongo_conn -> close();
		return true;
	}

	public function read($session_id) {
		$result = NULL;
		$ret = '';
		$expiry = time();
		$query['_id'] = $session_id;
		$query['expiry'] = array('$gte' => $expiry);
		$result = $this -> __mongo_collection -> findone($query);
		if ($result) {
			$this -> __current_session = $result;
			$result['expiry'] = time() + $this -> config['lifetime'];
			$this -> __mongo_collection -> update(array("_id" => $session_id), $result);
			$ret = $result['data'];
		}
		return $ret;
	}

	public function write($session_id, $data) {
		$result = true;
		$expiry = time() + $this -> config['lifetime'];
		$session_data = array();
		if (empty($this -> __current_session)) {
			$session_id = $session_id;
			$session_data['_id'] = $session_id;
			$session_data['data'] = $data;
			$session_data['expiry'] = $expiry;
		} else {
			$session_data = (array)$this -> __current_session;
			$session_data['data'] = $data;
			$session_data['expiry'] = $expiry;
		}
		$query['_id'] = $session_id;
		$record = $this -> __mongo_collection -> findOne($query);
		if ($record == null) {
			$this -> __mongo_collection -> insert($session_data);
		} else {
			$record['data'] = $data;
			$record['expiry'] = $expiry;
			$this -> __mongo_collection -> save($record);
		}
		return true;
	}

	public function destroy($session_id) {
		unset($_SESSION);
		$query['_id'] = $session_id;
		$this -> __mongo_collection -> remove($query);
		return true;
	}

	public function gc($max = 0) {
		$query = array();
		$query['expiry'] = array(':lt' => time());
		$this -> __mongo_collection -> remove($query, array('justOne' => false));
		return true;
	}

}

class Xs_Session_Mysql extends Xs_Session {
	protected $dbConnection;
	protected $dbTable;
	public function __construct($configFileName) {
		parent::__construct($configFileName);
		$cfg = \Core::config() -> getSessionConfig();
		$this -> config['lifetime'] = $cfg['lifetime'];
	}

	public function init() {
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
	}

	public function connect() {
		$this -> dbTable = $this -> config['table'];
		if ($this -> config['group']) {
			$this -> dbConnection = \Core::db($this -> config['group']);
		} else {
			$dbConfig = Data\Base::getDefaultConfig();
			$dbConfig['database'] = $this -> config['database'];
			$dbConfig['tablePrefix'] = $this -> config['table_prefix'];
			$dbConfig['masters']['master01']['hostname'] = $this -> config['hostname'];
			$dbConfig['masters']['master01']['port'] = $this -> config['port'];
			$dbConfig['masters']['master01']['username'] = $this -> config['username'];
			$dbConfig['masters']['master01']['password'] = $this -> config['password'];
			$this -> dbConnection = \Core::db($dbConfig);
		}
	}

	public function open($save_path, $session_name) {
		if (!is_object($this -> dbConnection)) {
			$this -> connect();
		}
		return TRUE;
	}

	public function close() {
		$this -> dbConnection -> close();
		return true;
	}

	public function read($id) {
		$result = $this -> dbConnection -> from($this -> dbTable) -> where(array('id' => $id)) -> execute();
		if ($result -> total()) {
			$record = $result -> row();
			$where['id'] = $id;
			$data['timestamp'] = time() + intval($this -> config['lifetime']);
			$this -> dbConnection -> update($this -> dbTable, $data, $where) -> execute();
			return $record['data'];
		} else {
			return false;
		}
		return true;
	}

	public function write($id, $sessionData) {
		$data['id'] = $id;
		$data['data'] = $sessionData;
		$data['timestamp'] = time() + intval($this -> config['lifetime']);
		$this -> dbConnection -> replace($this -> dbTable, $data);
		return $this -> dbConnection -> execute() > 0;
	}

	public function destroy($id) {
		unset($_SESSION);
		return $this -> dbConnection -> delete($this -> dbTable, array('id' => $id)) -> execute() > 0;
	}

	public function gc($max = 0) {
		return $this -> dbConnection -> delete($this -> dbTable, array('timestamp <' => time())) -> execute() > 0;
	}

}
?>