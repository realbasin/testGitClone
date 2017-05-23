<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealloadrepay extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//借款（标识ID）
				,'user_id'//投标人（标识ID）
				,'self_money'//本金
				,'repay_money'//还款金额
				,'manage_money'//管理费
				,'impose_money'//罚息
				,'repay_time'//预计回款时间
				,'repay_date'//预计回款时间,方便统计
				,'true_repay_time'//实际回款时间
				,'true_repay_date'//实际回款时间,方便统计使用
				,'true_repay_money'//真实还款本息
				,'true_self_money'//真实还款本金
				,'interest_money'//利息   repay_money - self_money
				,'true_interest_money'//实际利息
				,'true_manage_money'//实际管理费
				,'true_repay_manage_money'//true_repay_manage_money
				,'status'//0提前，1准时，2逾期，3严重逾期 前台在这基础上+1
				,'is_site_repay'//0自付，1网站垫付 2担保机构垫付
				,'l_key'//还的是第几期
				,'u_key'//还的是第几个投标人
				,'repay_id'//还款计划ID
				,'load_id'//投标记录ID
				,'has_repay'//0未收到还款，1已收到还款
				,'t_user_id'//承接着会员ID
				,'repay_manage_money'//从借款者均摊下来的管理费
				,'repay_manage_impose_money'//借款者均摊下来的逾期管理费
				,'loantype'//还款方式
				,'manage_interest_money'//预计能收到：利息管理费,是在满标放款时生成
				,'true_manage_interest_money'//实际收到：利息管理费,是在还款时生成
				,'manage_interest_money_rebate'//预计返佣金额(返给授权机构)
				,'true_manage_interest_money_rebate'//实际返佣金额(返给授权机构)
				,'manage_early_interest_money'//提前还款利息管理费(扣除投资人的)
				,'true_manage_early_interest_money'//实际提前还款利息管理费(扣除投资人的)
				,'t_pMerBillNo'//ips债权转让后新的ips流水号
				,'reward_money'//预计奖励收益
				,'true_reward_money'//实际奖励收益
				,'mortgage_fee'//抵押物管理费
				,'true_mortgage_fee'//抵押物管理费
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_load_repay';
	}

	//借出汇总
	//缓存10分钟
	public function getStatLoanAll($begin_time,$end_time){
		$where=array();
		if($begin_time>0){
			$where['true_repay_time >=']=$begin_time;
		}
		if($end_time>0){
			$where['true_repay_time <=']=$end_time;
		}
		$total=$this->getDb()->select('count(DISTINCT user_id) as investor,sum(self_money) as investmoney,sum(if(has_repay = 0, repay_money,0)) as due,sum(if(has_repay = 0, self_money,0)) as capital,sum(if(has_repay = 0, (repay_money - self_money),0)) as interest,sum(if(has_repay = 1, repay_money,0)) as receivetatal,sum(if(has_repay = 1, self_money,0)) as receivecapital,sum(if(has_repay = 1, (repay_money - self_money),0)) as receiveinterest,sum(if(has_repay = 1 and status = 0, impose_money,0)) as receivefine,sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as receivepanalty')->from($this->getTable())->where($where)->cache(C('stat_sql_cache_time'),'stat_loan_all_'.$begin_time.'_'.$end_time)->execute()->row();
		return $total;
	}

	//回款汇总
	//默认缓存
	public function getPayment($beginDate,$endDate){
		$where=array();
		$where['true_repay_time >=']=$beginDate;
		$where['true_repay_time <=']=$endDate;
		$where['has_repay']=1;
		return $this->getDb()->select("FROM_UNIXTIME(true_repay_time,'%Y-%m-%d') as repaydate,count(user_id) as usertotal,sum(repay_money + impose_money - manage_money) as investrepay,sum(self_money) as investcapital,sum(repay_money - self_money) as investinterest,sum(if(status = 0, impose_money,0)) as repaypenalty,sum(if(status = 2 or status = 3, impose_money,0)) as repayfine,sum(manage_money) as investfee,sum(repay_manage_money + repay_manage_impose_money) as loanfee,sum(manage_money + repay_manage_money + repay_manage_impose_money) as platfromincome")->from($this->getTable())->where($where)->groupBy("FROM_UNIXTIME(true_repay_time,'%Y-%m-%d')")->cache(C('stat_sql_cache_time'),'stat_repay_payment_total'.$beginDate.'_'.$endDate)->execute()->rows();
	}
}
