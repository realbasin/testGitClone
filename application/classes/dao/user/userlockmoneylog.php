<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userlockmoneylog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//关联用户
				,'lock_money'//操作金额
				,'account_lock_money'//当前账户余额
				,'memo'//操作备注
				,'type'//0结存，1充值，2投标成功，8申请提现，9提现手续费，10借款管理费，18开户奖励，19流标还返
				,'create_time'//操作时间
				,'create_time_ymd'//操作时间 ymd
				,'create_time_ym'//操作时间 ym
				,'create_time_y'//操作时间 y
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_lock_money_log';
	}

}
