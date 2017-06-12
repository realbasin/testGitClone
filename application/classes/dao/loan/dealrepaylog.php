<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealrepaylog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'repay_id'//账单ID
				,'log'//日志
				,'adm_id'//操作管理员
				,'user_id'//操作用户
				,'create_time'//操作时间
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_repay_log';
	}

}
