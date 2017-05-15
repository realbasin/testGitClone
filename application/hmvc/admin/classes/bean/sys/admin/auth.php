<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class bean_sys_admin_auth extends Bean {

	//自增id
	private $gid;

	//组名
	private $gname;

	//权限内容
	private $permission;

	//说明
	private $info;

	public function getGid() {
		return $this->gid;
	}

	public function setGid($gid) {
		$this->gid = $gid;
		return $this;
	}

	public function getGname() {
		return $this->gname;
	}

	public function setGname($gname) {
		$this->gname = $gname;
		return $this;
	}

	public function getPermission() {
		return $this->permission;
	}

	public function setPermission($permission) {
		$this->permission = $permission;
		return $this;
	}

	public function getInfo() {
		return $this->info;
	}

	public function setInfo($info) {
		$this->info = $info;
		return $this;
	}

}