<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_dashboard extends controller_sysBase {
	public function do_index() {

	}
	
	public function do_locktest(){
		\Core::view()->load('test_locktest');
	}
	
	public function do_lockA(){
		//初始化换一个锁管理对象
		$lockManage=\Core::library("Lock_LockManager");
		//实例化一个具体的锁类型,不填写锁类型，则使用默认配置
		//$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('memcached');
		$lock=$lockManage->lockStore('redis');
		//$lock=$lockManage->lockStore('redis_cluster');
//		$lock=$lockManage->lockStore('file');//本地测试用file
		//锁采用闭包封装，注意传参 function()传参使用 use()
		$lock->granule('filekey',function(){
			sleep(8);
			echo('上锁完成，输出A');
		});
	}
	
	public function do_lockB(){
		//初始化换一个锁管理对象
		$lockManage=\Core::library("Lock_LockManager");
		//实例化一个具体的锁类型,不填写锁类型，则使用默认配置
		//$lock=$lockManage->lockStore();
		//$lock=$lockManage->lockStore('memcached');
		$lock=$lockManage->lockStore('redis');
		//$lock=$lockManage->lockStore('redis_cluster');
//		$lock=$lockManage->lockStore('file');//本地测试用file
		//锁采用闭包封装，注意传参 function()传参使用 use()
		$lock->granule('filekey',function(){
			sleep(1);
			echo('上锁完成，输出B');
		});
	}
}
?>