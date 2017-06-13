<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
namespace Lock;

use Closure;

/**
 * 分布式并发锁接口
 */
interface LockInterface
{

	/**
	 * 上锁
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function acquire($key);

	/**
	 * 解锁
	 * @param unknown $key
	 */
	public function release($key);

	/**
	 * 隔离
	 */
	public function granule($key, Closure $func);

	/**
	 * 清理过期的死锁
	 *
	 * @return integer 清理的死锁数量
	 */
	public function clear();
}
