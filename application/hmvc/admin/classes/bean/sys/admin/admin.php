<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class bean_sys_admin_admin extends Bean {

	//管理员ID
	private $admin_id;

	//管理员名称
	private $admin_name;

	//管理员头像
	private $admin_avatar;

	//管理员密码
	private $admin_password;

	//最后一次登录时间
	private $admin_login_time;

	//登录次数
	private $admin_login_num;

	//是否超级管理员
	private $admin_is_super;

	//权限组ID
	private $admin_gid;

	//管理员常用操作
	private $admin_quick_link;

	public function getAdminId() {
		return $this->admin_id;
	}

	public function setAdminId($adminId) {
		$this->admin_id = $adminId;
		return $this;
	}

	public function getAdminName() {
		return $this->admin_name;
	}

	public function setAdminName($adminName) {
		$this->admin_name = $adminName;
		return $this;
	}

	public function getAdminAvatar() {
		return $this->admin_avatar;
	}

	public function setAdminAvatar($adminAvatar) {
		$this->admin_avatar = $adminAvatar;
		return $this;
	}

	public function getAdminPassword() {
		return $this->admin_password;
	}

	public function setAdminPassword($adminPassword) {
		$this->admin_password = $adminPassword;
		return $this;
	}

	public function getAdminLoginTime() {
		return $this->admin_login_time;
	}

	public function setAdminLoginTime($adminLoginTime) {
		$this->admin_login_time = $adminLoginTime;
		return $this;
	}

	public function getAdminLoginNum() {
		return $this->admin_login_num;
	}

	public function setAdminLoginNum($adminLoginNum) {
		$this->admin_login_num = $adminLoginNum;
		return $this;
	}

	public function getAdminIsSuper() {
		return $this->admin_is_super;
	}

	public function setAdminIsSuper($adminIsSuper) {
		$this->admin_is_super = $adminIsSuper;
		return $this;
	}

	public function getAdminGid() {
		return $this->admin_gid;
	}

	public function setAdminGid($adminGid) {
		$this->admin_gid = $adminGid;
		return $this;
	}

	public function getAdminQuickLink() {
		return $this->admin_quick_link;
	}

	public function setAdminQuickLink($adminQuickLink) {
		$this->admin_quick_link = $adminQuickLink;
		return $this;
	}

}