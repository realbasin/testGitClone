<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
namespace Lock;
/**
 * redis锁实例
 */
class RedisClusterStore extends GranuleStore implements LockInterface
{
	/**
	 *
	 * @var I_Cache_Redis_Cluster实例
	 */
	protected $redis;
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
	 * Create a new Redis store.
	 *
	 * @param  I_Cache_Redis  $redis
	 * @param  string  $prefix
	 * @return void
	 */
	public function __construct(\I_Cache_Redis_Cluster $redis, $timeout = 30, $max_timeout = 300, $retry_wait_usec = 100000,$prefix = 'lock')
	{
		$this->redis = $redis;
		$this->setPrefix($prefix);
		$this->timeout = $timeout;
		$this->max_timeout = $max_timeout;
		$this->retry_wait_usec = $retry_wait_usec;
	}

	/**
	 * 上锁
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function acquire($name)
	{
		$key = $this->getKey($name);
		$time = time();
		while (time() - $time < $this->max_timeout) {
			$lockValue = time() + $this->timeout;
			if ($this->redis->setnx($key, $lockValue)) {
				// 加锁成功,设置锁过期时间
				$this->redis->expire($key,$this->timeout);
				return true;
			}
			// 未能加锁成功
			// 检查当前锁是否已过期，并重新锁定
			// 这里有可能读超时，会返回NULL
			$data=$this->redis->get($key);
			if (!is_null($data) && $data < time()) {
				$oldValue=$this->redis->getSet($key, $lockValue);
				if($oldValue && $oldValue<time()){
					$this->redis->expire($key, $this->timeout);
					return true;
				}
			}
			usleep($this->retry_wait_usec);
		}
		return false;
	}

	/**
	 * 解锁
	 *
	 * @param string $key
	 */
	public function release($name)
	{
		$key = $this->getKey($name);

		if($this->redis->ttl($key)) {
			$this->redis->delete($key);
		}
	}

	/**
	 * 取得用于该锁的Key。
	 */
	protected function getKey($name)
	{
		return $this->prefix . $name;
	}

	/**
	 * 清理过期的死锁
	 *
	 * @return integer 清理的死锁数量
	 */
	public function clear()
	{
		return 0;
	}

	/**
	 * 获取redis实例
	 *
	 * @return I_Cache_Redis
	 */
	public function getRedis()
	{
		return $this->redis;
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
