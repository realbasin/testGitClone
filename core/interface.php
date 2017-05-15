<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
interface Xs_Logger_Writer {
	public function write(Xs_Exception $exception);
}

interface Xs_Request {
	public function getPathInfo();
	public function getQueryString();
}

//路由重写
interface Xs_Uri_Rewriter {
	public function rewrite($uri);
}

interface Xs_Exception_Handle {
	public function handle(Xs_Exception $exception);
}

interface Xs_Maintain_Handle {
	public function handle();
}

interface Xs_Database_SlowQuery_Handle {
	public function handle($sql, $explainString, $time);
}

interface Xs_Database_Index_Handle {
	public function handle($sql, $explainString, $time);
}

interface Xs_Cache {
	public function set($key, $value, $cacheTime = 0);
	public function get($key);
	public function delete($key);
	public function clean();
	public function & instance($key = null, $isRead = true);
	public function reset();
}

class I_Request_Default implements Xs_Request {
	private $pathInfo, $queryString;
	public function __construct() {
		$this -> pathInfo = \Core::arrayGet($_SERVER, 'PATH_INFO', \Core::arrayGet($_SERVER, 'REDIRECT_PATH_INFO'));
		$this -> queryString = \Core::arrayGet($_SERVER, 'QUERY_STRING', '');
	}

	public function getPathInfo() {
		return $this -> pathInfo;
	}

	public function getQueryString() {
		return $this -> queryString;
	}

	public function setPathInfo($pathInfo) {
		$this -> pathInfo = $pathInfo;
		return $this;
	}

	public function setQueryString($queryString) {
		$this -> queryString = $queryString;
		return $this;
	}

}

/*
 * 日志接口实现
 * */
class I_Logger_FileWriter implements Xs_Logger_Writer {
	private $logsDirPath, $log404;
	public function __construct($logsDirPath, $log404 = true) {
		$this -> log404 = $log404;
		$this -> logsDirPath = \Core::realPath($logsDirPath) . '/' . date(\Core::config() -> getLogsSubDirNameFormat()) . '/';
	}

	public function write(Xs_Exception $exception) {
		if (!$this -> log404 && ($exception instanceof Xs_Exception_404)) {
			return;
		}
		$content = 'Domain : ' . \Core::server('http_host') . "\n" . 'ClientIP : ' . \Core::server('SERVER_ADDR') . "\n" . 'ServerIP : ' . \Core::serverIp() . "\n" . 'ServerHostname : ' . \Core::hostname() . "\n" . (!\Core::isCli() ? 'Request Uri : ' . \Core::server('request_uri') : '') . "\n" . (!\Core::isCli() ? 'Get Data : ' . json_encode(\Core::get()) : '') . "\n" . (!\Core::isCli() ? 'Post Data : ' . json_encode(\Core::post()) : '') . "\n" . (!\Core::isCli() ? 'Cookie Data : ' . json_encode(\Core::cookie()) : '') . "\n" . (!\Core::isCli() ? 'Server Data : ' . json_encode(\Core::server()) : '') . "\n" . $exception -> renderCli() . "\n";
		if (!is_dir($this -> logsDirPath)) {
			mkdir($this -> logsDirPath, 0700, true);
		}
		if (!file_exists($logsFilePath = $this -> logsDirPath . 'logs.php')) {
			$content = '<?php defined("IN_XIAOSHU") or exit("Access Invalid!");?>' . "\n" . $content;
		}
		file_put_contents($logsFilePath, $content, LOCK_EX | FILE_APPEND);
	}

}

class I_Maintain_Handle_Default implements Xs_Maintain_Handle {
	public function handle() {
		if (!\Core::isCli()) {
			header('Content-type: text/html;charset=utf-8');
		}
		echo '<center><h2>server is under maintenance</h2><h3>服务器维护中</h3>' . date('Y/m/d H:i:s e') . '</center>';
	}

}

/*
 * 重写
 * */
class I_Uri_Rewriter_Default implements Xs_Uri_Rewriter {
	public function rewrite($uri) {
		return $uri;
	}

}

/*
 * 默认异常处理
 * */
class I_Exception_Handle_Default implements Xs_Exception_Handle {
	public function handle(Xs_Exception $exception) {
		$exception -> render();
	}

}

/*
 * 慢速查询异常
 * */
class I_Database_SlowQuery_Handle_Default implements Xs_Database_SlowQuery_Handle {
	public function handle($sql, $explainString, $time) {
		$dir = \Core::config() -> getStorageDirPath() . 'slow-query-debug/';
		$file = $dir . 'slow-query-debug.php';
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}
		$content = "\nSQL : " . $sql . "\nExplain : " . $explainString . "\nUsingTime : " . $time . " ms" . "\nTime : " . date('Y-m-d H:i:s') . "\n";
		if (!file_exists($file)) {
			$content = '<?php defined("IN_XIAOSHU") or exit("Access Invalid!");?>' . "\n" . $content;
		}
		file_put_contents($file, $content, LOCK_EX | FILE_APPEND);
	}

}

/*
 * 数据库异常
 * */
class I_Database_Index_Handle_Default implements Xs_Database_Index_Handle {
	public function handle($sql, $explainString, $time) {
		$dir = \Core::config() -> getStorageDirPath() . 'index-debug/';
		$file = $dir . 'index-debug.php';
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}
		$content = "\nSQL : " . $sql . "\nExplain : " . $explainString . "\nUsingTime : " . $time . " ms" . "\nTime : " . date('Y-m-d H:i:s') . "\n";
		if (!file_exists($file)) {
			$content = '<?php defined("IN_XIAOSHU") or exit("Access Invalid!");?>' . "\n" . $content;
		}
		file_put_contents($file, $content, LOCK_EX | FILE_APPEND);
	}

}

/*
 * 文件缓存实现
 * */
class I_Cache_File implements Xs_Cache {
	private $_cacheDirPath;
	public function __construct($cacheDirPath = '') {
		$cacheDirPath = empty($cacheDirPath) ? \Core::config() -> getStorageDirPath() . 'cache/' : $cacheDirPath;
		$this -> _cacheDirPath = \Core::realPath($cacheDirPath) . '/';
		if (!is_dir($this -> _cacheDirPath)) {
			mkdir($this -> _cacheDirPath, 0700, true);
		}
		if (!is_writable($this -> _cacheDirPath)) {
			throw new \Xs_Exception_500('cache dir [ ' . \Core::safePath($this -> _cacheDirPath) . ' ] not writable');
		}
	}

	private function _hashKey($key) {
		return md5($key);
	}

	private function _hashKeyPath($key) {
		$key = md5($key);
		$len = strlen($key);
		return $this -> _cacheDirPath . $key{$len - 1} . '/' . $key{$len - 2} . '/' . $key{$len - 3} . '/';
	}

	private function pack($userData, $cacheTime) {
		$cacheTime = (int)$cacheTime;
		return @serialize(array('userData' => $userData, 'expireTime' => ($cacheTime == 0 ? 0 : time() + $cacheTime)));
	}

	private function unpack($cacheData) {
		$cacheData = @unserialize($cacheData);
		if (is_array($cacheData) && \Core::arrayKeyExists('userData', $cacheData) && \Core::arrayKeyExists('expireTime', $cacheData)) {
			if ($cacheData['expireTime'] == 0) {
				return $cacheData['userData'];
			}
			return $cacheData['expireTime'] > time() ? $cacheData['userData'] : NULL;
		} else {
			return NULL;
		}
	}

	public function clean() {
		return \Core::rmdir($this -> _cacheDirPath, false);
	}

	public function delete($key) {
		if (empty($key)) {
			return false;
		}
		$key = $this -> _hashKey($key);
		$filePath = $this -> _hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			return @unlink($filePath);
		}
		return true;
	}

	public function get($key) {
		if (empty($key)) {
			return null;
		}
		$key = $this -> _hashKey($key);
		$filePath = $this -> _hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			$cacheData = file_get_contents($filePath);
			$userData = $this -> unpack($cacheData);
			return is_null($userData) ? null : $userData;
		}
		return NULL;
	}

	public function set($key, $value, $cacheTime = 0) {
		if (empty($key)) {
			return false;
		}
		$key = $this -> _hashKey($key);
		$cacheDir = $this -> _hashKeyPath($key);
		$filePath = $cacheDir . $key;
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir, 0700, true);
		}
		$cacheData = $this -> pack($value, $cacheTime);
		if (empty($cacheData)) {
			return false;
		}
		return file_put_contents($filePath, $cacheData, LOCK_EX);
	}

	public function & instance($key = null, $isRead = true) {
		return $this;
	}

	public function reset() {
		return $this;
	}

}

/*
 * Memcached实现
 * */
class I_Cache_Memcached implements Xs_Cache {
	private $config, $handle;
	public function __construct($config) {
		$this -> config = $config;
	}

	private function _init() {
		if (empty($this -> handle)) {
			$this -> handle = new \Memcached();
			foreach ($this->config as $server) {
				if ($server[2] > 0) {
					$this -> handle -> addServer($server[0], $server[1], $server[2]);
				} else {
					$this -> handle -> addServer($server[0], $server[1]);
				}
			}
		}
	}

	public function clean() {
		$this -> _init();
		return $this -> handle -> flush();
	}

	public function delete($key) {
		$this -> _init();
		return $this -> handle -> delete($key);
	}

	public function get($key) {
		$this -> _init();
		return ($data = $this -> handle -> get($key)) ? $data : null;
	}

	public function set($key, $value, $cacheTime = 0) {
		$this -> _init();
		return $this -> handle -> set($key, $value, $cacheTime > 0 ? (time() + $cacheTime) : 0);
	}

	public function & instance($key = null, $isRead = true) {
		$this -> _init();
		return $this -> handle;
	}

	public function reset() {
		$this -> handle = null;
		return $this;
	}

}

/*
 * Memcache实现
 * */
class I_Cache_Memcache implements Xs_Cache {
	private $config, $handle;
	public function __construct($config) {
		$this -> config = $config;
	}

	private function _init() {
		if (empty($this -> handle)) {
			$this -> handle = new \Memcache();
			foreach ($this->config as $server) {
				$this -> handle -> addserver($server[0], $server[1]);
			}
		}
	}

	public function clean() {
		$this -> _init();
		return $this -> handle -> flush();
	}

	public function delete($key) {
		$this -> _init();
		return $this -> handle -> delete($key);
	}

	public function get($key) {
		$this -> _init();
		return ($data = $this -> handle -> get($key)) ? $data : null;
	}

	public function set($key, $value, $cacheTime = 0) {
		$this -> _init();
		return $this -> handle -> set($key, $value, false, $cacheTime);
	}

	public function & instance($key = null, $isRead = true) {
		$this -> _init();
		return $this -> handle;
	}

	public function reset() {
		$this -> handle = null;
		return $this;
	}

}

/*
 * APC实现
 * */
class I_Cache_Apc implements Xs_Cache {
	public function clean() {
		@apc_clear_cache();
		@apc_clear_cache("user");
		return true;
	}

	public function delete($key) {
		return apc_delete($key);
	}

	public function get($key) {
		$data = apc_fetch($key, $bo);
		if ($bo === false) {
			return null;
		}
		return $data;
	}

	public function set($key, $value, $cacheTime = 0) {
		return apc_store($key, $value, $cacheTime);
	}

	public function & instance($key = null, $isRead = true) {
		return $this;
	}

	public function reset() {
		return $this;
	}

}

/*
 * Redis实现
 * */
class I_Cache_Redis implements Xs_Cache {
	private $config, $servers;
	public function __construct($config) {
		foreach ($config as $key => $node) {
			if (empty($node['slaves']) && !empty($node['master'])) {
				$config[$key]['slaves'][] = $node['master'];
			}
		}
		$this -> config = $config;
	}

	private function & selectNode($key, $isRead) {
		$nodeIndex = sprintf("%u", crc32($key)) % count($this -> config);
		if ($isRead) {
			$slaveIndex = array_rand($this -> config[$nodeIndex]['slaves']);
			$serverKey = $nodeIndex . '-slaves-' . $slaveIndex;
			$config = $this -> config[$nodeIndex]['slaves'][$slaveIndex];
		} else {
			$serverKey = $nodeIndex . '-master';
			$config = $this -> config[$nodeIndex]['master'];
		}
		if (empty($this -> servers[$serverKey])) {
			$this -> servers[$serverKey] = $this -> connect($config);
		}
		return $this -> servers[$serverKey];
	}

	private function & connect($config) {
		$redis = new \Redis();
		if ($config['type'] == 'sock') {
			$redis -> connect($config['sock']);
		} else {
			$redis -> connect($config['host'], $config['port'], $config['timeout'], $config['retry']);
		}
		if (!is_null($config['password'])) {
			$redis -> auth($config['password']);
		}
		if (!is_null($config['prefix'])) {
			if ($config['prefix']{strlen($config['prefix']) - 1} != ':') {
				$config['prefix'] .= ':';
			}
			$redis -> setOption(\Redis::OPT_PREFIX, $config['prefix']);
		}
		$redis -> select($config['db']);
		return $redis;
	}

	public function reset() {
		$this -> servers = array();
		return $this;
	}

	public function clean() {
		$status = true;
		foreach ($this->config as $nodeIndex => $config) {
			$redis = $this -> connect($config['master']);
			$status = $status && $redis -> flushDB();
		}
		return $status;
	}

	public function delete($key) {
		$redis = $this -> selectNode($key, false);
		return $redis -> delete($key);
	}

	public function get($key) {
		$redis = $this -> selectNode($key, true);
		if ($rawData = $redis -> get($key)) {
			$data = @unserialize($rawData);
			return $data ? $data : $rawData;
		} else {
			return null;
		}
	}

	public function set($key, $value, $cacheTime = 0) {
		$redis = $this -> selectNode($key, false);
		$value = serialize($value);
		if ($cacheTime) {
			return $redis -> setex($key, $cacheTime, $value);
		} else {
			return $redis -> set($key, $value);
		}
	}

	public function & instance($key = null, $isRead = true) {
		return $this -> selectNode($key, $isRead);
	}

}

/*
 * Redis集群实现
 * */
class I_Cache_Redis_Cluster implements Xs_Cache {
	private $config, $handle;
	public function __construct($config) {
		if (!is_null($config['prefix']) && ($config['prefix']{strlen($config['prefix']) - 1} != ':')) {
			$config['prefix'] .= ':';
		}
		$this -> config = $config;
	}

	private function _init() {
		if (empty($this -> handle)) {
			$this -> handle = new \RedisCluster(null, $this -> config['hosts'], $this -> config['timeout'], $this -> config['read_timeout'], $this -> config['persistent']);
			if ($this -> config['prefix']) {
				$this -> handle -> setOption(\RedisCluster::OPT_PREFIX, $this -> config['prefix']);
			}
		}
	}

	public function reset() {
		$this -> handle = null;
		return $this;
	}

	public function clean() {
		throw new \Xs_Exception_500('clean method not supported of Xs_Cache_Redis_Cluster ');
	}

	public function delete($key) {
		$this -> _init();
		return $this -> handle -> del($key);
	}

	public function get($key) {
		$this -> _init();
		if ($rawData = $this -> handle -> get($key)) {
			$data = @unserialize($rawData);
			return $data ? $data : $rawData;
		} else {
			return null;
		}
	}

	public function set($key, $value, $cacheTime = 0) {
		$this -> _init();
		$value = serialize($value);
		if ($cacheTime) {
			return $this -> handle -> setex($key, $cacheTime, $value);
		} else {
			return $this -> handle -> set($key, $value);
		}
	}

	public function & instance($key = null, $isRead = true) {
		$this -> _init();
		return $this -> handle;
	}

}
?>