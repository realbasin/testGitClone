<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userPointLog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//关联用户
				,'point'//操作信用积分
				,'account_point'//当前账户信用积分
				,'memo'//操作备注
				,'type'//0结存，4偿还本息，5回收本息，6提前还款，7提前回收，8申请认证，11逾期还款，13人工充值，14借款服务费，18开户奖励，22兑换，23邀请返利，24投标返利，25签到成功
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
		return 'user_point_log';
	}
	//根据条件统计积分
	public function getSumPoint($where){
		if(!is_array($where)) return false;
		$field = 'sum(point) as total_point';
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->execute()->value('total_point');
	}
	//根据条件获取最新时间
	public function getMaxTime($where){
		if(!is_array($where)) return false;
		$field = 'max(create_time) as max_time';
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->execute()->value('max_time');
	}
}
