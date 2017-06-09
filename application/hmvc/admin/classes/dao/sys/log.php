<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_log extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'log_info'//日志描述内容
				,'log_time'//发生时间
				,'log_admin'// 操作的管理员ID
				,'log_ip'//操作者IP
				,'log_status'//操作结果 1:操作成功 0:操作失败
				,'module'//操作的模块module
				,'action'//操作的命令action
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'log';
	}
	

}
