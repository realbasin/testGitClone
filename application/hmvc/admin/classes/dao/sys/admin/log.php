<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_admin_log extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'content'//操作内容
				,'operatetime'//时间
				,'operatetype'//操作类型，可能是login,loginout,add,delete,edit,update等类型
				,'admin_id'//管理员ID
				,'admin_name'//管理员名称
				,'ip'//IP
				,'link'//操作链接
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'admin_log';
	}

}
