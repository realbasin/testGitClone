<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_admin_auth extends Dao {

	public function getColumns() {
		return array(
				'gid'//自增id
				,'gname'//组名
				,'permission'//权限内容
				,'info'//说明
				);
	}

	public function getPrimaryKey() {
		return 'gid';
	}

	public function getTable() {
		return 'admin_auth';
	}
	
	public function getBygid($gid){
		return $this->getDb()->select('*')->from($this->getTable())->where(array('gid'=>$gid))->execute()->object('sys_admin_auth');
	}

}
