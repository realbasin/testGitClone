<?php
namespace Lock;
defined("IN_XIAOSHU") or exit("Access Invalid!");
/**
 * 文件锁实例
 */
class FileStore extends GranuleStore implements LockInterface {
	/**
	 * 文件锁文件夹
	 */
	protected $directory;
	/**
	 * 锁前缀
	 */
	protected $prefix;

	/**
	 * 锁超时时间（秒）
	 */
	protected $timeout;

	/**
	 * 上锁最大超时时间（秒）
	 */
	protected $max_timeout;

	/**
	 * 重试等待时间（微秒）
	 */
	protected $retry_wait_usec;

	/**
	 * 锁识别码
	 */
	protected $identifier;

	/**
	 * 锁的到期时间列表
	 */
	protected $expires_at = array();

	/**
	 * 创建一个文件锁实例
	 *
	 * @param  string  $directory
	 * @return void
	 */
	public function __construct($directory, $timeout = 30, $max_timeout = 300, $retry_wait_usec = 100000, $prefix = '') {
		$this -> directory = $directory;
		$this -> timeout = $timeout;
		$this -> max_timeout = $max_timeout;
		$this -> retry_wait_usec = $retry_wait_usec;
		$this -> prefix = $prefix?$prefix:'';
		$this -> identifier = md5(uniqid(gethostname(), true));
		if (!is_dir($this -> directory)) {
			mkdir($this -> directory, 0700, true);
		}
		if (!is_writable($this -> directory)) {
			throw new \Xs_Exception_500('lock dir [ ' . \Core::safePath($this -> directory) . ' ] not writable');
		}
	}

	/**
	 * 上锁
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function acquire($name) {
		$key = $this -> getKey($name);

		// 取得锁文件路径。
		$lockDir = $this -> _hashKeyPath($key);
		$file = $lockDir . $key;

		$time = time();
		while ((time() - $time) < $this -> max_timeout) {
			// 删除超时的锁文件。
			try {
				$current_value = $this -> get($name);
				if (!is_null($current_value) && $this -> hasLockValueExpired($current_value)) {
					$this -> delete($name);
				}
			} catch (Exception $e) {
			}

			// 文件锁不存在则创建
			if (!file_exists($file)) {
				// 创建文件锁目录。
				if (!is_dir($lockDir)) {
					//创建锁文件夹路径
					mkdir($lockDir, 0700, true);
				}
				// 创建锁文件。
				$value = $this -> getLockExpirationValue();
				if (file_put_contents($file, $value, LOCK_EX)) {
					// 记录锁到期时间
					$this -> acquired($name);
					return true;
				}
			}

			usleep($this -> retry_wait_usec);
		}
		return false;
	}

	/**
	 * 记录该锁的到期时间
	 */
	protected function acquired($name) {
		$this -> expires_at[$name] = time() + $this -> timeout;
	}

	/**
	 * 解锁
	 * @param unknown $key
	 */
	public function release($name) {
		if (!$this -> isLocked($name)) {
			throw new \Xs_Exception_500("Attempting to release a lock that is not held");
		}
		try {
			$value = $this -> get($name);
			unset($this -> expires_at[$name]);
			// 释放内存占用。
			if (!$this -> hasLockValueExpired($value)) {
				$this -> delete($name);
				// 释放锁。
			} else {
				trigger_error(sprintf('A FileLock was not released before the timeout. Class: %s Lock Name: %s', get_class($this), $name), E_USER_WARNING);
			}
		} catch (Exception $e) {
			trigger_error(sprintf('Attempting to release a lock that is not held. Class: %s Lock Name: %s', get_class($this), $name), E_USER_WARNING);
		}
	}

	/**
	 * 我们有一个锁？
	 */
	protected function isLocked($name) {
		return key_exists($name, $this -> expires_at);
	}

	/**
	 * 取得用于该锁的Key。
	 */
	protected function getKey($name) {
		return $this -> prefix . md5($name);
	}

	/**
	 * 获得文件锁hash文件夹路径
	 * @param string $key
	 */
	private function _hashKeyPath($key) {
		$key = md5($key);
		$len = strlen($key);
		return $this -> directory . $key{$len - 1} . '/' . $key{$len - 2} . '/' . $key{$len - 3} . '/';
	}

	/**
	 * 获得文件锁文件路径
	 * @param string $key
	 */
	private function get($name) {
		if (empty($name)) {
			return null;
		}
		$key = $this -> getKey($name);
		$filePath = $this -> _hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			return file_get_contents($filePath);
		}
		return NULL;
	}

	/**
	 * 删除文件锁
	 * @param string $key
	 */
	private function delete($name) {
		if (empty($name)) {
			return false;
		}
		$key = $this -> getKey($name);
		$filePath = $this -> _hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			return unlink($filePath);
		}
		return true;
	}

	private function readFileList($path, &$file_list, $ignore_dir = array()) {
		$path = rtrim($path, DIRECTORY_SEPARATOR);
		if (is_dir($path)) {
			$handle = @opendir($path);
			if ($handle) {
				while (false !== ($dir = readdir($handle))) {
					if ($dir != '.' && $dir != '..') {
						if (!in_array($dir, $ignore_dir)) {
							if (is_file($path . DIRECTORY_SEPARATOR . $dir)) {
								$file_list[] = $path . DIRECTORY_SEPARATOR . $dir;
							} elseif (is_dir($path . DIRECTORY_SEPARATOR . $dir)) {
								readFileList($path . DIRECTORY_SEPARATOR . $dir, $file_list, $ignore_dir);
							}
						}
					}
				}
				@closedir($handle);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * 取得锁的值。
	 * 添加到期时间与识别码。
	 *
	 * @return string
	 */
	protected function getLockExpirationValue() {
		return serialize(array('identifier' => $this -> identifier, 'expires_at' => time() + $this -> timeout));
	}

	/**
	 * 确定一个锁已过期。
	 *
	 * @param string 锁的值
	 * @return boolean
	 */
	protected function hasLockValueExpired($value) {
		$data = @unserialize($value);
		if (!$data) {
			return true;
		}
		return (time() > $data['expires_at']);
	}

	/**
	 * 清理过期的死锁
	 *
	 * @return integer 清理的死锁数量
	 */
	public function clear() {
		//获取锁文件夹下全部文件
		$files = array();
		//main.php中的方法
		$this -> readFileList($this -> directory, $files);
		$num = 0;
		foreach ($files as $file) {
			$value = file_get_contents($file);
			if ($this -> hasLockValueExpired($value)) {
				@unlink($file);
				$num++;
			}
		}
		return $num;
	}

	/**
	 * 获得文件锁储存文件夹
	 *
	 * @return string
	 */
	public function getDirectory() {
		return $this -> directory;
	}

	/**
	 * 获得文件锁前缀
	 *
	 * @return string
	 */
	public function getPrefix() {
		return $this -> prefix;
	}

}
