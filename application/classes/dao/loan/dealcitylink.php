<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealcitylink extends Dao {

	public function getColumns() {
		return array(
				'deal_id'//deal_id
				,'city_id'//city_id
				);
	}

	public function getPrimaryKey() {
		return '';
	}

	public function getTable() {
		return 'deal_city_link';
	}

}
