<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_dealrepay extends Business {
	public function business() {
		
	}
	/**
	 * 等额本息还款计算方式
	 * $money 贷款金额
	 * $rate 月利率
	 * $remoth 还几个月
	 * 返回  每月还款额
	 */
	public function pl_it_formula($money, $rate, $remoth)
	{
		if ($rate <= 0) return $money / $remoth;
		if ((pow(1 + $rate, $remoth) - 1) > 0)
			return $money * ($rate * pow(1 + $rate, $remoth) / (pow(1 + $rate, $remoth) - 1));
		else
			return 0;
	}
	/**
	 * 按月还款计算方式
	 * $total_money 贷款金额
	 * $rate 年化利率
	 * 返回月应该还多少利息
	 */
	function av_it_formula($total_money, $rate)
	{
		return $total_money * $rate;
	}
	/**
	 * 获取该期剩余本金
	 * int $Idx  第几期
	 * int $all_idx 总的是几期
	 * floatval $amount_money 总的借款多少
	 * floatval $month_repay_money 月还本息
	 * floatval $rate 费率
	 */
	function get_benjin($idx, $all_idx, $amount_money, $month_repay_money, $rate)
	{
		//计算剩多少本金
		$benjin = $amount_money;
		for ($i = 1; $i < $idx + 1; $i++) {
			$benjin = $benjin - ($month_repay_money - $benjin * $rate / 12 / 100);
		}
		return $benjin;
	}
	/**
	 * 获取该期本金
	 * int $Idx  第几期
	 * floatval $amount_money 总的借款多少
	 * floatval $month_repay_money 月还本息
	 * floatval $rate 费率
	 */
	function get_self_money($idx, $amount_money, $month_repay_money, $rate)
	{
		return $month_repay_money - $this->get_benjin($idx, $idx, $amount_money, $month_repay_money, $rate) * $rate / 12 / 100;
	}
	/**
	 * 还多少钱
	 */
	public function deal_repay_money($deal){
		//到期本息
		if($deal['loantype'] == 2){
			if($deal['repay_time_type'] == 0){
				$deal['rate'] = $deal['rate']/30;
			}
			//月还本息
			$return['month_repay_money'] = $deal['borrow_amount'] * $deal['rate']/12/100 * $deal['repay_time'];
			//实际还多少钱
			$return['remain_repay_money'] = $deal['borrow_amount'] + $return['month_repay_money'] ;
			//最后一期还款本息
			$return['last_month_repay_money'] = $return['remain_repay_money'];
			//是否最后一期才算罚息
			$return['is_check_impose'] = true;
		}else {
			//等额本息
			if($deal['loantype'] == 0 ) {
				//月还本息
				$return['month_repay_money'] = number_format($this->pl_it_formula($deal['borrow_amount'],$deal['rate']/12/100,$deal['repay_time']),2);
				//实际还多少钱
				$return['remain_repay_money'] = round($return['month_repay_money'] * $deal['repay_time'],2);
			}
			//付息还本
			if($deal['loantype'] == 1 ) {
				//月还本息
				$return['month_repay_money'] = number_format($this->av_it_formula($deal['borrow_amount'],$deal['rate']/12/100),2);
				//实际还多少钱
				$return['remain_repay_money'] = round($deal['borrow_amount'] + $return['month_repay_money'] * $deal['repay_time'],2);
			}
			//等额本金
			if($deal['loantype'] == 3) {
				//月还本息
				$return['month_repay_money'] = number_format(($deal['borrow_amount']/$deal['repay_time']) + $this->av_it_formula($deal['borrow_amount'],$deal['rate']/12/100));
				//实际还多少钱
				$return['remain_repay_money'] = $deal['borrow_amount'] + $this->av_it_formula($deal['borrow_amount'],$deal['rate']/12/100)*$deal['repay_time'];
			}
			//最后一期还款本息
			$return['last_month_repay_money'] = $return['remain_repay_money'] - round($return['month_repay_money'],2)*($deal['repay_time']-1);
			$return['is_check_impose'] = false;
		}
		return $return;
	}
	//根据借款信息生成还款计划
	public function makeRepayPlan($loanBase,$loanBid,$loanExt,$loan_time){
		if(!$loanBase || !$loanBid || !$loanExt)return false;
		$loan = array_merge($loanBase,$loanBid,$loanExt);
		$repaymoney = $this->deal_repay_money($loan);
		$true_repay_time = $loan['repay_time'];
		$repay_day = $loan_time;
		$has_use_self_money = 0;
		$dealRepayDao = \Core::dao('loan_dealrepay');
		$dealLoadRepayBusiness = \Core::business('sys_dealloadrepay');
		$total_money  = array();
		for ($i=0;$i<$true_repay_time;$i++) {
			//还款时间
			$load_repay['repay_time'] = $repay_day = strtotime(date('Y-m-d', $repay_day) . '+1 month');
			//TODO 根据不同还款方式，生成不同计划
			if($loan['loantype'] == 2 && $i + 1 != $true_repay_time) {
				continue;
			}
			if ($i + 1 == $true_repay_time) {
				//最后一期 $loan['loantype'] 0 等额本息 1付息还本 2到期本息
				if($loan['loantype'] == 0) {
					$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount'] - $has_use_self_money;
				}
				if($loan['loantype'] == 1) {
					$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount'];
				}
				if($loan['loantype'] == 2) {
					$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount'] ;
					//管理费
					//$load_repay['manage_money'] = $deal['all_manage_money'];
				}
				if($loan['loantype'] == 3) {
					$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount'] - round($loan['borrow_amount']/$true_repay_time,2)*($true_repay_time -1);
				}
			} else {
				if($loan['loantype'] == 0) {
					$load_repay['repay_money'] = $repaymoney['month_repay_money'];
					$load_repay['self_money'] = number_format($this->get_self_money($i, $loan['borrow_amount'], $repaymoney['month_repay_money'], $loan['rate']), 2);
					$has_use_self_money += $load_repay['self_money'];
				}
				if($loan['loantype'] == 1) {
					$load_repay['repay_money'] = $repaymoney['month_repay_money'];
					$load_repay['self_money'] = 0;
				}
				if($loan['loantype'] == 2) {
					$load_repay['repay_money'] = 0;
					$load_repay['self_money'] = 0;
					//管理费
					//$load_repay['manage_money'] = 0;
				}
				if($loan['loantype'] == 3) {
					$load_repay['repay_money'] = $repaymoney['month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount']/$true_repay_time;
				}
			}
			//管理费率，从配置字段config_common中获取
			$config_common = unserialize($loan['config_common']);
			$manage_fee = \Core::arrayKeyExists('manage_fee',$config_common)?\Core::arrayGet($config_common,'manage_fee'):0;
			//管理费
			if($loan['loantype'] == 2) {
				if($i + 1 == $true_repay_time) {
					$load_repay['manage_money'] = number_format($loan['borrow_amount'] * floatval($manage_fee)/ 100, 2) * $true_repay_time;
				}else {
					$load_repay['manage_money'] = 0;
				}
			}else {
				$load_repay['manage_money'] = number_format($loan['borrow_amount'] * floatval($manage_fee)/ 100, 2);
			}
			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			$load_repay['deal_id'] = $loan['loan_id'];
			$load_repay['user_id'] = $loan['user_id'];
			//借款者 授权服务机构获取的管理费抽成
			$rebate = C('BORROWER_COMMISSION_RATIO');
			$load_repay['manage_money_rebate'] = $load_repay['manage_money']* floatval($rebate)/100;
			//还款类型
			$load_repay['loantype'] = $loan['loantype'];
			//判断是否存在该期还款计划
			if ($repayInfo = $dealRepayDao->getOneRepayPlan($loan['loan_id'], $i)) {
				$repay_id = $repayInfo['id'];
				if ($repayInfo['has_repay'] == 0) {
					//未还款
					$load_repay['l_key'] = $i;
					$load_repay['status'] = 0;
					//更新数据
					$dealRepayDao->update($load_repay,array('deal_id' => $loan['loan_id'], 'l_key'=>$i));
				} else {
					$load_has_repay = array();
					$load_has_repay = $load_repay;
					unset($load_has_repay['self_money']);
					unset($load_has_repay['repay_money']);
					unset($load_has_repay['manage_money']);
					unset($load_has_repay['manage_money_rebate']);
					$dealRepayDao->update($load_has_repay,array('deal_id' => $loan['loan_id'], 'l_key'=>$i));
					unset($load_has_repay);
				}
			} else {
				if($loan['loantype'] == 2 ) {
					$load_repay['l_key'] = 0;
				}else{
					$load_repay['l_key'] = $i;
				}
				$load_repay['status'] = 0;
				$load_repay['has_repay'] = 0;
				$repay_id = $dealRepayDao->insert($load_repay);
			}
			$repay_plan[] = $load_repay;
			$load_repay_plan = $dealLoadRepayBusiness->makeLoadRepayPlan($loan,$load_repay, $i, $repay_id,$total_money);
			if($load_repay_plan === false){
				return false;
			}
		}
		$plan['repay_plan'] = $repay_plan;
		$plan['load_repay_plan'] = $load_repay_plan;
		return $plan;
	}
	//还款计划是否逾期，返回贷款状态、逾期天数、罚息、罚息管理费
	public function repayPlanImpose($deal_id,$l_key,$time=0){
		//借款用户还款计划
		$user_repay = \Core::dao('loan_dealrepay')->getOneRepayPlan($deal_id,$l_key,'*');
		//判断是否逾期
		//获取普通配置中的罚息利率等配置 loan_ext表的config_common字段
		$config_common = \Core::dao('loan_loanext')->getCommonconfig($deal_id);
		if ($config_common ){
			unserialize($config_common);
		}
		//判断是否罚息(**到期还本息到最后一期才算罚息**)
		if($time == 0){
			$time = time();
		}
		$result = array();
		$result['status'] = 0;
		$result['overday'] = 0;
		if(($user_repay['has_repay'] == 0) && ($time > ($user_repay['repay_time'] + 24 * 3600 - 1)) && ($user_repay['repay_money'] > 0)) {
			//逾期未还
			$result['status'] = 2;
			//计算逾期时间,设置还款状态
			$result['overday'] = ceil((strtotime(date('Y-m-d',$time)) - $user_repay['repay_time']) / (3600 * 24));
			//根据日期判断是否严重逾期 获取费率
			//费率修改为拓展表loan_ext中普通配置字段config_common中获取
			if ($result['overday'] >= C('YZ_IMPSE_DAY')) {
				$result['status'] = 3;
				$impose_fee = \Core::arrayKeyExists('impose_fee_day2',$config_common)?\Core::arrayGet($config_common,'impose_fee_day2'):0.9;
				$manage_impose_fee = \Core::arrayKeyExists('manage_impose_fee_day2',$config_common)?\Core::arrayGet($config_common,'manage_impose_fee_day2'):9;
			}else {
				$impose_fee = \Core::arrayKeyExists('impose_fee_day1',$config_common)?\Core::arrayGet($config_common,'impose_fee_day1'):1;
				$manage_impose_fee = \Core::arrayKeyExists('manage_impose_fee_day1',$config_common)?\Core::arrayGet($config_common,'manage_impose_fee_day1'):10;
			}
			$impose_fee = floatval($impose_fee);
			$manage_impose_fee = floatval($manage_impose_fee);
			//罚息
			$result['impose_money'] = number_format($user_repay['repay_money'] * $impose_fee * $result['overday'] / 100,2);
			//罚管理费
			$result['manage_impose_money'] = number_format($user_repay['repay_money'] * $manage_impose_fee * $result['overday'] / 100,2);
			$result['need_repay_money']  = $user_repay['repay_money'] + $result['impose_money'] + $result['manage_impose_money'];

		}elseif(($user_repay['has_repay'] == 0) && ($time < ($user_repay['repay_time'] + 24 * 3600 - 1)) && ($user_repay['repay_money'] > 0)){
			//未逾期未还
			$result['status'] = 0;
			$result['overday'] = 0;
			$result['impose_money'] = 0;
			$result['manage_impose_money'] = 0;
			$result['need_repay_money']  = $user_repay['repay_money'];
		}elseif ($user_repay['has_repay'] == 1) {
			//已还所有
			$result['status'] = $user_repay['status'];
			$result['overday'] = ceil((strtotime(date('Y-m-d',$user_repay['true_repay_time'])) - $user_repay['repay_time']) / (3600 * 24));
			$result['impose_money'] = $user_repay['impose_money'];
			$result['manage_impose_money'] = $user_repay['manage_impose_money'];
			$result['need_repay_money']  = 0;
		}elseif($user_repay['has_repay'] == 2 && $time > ($user_repay['repay_time'] + 24 * 3600 - 1) && $user_repay['repay_money'] > 0) {
			//部分还款逾期
			$result['status'] = 2;
			//计算逾期时间,设置还款状态
			$result['overday'] = ceil((strtotime(date('Y-m-d',$time)) - $user_repay['repay_time']) / (3600 * 24));
			//根据日期判断是否严重逾期 获取费率
			//费率修改为拓展表loan_ext中普通配置字段config_common中获取
			if ($result['overday'] >= C('YZ_IMPSE_DAY')) {
				$result['status'] = 3;
				$impose_fee = \Core::arrayKeyExists('impose_fee_day2',$config_common)?\Core::arrayGet($config_common,'impose_fee_day2'):0.9;
				$manage_impose_fee = \Core::arrayKeyExists('manage_impose_fee_day2',$config_common)?\Core::arrayGet($config_common,'manage_impose_fee_day2'):9;
			}else {
				$impose_fee = \Core::arrayKeyExists('impose_fee_day1',$config_common)?\Core::arrayGet($config_common,'impose_fee_day1'):1;
				$manage_impose_fee = \Core::arrayKeyExists('manage_impose_fee_day1',$config_common)?\Core::arrayGet($config_common,'manage_impose_fee_day1'):10;
			}
			$impose_fee = floatval($impose_fee);
			$manage_impose_fee = floatval($manage_impose_fee);
			//已还金额
			$has_repay_money = \Core::dao('loan_dealloadrepay')->getHasRepayTotal($deal_id,$l_key);
			//还需还金额
			$repay_money = $user_repay['repay_money'] - $has_repay_money['total_repay_money'];
			//罚息
			$result['impose_money'] = number_format($repay_money * $impose_fee * $result['overday'] / 100,2);
			//罚管理费
			$result['manage_impose_money'] = number_format($repay_money * $manage_impose_fee * $result['overday'] / 100,2);
			$user_repay['repay_money'] = $repay_money;
			$result['need_repay_money']  = $user_repay['repay_money'] + $result['impose_money'] + $result['manage_impose_money'];
		}else {
			$result['status'] = 0;
			$result['overday'] = 0;
			$result['impose_money'] = 0;
			$result['manage_impose_money'] = 0;
			$result['need_repay_money'] = $user_repay['repay_money'];
		}

		return $result;
	}
	//更新还款计划
	public function updateRepayPlan($hasRepayTotal,$impose_money,$manage_impose_money,$getManage=0,$status=0){
		$repay_update_data = array();
		$repay_update_data['has_repay'] = 1;
		$repay_update_data['true_repay_time'] = time();
		//$repay_update_data['true_repay_date'] = to_date(TIME_UTC);
		$repay_update_data['true_repay_money'] = floatval($hasRepayTotal['total_repay_money']);
		$repay_update_data['true_self_money'] = floatval($hasRepayTotal['total_self_money']);
		$repay_update_data['true_interest_money'] = floatval($hasRepayTotal['total_interest_money']);
		if ($hasRepayTotal['is_site_repay'] == 0) {
			$repay_update_data['impose_money'] = floatval($hasRepayTotal['total_impose_money']);
		}else {
			$repay_update_data['impose_money'] = floatval($impose_money);
		}
		if ($getManage == 0) {
			$repay_update_data['true_manage_money'] = floatval($hasRepayTotal['total_repay_manage_money']);
		}
		$repay_update_data['true_mortgage_fee'] = floatval($hasRepayTotal['total_mortgage_fee']);
		if ($hasRepayTotal['is_site_repay'] == 0) {
			$repay_update_data['manage_impose_money'] = floatval($hasRepayTotal['total_repay_manage_impose_money']);
		} else {
			$repay_update_data['manage_impose_money'] = floatval($manage_impose_money);
		}
		$repay_update_data['true_manage_money_rebate'] = floatval($hasRepayTotal['total_repay_manage_money']) * floatval(C('BORROWER_COMMISSION_RATIO')) / 100;
		$repay_update_data['status'] = $status;
		return \Core::dao('loan_dealrepay')->update($repay_update_data,array('deal_id'=>$hasRepayTotal['deal_id'],'l_key'=>$hasRepayTotal['l_key']));
	}
	//提前还款
	public function inrepayRepay($loan,$start_lkey){
		if(!$loan) {
			return false;
		}
		$loanExtDao = \Core::dao('loan_loanext');
		$dealRepayDao = \Core::dao('loan_dealrepay');
		//要还多少
		$repay_money = $this->deal_repay_money($loan);
		//本金
		$benjin = $this->get_benjin($start_lkey,$loan['repay_time'],$loan['borrow_amount'],$repay_money['month_repay_money'],$loan['rate']);
		//贷款普通配置，提前还款费率
		$loan_config = $loanExtDao->getCommonconfig($loan['id']);
		if(!$loan_config) {
			return false;
		}
		$loan_config = unserialize($loan_config);
		$compensate_fee = \Core::arrayKeyExists('compensate_fee',$loan_config)?\Core::arrayGet($loan_config,'compensate_fee'):0;
		$mortgage_fee = \Core::arrayKeyExists('mortgage_fee',$loan_config)?\Core::arrayGet($loan_config,'mortgage_fee'):0;
		$manage_fee = \Core::arrayKeyExists('manage_fee',$loan_config)?\Core::arrayGet($loan_config,'manage_fee'):0;
		$return["impose_money"] = round($benjin * floatval($compensate_fee)*0.01, 2);
		$return["true_self_money"] = $benjin;

		//$o_repay_loans = $GLOBALS['db']->getAll("SELECT id,user_id,deal_id,l_key,repay_money,self_money,interest_money FROM ".DB_PREFIX."deal_repay WHERE deal_id=".$loaninfo['deal']['id']." ORDER BY l_key ASC");
		$o_repay_loans = $dealRepayDao->getAllRepayLoan($loan['id'],'id,user_id,deal_id,l_key,repay_money,self_money,interest_money');
		if(!$o_repay_loans) {
			return false;
		}
		$o_total_repay = 0.00;
		$return["true_repay_money"] = $benjin;
		$return["true_manage_money"] = 0;
		for($k=$start_lkey ;$k < $loan['repay_time']; $k++){//剩余利息
			$benjin = round($this->get_benjin($k,$loan['repay_time'],$loan['borrow_amount'],$repay_money['month_repay_money'],$loan['rate']),2);
			$return['true_repay_money'] += $benjin*$loan['rate']*0.01/12;
			$o_total_repay += $o_repay_loans[$k]['repay_money'];
		}
		$return['true_repay_money'] = round($return['true_repay_money'],2);
		$return["true_manage_money"] = round($return['true_manage_money'], 2);
		$return["true_mortgage_fee"] = round($mortgage_fee, 2);
		$return["true_manage_money_rebate"] = round($return["true_manage_money"] * floatval(C('INVESTORS_COMMISSION_RATIO'))/100, 2);
		$return["true_manage_money"] = round($loan['borrow_amount']*$manage_fee/100,2);
		//利息管理费
		$return["true_manage_interest_money"] = 0;
		//$return["true_manage_interest_money_rebate"] = round($loaninfo['deal']['manage_interest_money_rebate'], 2);

		return $return;
	}
	//根据还款方式判断是否最后才一期还款
	public function isLastRepay($loantype) {
		if($loantype == 2) {
			return true;
		} else {
			return false;
		}
	}

}