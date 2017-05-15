<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class bean_sys_admin_log extends Bean {

	//id
	private $id;

	//操作内容
	private $content;

	//时间
	private $operatetime;

	//操作类型，可能是login,loginout,add,delete,edit,update等类型
	private $operatetype;

	//管理员ID
	private $admin_id;

	//管理员名称
	private $admin_name;

	//IP
	private $ip;

	//操作链接
	private $link;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function getOperatetime() {
		return $this->operatetime;
	}

	public function setOperatetime($operatetime) {
		$this->operatetime = $operatetime;
		return $this;
	}

	public function getOperatetype() {
		return $this->operatetype;
	}

	public function setOperatetype($operatetype) {
		$this->operatetype = $operatetype;
		return $this;
	}

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

	public function getIp() {
		return $this->ip;
	}

	public function setIp($ip) {
		$this->ip = $ip;
		return $this;
	}

	public function getLink() {
		return $this->link;
	}

	public function setLink($link) {
		$this->link = $link;
		return $this;
	}

}