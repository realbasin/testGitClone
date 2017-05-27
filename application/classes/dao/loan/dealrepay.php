<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealrepay extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//借款ID
				,'user_id'//借款人
				,'repay_money'//还款金额
				,'manage_money'//管理费
				,'impose_money'//罚息
				,'repay_time'//还的是第几期的
				,'true_repay_time'//还款时间
				,'status'//0提前,1准时还款，2逾期还款 3严重逾期  前台在这基础上+1
				,'l_key'//还款顺序 0 开始
				,'has_repay'//0未还,1已还 2部分还款
				,'manage_impose_money'//逾期管理费
				,'is_site_bad'//是否坏账  0不是，1坏账 管理员看到的
				,'repay_date'//预期还款日期,日期格式方便统计
				,'true_repay_date'//实际还款日期,日期格式方便统计
				,'true_repay_money'//实还金额
				,'true_self_money'//实际还款本金
				,'interest_money'//待还利息   repay_money - self_money
				,'true_interest_money'//实际还利息
				,'true_manage_money'//实际管理费
				,'self_money'//需还本金
				,'loantype'//还款方式
				,'manage_money_rebate'//预计收到的：管理费返佣,满标放款时生成
				,'true_manage_money_rebate'//实际收到的：管理费返佣,每期还款时生成
				,'get_manage'//是否已收取管理费
				,'mortgage_fee'//抵押物管理费
				,'true_mortgage_fee'//抵押物管理费
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_repay';
	}
	//借款统计
	public function getStatBorrowAll($datestart=0,$dateend=0){
		$fields="sum(self_money) as suc_borrow_amount, 
		sum(if(has_repay = 0, repay_money,0)) as to_paid_amount,
		sum(if(has_repay = 0, self_money,0)) as to_paid_capital,
		sum(if(has_repay = 0, repay_money - self_money,0)) as to_paid_interest,
		sum(if(has_repay = 0, manage_money,0)) as to_paid_fee,
		sum(if(has_repay = 1, repay_money,0)) as paid_amount,
		sum(if(has_repay = 1, self_money,0)) as paid_capital,
		sum(if(has_repay = 1, repay_money - self_money,0)) as paid_interest,
		sum(if(has_repay = 1, manage_money,0)) as paid_fee,
		sum(if(has_repay = 1 and status = 0, impose_money,0)) as paid_fine,
		sum(if(has_repay = 1 and (status = 2 or status = 3), impose_money,0)) as paid_panalty";

		$this->getDb()->select($fields,false)->from($this->getTable());
		if($datestart && $dateend){
			$this->getDb()->where(array('true_repay_time >='=>$datestart,'true_repay_time <='=>$dateend));
		}
		$this->getDb()->cache(C('stat_sql_cache_time'),__METHOD__.$datestart.$dateend);
		return $this->getDb()->execute()->row();
	}
	/*
	 * 通过贷款id获取还款计划
	 * @loan_id 贷款id
	 * @field 查询字段
	 */
	public function getRepayPlan($where,$field) {
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->orderBy('l_key','asc')->execute()->rows();
	}
	//获取某一期贷款还款状态
	public function getRepayStstus($deal_id,$l_key){
		$where = array();
		$where['deal_id'] = $deal_id;
		$where['l_key'] = $l_key;
		return $this->getDb()->from($this->getTable())->where($where)->execute()->value('has_repay');
	}

	//获取逾期次数
	public function getOverdueTimes($userId) {
		return $this->getDb()->select('SUM(IF((has_repay = 0 AND (UNIX_TIMESTAMP(NOW()) - 8 * 3600 - (repay_time + 24 * 3600) > 0)) OR (true_repay_time - (repay_time + 24 * 3600) > 0),1,0)) > 0')->from($this->getTable())->where(array('user_id'=>$userId))->execute()->row();
	}
}
