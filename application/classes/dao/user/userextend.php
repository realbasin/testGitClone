<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userextend extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'field_id'//扩展字段ID
				,'user_id'//会员ID
				,'value'//值
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_extend';
	}

}
