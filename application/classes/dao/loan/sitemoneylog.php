<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_sitemoneylog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//关联用户
				,'money'//操作金额
				,'memo'//操作备注
				,'type'//7提前回收，9提现手续费，10借款管理费，12逾期管理费，13人工充值，14借款服务费，17债权转让管理费，18开户奖励，20投标管理费，22兑换，23邀请返利，24投标返利，25签到成功，26逾期罚金（垫付后），27其他费用
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
		return 'site_money_log';
	}
	
	//获取债券转让管理费
	public function getStatTransferFee($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as logdate,sum(money) as transferfeemoney")->from($this->getTable())->where(array('create_time >='=>$startDate,'create_time <='=>$endDate,'type'=>17))->groupBy('logdate')->execute()->key('logdate')->rows();
	}

}
