<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_bankerStatistics extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//校园行长（标识ID）
				,'sta_date'//统计日期
				,'user_new'//新增用户
				,'user_borrow'//借款用户
				,'borrow_amount'//借款总额
				,'borrow_first'//首次借款总额
				,'borrow_more'//首次借款总额
				,'repay_amount'//还款总额
				,'repay_fisrt'//还款总额
				,'repay_more'//还款总额
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'banker_statistics';
	}
	
	//获取统计列表
	public function getStatList(){
		$select='';
		$where=array();
	}

}
