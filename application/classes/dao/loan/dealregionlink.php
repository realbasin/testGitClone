<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealregionlink extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//deal_id
				,'region_id'//region_id
				,'region_pid'//region_pid
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_region_link';
	}
	
	public function getRegionLink($fields,$where) {
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->row();
	}

}
