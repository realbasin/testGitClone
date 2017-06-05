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
			}
			//付息还本
			if($deal['loantype'] == 1 ) {
				//月还本息
				$return['month_repay_money'] = number_format($this->av_it_formula($deal['borrow_amount'],$deal['rate']/12/100));
			}
			//实际还多少钱
			$return['remain_repay_money'] = round($return['month_repay_money'] * $deal['repay_time'],2);
			//最后一期还款本息
			$return['last_month_repay_money'] = $return['remain_repay_money'] - round($return['month_repay_money'],2)*($deal['repay_time']-1);
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
		for ($i=0;$i<$true_repay_time;$i++) {
			//还款时间
			$load_repay['repay_time'] = $repay_day = strtotime(date('Y-m-d', $repay_day) . '+1 month');
			//TODO 根据不同还款方式，生成不同计划
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

			}
			//管理费率，从配置字段config_common中获取
			$config_common = unserialize($loan['config_common']);
			$manage_fee = \Core::arrayKeyExists('manage_fee',$config_common)?\Core::arrayGet('manage_fee',$config_common):0;
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
				$load_repay['l_key'] = $i;
				$load_repay['status'] = 0;
				$load_repay['has_repay'] = 0;
				$repay_id = $dealRepayDao->insert($load_repay);
			}
			$repay_plan[] = $load_repay;
			$load_repay_plan = $dealLoadRepayBusiness->makeLoadRepayPlan($loan,$load_repay, $repay_id);
			if($load_repay_plan === false){
				return false;
			}
		}
		$plan['repay_plan'] = $repay_plan;
		$plan['load_repay_plan'] = $load_repay_plan;
		return $plan;
	}
}