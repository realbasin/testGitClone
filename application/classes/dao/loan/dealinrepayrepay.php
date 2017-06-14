<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealinrepayrepay extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//借款（标识性ID）
				,'user_id'//借款人（标识性ID）
				,'repay_money'//提前还款多少
				,'manage_money'//提前还款管理费
				,'mortgage_fee'//代换多少抵押物管理费
				,'impose'//提前还款罚息
				,'repay_time'//在哪一期提前还款
				,'true_repay_time'//还款时间
				,'self_money'//提前还款本金
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_inrepay_repay';
	}

}
