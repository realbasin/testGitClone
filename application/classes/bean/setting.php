<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class bean_setting extends Bean {

	//变量名称
	private $name;

	//变量值
	private $value;

	//是否系统变量
	private $sys;

	//描述
	private $info;

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function getSys() {
		return $this->sys;
	}

	public function setSys($sys) {
		$this->sys = $sys;
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