<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
namespace Lock;

use Closure;
/**
 * 实现锁基本闭包虚类
 * 所有锁实现基于GranuleStore继承于LockInterface
 * php版本>=5.5
 */ 
abstract class GranuleStore{
	public function granule($key, Closure $callback)
	{
		try {
			if ($this->acquire($key)) {
				$callback();
			} else {
				throw new \Xs_Exception_500("Acquire lock key {$key} timeout!");
			}
		} finally {
			$this->release($key);
		}
	}
	
	public function synchronized($key, Closure $callback)
	{
		return $this->granule($key, $callback);
	}
}
