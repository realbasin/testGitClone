<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealfundtype extends Dao {

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

	public function getAllDealFundType(){
		return $this->getDb()->select('*')->from($this->getTable())->where(array('is_effect'=>1))->execute()->key('fund_type')->rows();
	}

}
