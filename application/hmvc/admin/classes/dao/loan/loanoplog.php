<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_loanoplog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//借款ID
				,'user_id'//借款用户
				,'op_id'//操作ID
				,'op_name'//审核操作阶段
				,'op_result'//操作结果
				,'log'//日志内容
				,'admin_id'//操作人员
				,'create_time'//操作时间
				,'ip'//操作ip
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_op_log';
	}

}
