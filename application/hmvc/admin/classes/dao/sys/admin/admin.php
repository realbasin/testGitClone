<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_admin_admin extends Dao {

	public function getColumns() {
		return array(
				'admin_id'//管理员ID
				,'admin_name'//管理员名称
				,'admin_avatar'//管理员头像
				,'admin_password'//管理员密码
				,'admin_login_time'//最后一次登录时间
				,'admin_login_num'//登录次数
				,'admin_is_super'//是否超级管理员
				,'admin_gid'//权限组ID
				,'admin_quick_link'//管理员常用操作
				);
	}

	public function getPrimaryKey() {
		return 'admin_id';
	}

	public function getTable() {
		return 'adminuser';
	}
	
	public function updateLastLogin($id){
		$this->getDb()->where(array('admin_id'=>$id))->set('admin_login_num','admin_login_num+1',false)->update($this->getTable(),array('admin_login_time'=>time()))->execute();
		//DEBUG
//		echo $this->getDb()->getSql();
//		Core::dump($this->getDb()->getSqlValues());
//		exit;
	}
	
	/*
	 * 获取管理员名称
	 * @userId number or array
	 * @return array
	 */
	public function getAdmin($adminId,$field){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('admin_id'=>$adminId))->execute()->key('admin_id')->rows();
	}

}
