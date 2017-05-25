<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_deal_dealfundtype extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'fund_type'//资金源类型
				,'type_name'//type_name
				,'is_effect'//是否有效
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_fund_type';
	}

}
