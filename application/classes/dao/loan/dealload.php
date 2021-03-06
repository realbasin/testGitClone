<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealload extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//借款ID
				,'user_id'//投标人ID
				,'user_name'//用户名
				,'money'//投标金额
				,'create_time'//投标时间
				,'is_repay'//流标是否已返还
				,'is_rebate'//是否已返利
				,'is_auto'//是否为自动投标 0:收到 1:自动
				,'pP2PBillNo'//IPS P2P订单号 否 由IPS系统生成的唯一流水号
				,'pContractNo'//合同号
				,'pMerBillNo'//登记债权人时提 交的订单号
				,'is_has_loans'//是否已经放款给招标人
				,'msg'//转账备注  转账失败的原因
				,'is_old_loan'//历史投标 0 不是  1 是 
				,'create_date'//记录投资日期,方便统计使用
				,'rebate_money'//返利金额
				,'is_winning'//是否中奖 0未中奖 1中奖
				,'income_type'//收益类型 1红包 2收益率 3积分 4礼品
				,'income_value'//收益值
				,'bid_score'//投标获得的积分
				,'ecv_id'//使用的红包的ID
				,'bonus_user_id'//优惠券id
				,'user_from'//投资来源，1：手机，0：pc
				,'income'//预期收益
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_load';
	}
	
	public function getLoads($deal_id,$field){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('deal_id'=>$deal_id))->execute()->rows();
	}
	
	//报表
	//获取奖励（折扣）总额
	//默认缓存
	public function getStatRebateTotal(){
		return $this->getDb()->select('SUM(rebate_money) as rebatetotal')->from($this->getTable())->where(array('is_has_loans'=>1,'is_rebate'=>1))->cache(C('stat_sql_cache_time'),'stat_load_rebate_total')->execute()->value('rebatetotal');
	}
	
	//报表
	//获取投资人数量已经投资金额
	//默认缓存
	public function getInvest($beginDate,$endDate){
		$where=array();
		$where['create_time >=']=$beginDate;
		$where['create_time <=']=$endDate;
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,count(DISTINCT user_id) as usertotal,sum(money) as moneytotal")->from($this->getTable())->where($where)->groupBy("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->cache(C('stat_sql_cache_time'),'stat_load_invest_total'.$beginDate.'_'.$endDate)->execute()->rows();
	}
	
	//获取成功投资比率
	//默认缓存
	public function getInvestAmount($beginDate,$endDate){
		$where=array();
		$where['create_time >=']=$beginDate;
		$where['create_time <=']=$endDate;
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,count(user_id) as usertotal,sum(if(is_has_loans = 1, money,0)) as sucinvest,sum(if(is_has_loans = 0 and is_repay = 0, money,0)) as frozeninvest,sum(if(is_has_loans = 0 and is_repay = 1, money,0)) as failinvest,sum(if(is_has_loans = 1, rebate_money,0)) as prizeinvest")->from($this->getTable())->where($where)->groupBy("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->cache(C('stat_sql_cache_time'),'stat_load_invest_amount_total'.$beginDate.'_'.$endDate)->execute()->rows();
	}
	
	//获取投资额比例统计
	//默认缓存
	public function getInvestProportion($beginDate,$endDate){
		$where=array();
		$where['create_time >=']=$beginDate;
		$where['create_time <=']=$endDate;
		$where['is_has_loans']=1;
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,count(user_id) as usertotal,sum(if(money < 5000, 1, 0)) as p1,sum(if(money >= 5000 and money < 10000, 1, 0)) as p2,sum(if(money >= 10000 and money < 50000, 1, 0)) as p3,sum(if(money >= 50000 and money < 100000, 1, 0)) as p4,sum(if(money >= 100000 and money < 200000, 1, 0)) as p5,sum(if(money >= 200000 and money < 500000, 1, 0)) as p6,sum(if(money >= 500000, 1, 0)) as p7",false)->from($this->getTable())->where($where)->groupBy("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->cache(C('stat_sql_cache_time'),'stat_load_invest_proportion_'.$beginDate.'_'.$endDate)->execute()->rows();
	}
	
	//获取总支出奖励
	public function getAllRebate($beginDate,$endDate){
		$fields="ifnull(sum(rebate_money),0) as rebate_all";
		$this->getDb()->select($fields)->from($this->getTable());
		$this->getDb()->where(array('is_rebate'=>1));
		if($beginDate && $endDate){
			$this->getDb()->where(array('create_time >='=>$beginDate,'create_time <='=>$endDate));
		}
		$this->getDb()->cache(C('stat_sql_cache_time'),__METHOD__.$beginDate.$endDate);
		return $this->getDb()->execute()->value('rebate_all');
	}
	//根据条件获取投资人列表 add by zlz 201705261759
	public function getList($where,$fields){
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->rows();
	}

	//获取某个标已投标总额
	public function getLoadMoneyByLoanId($loan_id) {
		return $this->getDb()->select('sum(money) as load_money')->from($this->getTale())->where(array('deal_id'=>$loan_id))->execute()->value('load_money');
	}

	//获取最后一次投标时间
	public function getLastBidTime($loan_id) {
		return $this->getDb()->select('max(create_time) as last_bid_time')->from($this->getTable())->where(array('deal_id'=>$loan_id))->execute()->value('last_big_time');
	}
	
	//获取投标数据
	public function getDealLoad($fields,$where) {
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->row();
	}


	
}
