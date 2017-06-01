<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_ecv extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'sn'//sn
				,'password'//password
				,'use_limit'//use_limit
				,'use_count'//use_count
				,'user_id'//user_id
				,'begin_time'//begin_time
				,'end_time'//end_time
				,'money'//money
				,'ecv_type_id'//ecv_type_id
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'ecv';
	}
	//通过id获取红包金额
	public function getMoneyById($id){
		return $this->getDb()->from($this->getTable())->where(array('id'=>$id))->execute()->value('money');
	}
}
