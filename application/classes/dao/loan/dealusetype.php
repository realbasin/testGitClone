<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealusetype extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//name
				,'is_effect'//是否有效
				,'is_delete'//是否删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_use_type';
	}
	
	public function getAllDealUseType(){
		return $this->getDb()->select('*')->from($this->getTable())->where(array('is_effect'=>1,'is_delete'=>0))->execute()->key('id')->rows();
	}

}
