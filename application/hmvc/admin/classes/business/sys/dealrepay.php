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
	 * 还多少钱
	 */
	public function deal_repay_money($deal){
		//月还本息
		$return['month_repay_money'] = number_format($this->pl_it_formula($deal['borrow_amount'],$deal['rate']/12/100,$deal['repay_time']),2);
		//实际还多少钱
		$return['remain_repay_money'] = round($return['month_repay_money'] * $deal['repay_time'],2);
		//最后一期还款本息
		$return['last_month_repay_money'] = $return['remain_repay_money'] - round($return['month_repay_money'],2)*($deal['repay_time']-1);

		return $return;
	}
	//根据借款信息生成还款计划
	public function makeRepayPlan($loanBase,$loanBid,$loanExt){
		if(!$loanBase || !$loanBid)return false;
		$loan = array_merge($loanBase,$loanBid,$loanExt);
		$repaymoney = $this->deal_repay_money($loan);
		\Core::dump($repaymoney);die();
		$benjinmoney = $loan['borrow_amount'];
		$true_repay_time = $loan['repay_time'];
		$repay_day = $loan['repay_start_time'];
		$has_use_self_money = 0;
		for ($i=0;$i<$true_repay_time;$i++) {
			//还款时间
			$repay_time = $repay_day =  strtotime(date('Y-m-d',$repay_day).'+1 month');
			if($i+1 == $true_repay_time){
				//最后一期
				$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
				$load_repay['self_money'] = $loan['borrow_amount'] - $has_use_self_money;
			}
			else{
				$load_repay['repay_money'] = $repaymoney['month_repay_money'];
				$benjin = $repaymoney['month_repay_money'] - $benjinmoney * $loan['rate'] / 12 / 100;
				//$load_repay['self_money'] = $repaymoney['month_repay_money']  - $benjin;
				//$self_money = get_self_money($i,$loan['borrow_amount'],$loan['month_repay_money'],$loan['rate']);
				$benjinmoney -= $benjin;
				$has_use_self_money += $load_repay['self_money'];
			}
			$plan[] = $load_repay;
		}
		\Core::dump($plan);die();
	}
}