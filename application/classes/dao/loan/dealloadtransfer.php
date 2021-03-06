<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealloadtransfer extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//所投的标
				,'load_id'//债权ID
				,'user_id'//债权人ID
				,'transfer_amount'//转让价格
				,'load_money'//投标金额
				,'last_repay_time'//最后还款日期
				,'near_repay_time'//下次还款日
				,'transfer_number'//转让期数
				,'t_user_id'//承接人
				,'is_last'//是否最后债权
				,'is_auto'//是否自动承接
				,'transfer_time'//承接时间
				,'create_time'//发布时间
				,'status'//转让状态，0取消 1开始
				,'status2'//临时状态，二次转让状态
				,'next_dltid'//二次转让ID
				,'callback_count'//撤回次数
				,'lock_user_id'//锁定用户id,给用户支付时间,主要用于资金托管
				,'lock_time'//锁定时间,10分钟后,自动解锁;给用户支付时间,主要用于资金托管
				,'ips_status'//ips处理状态;0:未处理;1:已登记债权转让;2:已转让
				,'ips_bill_no'//IPS P2P订单号 否 由IPS系统生成的唯一流水号
				,'pMerBillNo'//商户订单号 商户系统唯一不重复
				,'create_date'//发布时间,日期格式,方便统计
				,'transfer_date'//承接时间,日期格式,方便统计
				,'bonus_user_id'//优惠券id
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_load_transfer';
	}
	
	//获取债权转让统计
	public function getStatTransferAll($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,count(*) as transfernum,sum(transfer_amount) as transfermoney")->from($this->getTable())->where(array('create_time >='=>$startDate,'create_time <='=>$endDate))->groupBy('createdate')->execute()->key('createdate')->rows();
	}

	//获取债权成功转让统计
	public function getStatTransferSuc($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(transfer_time,'%Y-%m-%d') as transferdate,count(*) as successnum,sum(transfer_amount) as successmoney")->from($this->getTable())->where(array('transfer_time >='=>$startDate,'transfer_time <='=>$endDate))->groupBy('transferdate')->execute()->key('transferdate')->rows();
	}
	//根据load_id 获取转让信息 add by zlz 201706061547
	public function getTransInfoByLoanId($load_id,$field="*"){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('load_id'=>$load_id))->execute()->row();
	}
	//根据load_id 获取转让信息 add by zlz 201706061547 end
}
