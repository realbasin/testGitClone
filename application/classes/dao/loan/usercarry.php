<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_usercarry extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//提现人（标识ID）
				,'money'//提现金额
				,'fee'//手续费
				,'bank_id'//银行ID
				,'bankcard'//开好
				,'create_time'//提交日期
				,'status'//0待审核，1已付款，2未通过，3待付款
				,'transaction_id'//交易流水号
				,'update_time'//处理时间
				,'msg'//操作通知内容
				,'desc'//备注
				,'real_name'//姓名
				,'region_lv1'//国ID
				,'region_lv2'//省ID
				,'region_lv3'//市ID
				,'region_lv4'//区ID
				,'create_date'//记录提现提交日期，方便统计使用
				,'bankzone'//开户网点
				,'pingzheng'//打款凭证
				,'r_bank_name'//后台补充的开户行
				,'r_bankcard'//后台补充的银行卡号
				,'first_admin_id'//处理待审申请的经手人
				,'second_admin_id'//处理待付申请的经手人
				,'ip'//用户提现申请时的IP
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_carry';
	}
	
	//提现统计
	public function getStatWithdraw($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,sum(money) as moneytotal,sum(if(status=1,money,0)) as moneytotalsuc,count(*) as usertimes")->from($this->getTable())->where(array('create_time >='=>$startDate,'create_time <='=>$endDate))->groupBy('createdate')->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate)->execute()->rows();
	}

}
