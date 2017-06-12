<?php
namespace Lock;

use Closure;

class LockManager
{
	protected static $stores = array();
	protected $config;
	

	public function __construct()
	{
		//正式部署后，去掉第2个参数或者设置为true
		$this->config=\Core::config('lock',false);
	}

	/**
	 * 获取一个锁实例
	 *
	 * @param  string  $name
	 * @return mixed
	 */
	public function store($name)
	{
		$name = $name ?  : $this->getDefaultDriver();
		return $this->stores[$name] = $this->get($name);
	}

	/**
	 * 尝试获取锁的实例
	 *
	 * @param  string  $name
	 * @return LockInterface
	 */
	protected function get($name)
	{
		return isset($this->stores[$name]) ? $this->stores[$name] : $this->resolve($name);
	}

	/**
	 * 实例化锁
	 *
	 * @param  string  $name
	 * @return LockInterface
	 *
	 */
	protected function resolve($name)
	{
		$config = $this->getConfig($name);
		$className=$config['class'];
		switch($className){
			case 'FileStore':
				break;
			case 'MemcachedStore':
				break;
			case 'RedisClusterStore':
				break;
			case 'RedisStore':
				break;
		}
	}

	/**
	 * 获取锁前缀
	 *
	 * @return string
	 */
	protected function getPrefix()
	{
		$prefix=\Core::arrayGet($this->config,'prefix');
		return $prefix?$prefix:'';
	}

	/**
	 * 获取锁过期时间
	 *
	 * @return string
	 */
	protected function getTimeout()
	{
		$timeout=\Core::arrayGet($this->config,'timeout');
		return ($timeout && is_numeric($timeout))?$timeout:30;
	}

	/**
	 * 获取上锁最大超时时间
	 *
	 * @return string
	 */
	protected function getMaxTimeout()
	{
		$maxTimeout=\Core::arrayGet($this->config,'max_timeout');
		return ($maxTimeout && is_numeric($maxTimeout))?$maxTimeout:300;
	}

	/**
	 * 获取锁重试等待时间（微秒）
	 *
	 * @return string
	 */
	protected function getRetryWaitUsec()
	{
		$retryWait=\Core::arrayGet($this->config,'retry_wait_usec');
		return ($retryWait && is_numeric($retryWait))?$retryWait:100000;
	}

	/**
	 * 获取指定锁的配置
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function getConfig($name)
	{
		$config=\Core::arrayGet($this->config['stores'],$name);
		if($config){
			return $config;
		}else{
			throw new \Xs_Exception_500("unknown lock config type {$name} !");
		}
	}

	/**
	 * 获取默认锁配置
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		$config=\Core::arrayGet($this->config,'default_lock');
		if($config){
			return $config;
		}else{
			throw new \Xs_Exception_500("can not find lock default lock config !");
		}
	}

	/**
	 * 设置默认锁配置
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultDriver($name)
	{
		$this->config['default_lock'] = $name;
	}

}
