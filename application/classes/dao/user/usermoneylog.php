<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_usermoneylog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//关联用户
				,'user_name'//用户名
				,'money'//操作金额
				,'account_money'//当前账户余额
				,'transaction_id'//流水号
				,'transaction_status'//流水状态，1自身平台处理成功,2富友平台处理成功
				,'memo'//操作备注
				,'extra_data'//其他需要保存的数据
				,'type'//0结存，1充值，2投标成功，3招标成功，4偿还本息，5回收本息，6提前还款，7提前回收，8申请提现，9提现手续费，10借款管理费，11逾期罚息，12逾期管理费，13人工充值，14借款服务费，15出售债权，16购买债权，17债权转让管理费，18开户奖励，19流标还返，20投标管理费，21投标逾期收入，22兑换，23邀请返利，24投标返利，25签到成功，26逾期罚金（垫付后），27其他费用
				,'ip'//操作IP
				,'create_time'//操作时间
				,'create_time_ymd'//操作时间 ymd
				,'create_time_ym'//操作时间 ym
				,'create_time_y'//操作时间 y
				,'user_mark'//用户标记，用来区分借款和投资用户，0:未确定，1:借款用户，2:投资用户
				);
	}

	public function getPrimaryKey() {
		return 'user_id';
	}

	public function getTable() {
		return 'user_money_log';
	}

}
