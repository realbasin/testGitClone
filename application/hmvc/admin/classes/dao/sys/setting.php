<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_setting extends Dao {

	public function getColumns() {
		return array('name', 'value', 'sys', 'info');
	}

	public function getPrimaryKey() {
		return 'name';
	}

	public function getTable() {
		return 'setting';
	}

	public function getSettings() {
		return $this -> getDb() -> select('name,value') -> from($this -> getTable()) -> execute() -> key('name') -> rows();
	}

}
