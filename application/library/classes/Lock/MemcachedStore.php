<?php
namespace Lock;
/**
 * Memcached锁实例
 */
class MemcachedStore extends GranuleStore implements LockInterface
{

	/**
	 * Memcached实例
	 *
	 * @var I_Cache_Memcached
	 */
	protected $memcached;

	/**
	 * 锁前缀
	 *
	 * @var string
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
	protected $Identifier;

	/**
	 * 初始化Memcached锁
	 *
	 * @param  I_Cache_Memcached  $memcached
	 * @param  string      $prefix
	 * @return void
	 */
	public function __construct(\I_Cache_Memcached $memcached, $prefix = '', $timeout = 30, $max_timeout = 300, $retry_wait_usec = 100000)
	{
		$this->setPrefix($prefix);
		$this->memcached = $memcached;
		$this->timeout = $timeout;
		$this->max_timeout = $max_timeout;
		$this->retry_wait_usec = $retry_wait_usec;
		$this->Identifier = md5(uniqid(gethostname(), true));
	}

	/**
	 * 上锁
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function acquire($key)
	{
		$key = $this->prefix . $key;
		$time = time();
		while (time() - $time < $this->max_timeout) {
			if ($this->memcached->add($key, $this->Identifier, $this->timeout)) {
				return true;
			}
			usleep($this->retry_wait_usec);
		}
		return false;
	}

	/**
	 * 解锁
	 * @param unknown $key
	 */
	public function release($key)
	{
		$key = $this->prefix . $key;
		if ($this->memcached->get($key) === $this->Identifier) {
			$this->memcached->delete($key);
		}
	}

	/**
	 * 清理过期的死锁
	 *
	 * @return integer 清理的死锁数量
	 */
	public function clear()
	{
		// 由Memcache自动管理。
		return 0;
	}

	/**
	 * 获取Memcached实例
	 *
	 * @return I_Cache_Memcached
	 */
	public function getMemcached()
	{
		return $this->memcached;
	}

	/**
	 * 获取锁前缀
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * 设置锁前缀
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = ! $prefix ? $prefix . ':' : '';
	}
}
