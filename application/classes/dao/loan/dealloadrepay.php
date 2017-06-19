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
	
	//待收统计
	//默认缓存
	public function getDue($beginDate,$endDate){
		$where=array();
		$where['repay_time >=']=$beginDate;
		$where['repay_time <=']=$endDate;
		$where['has_repay']=0;
		return $this->getDb()->select("FROM_UNIXTIME(repay_time,'%Y-%m-%d') as repaydate,count(user_id) as usertotal,sum(repay_money + impose_money - manage_money) as investrepay,sum(self_money) as investcapital,sum(repay_money - self_money) as investinterest")->from($this->getTable())->where($where)->groupBy("FROM_UNIXTIME(repay_time,'%Y-%m-%d')")->cache(C('stat_sql_cache_time'),'stat_repay_due_total'.$beginDate.'_'.$endDate)->execute()->rows();
	}
	
	//待收明细
	public function getDueDetail(Array $ids){
		if($ids){
			$this->getDb()->where(array('t_user_id'=>0,'user_id'=>$ids),'((',')');
			$this->getDb()->where(array('t_user_id <>'=>0,'t_user_id'=>$ids),'OR (','))');
		}
		$this->getDb()->where(array('has_repay'=>0));
		$this->getDb()->select("IF(t_user_id=0,user_id,t_user_id) AS u_id,SUM(self_money) AS self_money")->from($this->getTable())->groupBy("IF(t_user_id=0,user_id,t_user_id)");
		return $this->getDb()->execute()->rows();
	}
	
	//已还款统计
	public function getStatHasPayment($beginDate,$endDate){
		$field="FROM_UNIXTIME(true_repay_time,'%Y-%m-%d') as payment_date,
		sum(repay_money) as	payment_amount,
		sum(self_money) as	payment_capital,
		sum(repay_money - self_money) as payment_interest,
		sum(if(status = 0, impose_money,0)) as payment_fine,
		sum(if(status = 2 or status = 3, impose_money,0)) as payment_penalty,
		sum(manage_money) as invest_fee,
		sum(repay_manage_money + repay_manage_impose_money) as loan_fee,
		sum(manage_money + repay_manage_money + repay_manage_impose_money) as platform_income,
		count(DISTINCT repay_id) as payment_number";
		$this->getDb()->select($field,false);
		$this->getDb()->from($this->getTable());
		$this->getDb()->where(array('true_repay_time >='=>$beginDate,'true_repay_time <='=>$endDate,'has_repay'=>1));
		$this->getDb()->groupBy('payment_date');
		$this->getDb()->orderBy('payment_date','asc');
		$this->getDb()->cache(C('stat_sql_cache_time'),__METHOD__.$beginDate.$endDate);
		return $this->getDb()->execute()->rows();
	}
	
	//待还款统计
	public function getStatNoPayment($beginDate,$endDate){
		$field="FROM_UNIXTIME(repay_time,'%Y-%m-%d') as nopayment_date,
		sum(repay_money) as	nopayment_amount,
		sum(self_money) as	nopayment_capital,
		sum(repay_money - self_money) as nopayment_interest,
		count(DISTINCT if(has_repay = 0, repay_id, null)) as nopayment_number";
		$this->getDb()->select($field,false);
		$this->getDb()->from($this->getTable());
		$this->getDb()->where(array('repay_time >='=>$beginDate,'repay_time <='=>$endDate,'has_repay'=>0));
		$this->getDb()->groupBy('repay_time');
		$this->getDb()->orderBy('nopayment_date','asc');
		$this->getDb()->cache(C('stat_sql_cache_time'),__METHOD__.$beginDate.$endDate);
		return $this->getDb()->execute()->rows();
	}
	
	public function getIsSiteRepay($where) {

		return $this->getDb()->from($this->getTable())->where($where)->execute()->value('is_site_repay');
	}
	public function getLkeys($id){
		return $this->getDb()->from($this->getTable())->where(array('deal_id'=>$id))->execute()->values('l_key');
	}
	//获取投资人谋期回款情况
	public function getLoadRepayByLkey($deal_id,$l_key,$fields='*'){
		$where = array();
		$where['deal_id'] = $deal_id;
		$where['l_key'] = $l_key;
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->rows();
	}
	//add by zlz 获取某人某个标某一期回款计划
	public function getSomeOneLkeyPlan($deal_id,$l_key,$user_id,$field='*'){
		$where = array();
		$where['deal_id'] = $deal_id;
		$where['l_key'] = $l_key;
		$where['user_id'] = $user_id;
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->execute()->row();
	}
	//add by zlz 获取某人某个标未回款计划
	public function getSomeOnePlanByLoanId($deal_id,$user_id,$field='*'){
		$where = array();
		$where['deal_id'] = $deal_id;
		$where['user_id'] = $user_id;
		$where['has_repay'] = 0;
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->execute()->key('l_key')->rows();
	}
	//某期已还款统计 add by zlz 201706081601
	public function getHasRepayTotal($deal_id,$l_key){
		$field="deal_id,l_key,sum(true_self_money) as	total_self_money,
		sum(true_interest_money) as	total_interest_money,
		sum(true_repay_money) as total_repay_money,
		sum(impose_money) as total_impose_money,
		sum(true_repay_manage_money) as total_repay_manage_money,
		sum(repay_manage_impose_money) as total_repay_manage_impose_money,
		sum(true_mortgage_fee) as total_mortgage_fee,
		is_site_repay";
		$this->getDb()->select($field,false);
		$this->getDb()->from($this->getTable());
		$this->getDb()->where(array('deal_id'=>$deal_id,'l_key'=>$l_key,'has_repay'=>1));
		return $this->getDb()->execute()->row();
	}
	//获取所有利息 add by zlz
	public function getAllInterest($deal_id,$l_key,$user_id){
		$field = 'sum(interest_money) as total_interest_money';
		$this->getDb()->select($field,false);
		$this->getDb()->from($this->getTable());
		$this->getDb()->where(array('deal_id'=>$deal_id,'l_key >='=>$l_key,'user_id'=>$user_id));
		return $this->getDb()->execute()->row();
	}
	//获取某个标未回款期数，用于判断某个标是否全部回款
	public function getNoRepayCountByDealId($deal_id){
		$where = array();
		$where['deal_id'] = $deal_id;
		$where['has_repay'] = 0;
		return $this->getCount($where);
	}
	
	//获取某个标已回款金额
	public function getAllReapyMoney($loan_id)
	{
		return $this->getDb()->select('sum(true_repay_money) as all_repay_money')->from($this->getTable())->where(array('deal_id' => $loan_id, 'has_repay' => 1))->execute()->value('all_repay_money');
	}
	
	//某期已代还款统计 add by zlz 201706081601
	public function getHasSiteRepayTotal($deal_id,$l_key){
		$field="repay_id,sum(true_self_money) as	total_self_money,
		sum(true_repay_money) as total_repay_money,
		sum(impose_money) as total_impose_money,
		sum(true_repay_manage_money) as total_manage_money,
		sum(repay_manage_impose_money) as total_repay_manage_impose_money,
		sum(true_mortgage_fee) as total_mortgage_fee";
		$this->getDb()->select($field,false);
		$this->getDb()->from($this->getTable());
		$this->getDb()->where(array('deal_id'=>$deal_id,'l_key'=>$l_key,'has_repay'=>1,'is_site_repay'=>1));
		return $this->getDb()->execute()->row();
	}
	//通过id获取数据
	public function getDataById($id,$field='*'){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('id'=>$id))->execute()->row();
	}
}
