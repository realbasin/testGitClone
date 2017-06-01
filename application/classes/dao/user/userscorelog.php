<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userscorelog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//关联用户
				,'score'//操作积分
				,'account_score'//当前账户剩余积分
				,'memo'//操作备注
				,'type'//0结存，1充值，2投标成功，3招标成功，4偿还本息，5回收本息，6提前还款，13人工充值，14借款服务费，15出售债权，16购买债权，17债权转让管理费，18开户奖励，19流标还返，20投标管理费，21投标逾期收入，22兑换，23邀请返利，24投标返利，25签到成功 
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
		return 'user_score_log';
	}

}
