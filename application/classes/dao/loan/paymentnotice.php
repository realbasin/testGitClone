<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_paymentnotice extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'notice_sn'//支付单号(商户订单ID)
				,'create_time'//下单时间
				,'pay_time'//付款时间
				,'order_id'//关联的订单号ID
				,'is_paid'//是否已支付
				,'user_id'//会员ID
				,'deal_id'//投标ID
				,'load_id'//债权ID
				,'transfer_id'//债权转让ID
				,'payment_id'//支付接口ID
				,'type'//0充值,1预授权,2撤销预授权,3划拨(个人与个人),4划拨(商户与个人),5冻结,6解除冻结
				,'memo'//付款单备注
				,'money'//应付金额
				,'outer_notice_sn'//第三方支付平台的对帐号
				,'pay_date'//收款日期
				,'create_date'//记录充值下单时间,方便统计使用
				,'bank_id'//直联银行编号
				,'fee_amount'//收用户手续费
				,'pay_fee_amount'//平台付支付公司手续费
				,'debit_type'//白条还款类型  1：正常还款，2：提前还款
				,'ip'//用户在线充值或提交下线充值单时的IP地址
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'payment_notice';
	}
	
	//获取充值统计
	public function getStatRecharge($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(pay_time,'%Y-%m-%d') as paydate,sum(if(is_paid=1,money,0)) as paytotal")->from($this->getTable())->where(array('pay_time >='=>$startDate,'pay_time <='=>$endDate))->groupBy('paydate')->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate)->execute()->rows();
	}

}
