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
		//月还本息
		$return['month_repay_money'] = number_format($this->pl_it_formula($deal['borrow_amount'],$deal['rate']/12/100,$deal['repay_time']),2);
		//实际还多少钱
		$return['remain_repay_money'] = round($return['month_repay_money'] * $deal['repay_time'],2);
		//最后一期还款本息
		$return['last_month_repay_money'] = $return['remain_repay_money'] - round($return['month_repay_money'],2)*($deal['repay_time']-1);

		return $return;
	}
	//根据借款信息生成还款计划
	public function makeRepayPlan($loanBase,$loanBid,$loanExt,$loan_time){
		if(!$loanBase || !$loanBid)return false;
		$loan = array_merge($loanBase,$loanBid,$loanExt);
		$repaymoney = $this->deal_repay_money($loan);
		$true_repay_time = $loan['repay_time'];
		$repay_day = $loan_time;
		$has_use_self_money = 0;
		$dealRepayDao = \Core::dao('loan_dealrepay');
		for ($i=0;$i<$true_repay_time;$i++) {
			//还款时间
			$load_repay['repay_time'] = $repay_day = strtotime(date('Y-m-d', $repay_day) . '+1 month');
			if ($i + 1 == $true_repay_time) {
				//最后一期
				$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
				$load_repay['self_money'] = $loan['borrow_amount'] - $has_use_self_money;
			} else {
				$load_repay['repay_money'] = $repaymoney['month_repay_money'];
				$load_repay['self_money'] = number_format($this->get_self_money($i, $loan['borrow_amount'], $repaymoney['month_repay_money'], $loan['rate']), 2);
				$has_use_self_money += $load_repay['self_money'];
			}
			//TODO 服务费率，从配置字段config_common中获取
			$server_fee = 6.5;
			$load_repay['manage_money'] = number_format($loan['borrow_amount'] * $server_fee/ 100, 2);
			$load_repay['interest_money'] = $load_repay['repay_money'] - $load_repay['self_money'];
			$load_repay['deal_id'] = $loan['id'];
			$load_repay['user_id'] = $loan['user_id'];
			//判断是否存在该期还款计划
			if ($repayInfo = $dealRepayDao->getOneRepayPlan($loan['id'], $i)) {
				$repay_id = $repayInfo['id'];
				if ($repayInfo['has_repay'] == 0) {
					//未还款
					$load_repay['l_key'] = $i;
					$load_repay['status'] = 0;
					//更新数据
					$dealRepayDao->update($load_repay,array('deal_id' => $loan['id'], 'l_key'=>$i));
				} else {
					$load_has_repay = array();
					$load_has_repay = $load_repay;
					unset($load_has_repay['self_money']);
					unset($load_has_repay['repay_money']);
					unset($load_has_repay['manage_money']);
					unset($load_has_repay['manage_money_rebate']);
					$dealRepayDao->update($load_has_repay,array('deal_id' => $loan['id'], 'l_key'=>$i));
				}
			} else {
				$load_repay['l_key'] = $i;
				$load_repay['status'] = 0;
				$load_repay['has_repay'] = 0;
				$repay_id = $dealRepayDao->insert($load_repay);
			}
			$this->make_user_repay_plan($loan,$load_repay, $repay_id);
		}
		return true;
	}
	/**
	 * 生成投标者的回款计划
	 */
	public function make_user_repay_plan($loan,$load_repay, $repay_id){
		//查询投标人id 每个人生成一条回款信息
		$load_users = \Core::dao('loan_dealload')->getList(array('deal_id'=>$loan['id']),'id,user_id,money,is_winning,income_type,income_value');

		if(!$load_users){
			//投资者不存在
			return false;
		}
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		foreach ($load_users as $k=>$v){
			$load_repay['user_id'] = $v['user_id'];
			$load_repay['repay_id'] = $repay_id;
			$load_repay['load_id'] = $v['id'];
			$load_repay['has_repay'] = 0;
			$load_repay['t_user_id'] = 0;
			if($k+1 == count($load_users)){
				$load_repay['repay_manage_money'] = $load_repay['manage_money'] - round($load_repay['manage_money'] / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
				$load_repay['mortgage_fee'] = $loan['mortgage_fee'] - round($loan['mortgage_fee'] / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
			}
			else{
				$load_repay['repay_manage_money'] = $load_repay['manage_money']/ $loan['buy_count'];
				$load_repay['mortgage_fee'] = $loan['mortgage_fee'] / $loan['buy_count'];
			}

			$load_repay['interest_money'] =  $load_repay['repay_money'] - $load_repay['self_money'];
			//TODO 投资者管理费率 从config_common字段中获取
			$user_loan_manage_fee = 0;
			$load_repay['manage_money'] = $v['money']* floatval($user_loan_manage_fee)/100;
			if($v['is_winning']==1 && (int)$v['income_type']==2 && (float)$v['income_value']!=0){
				$load_repay['reward_money'] = $load_repay['interest_money'] * (float)$v['income_value'] * 0.01;
			}
			//判断是否存在该期回款计划
			$is_plan = $dealLoadRepayDao->getSomeOneLkeyPlan($load_repay['deal_id'],$load_repay['l_key'],$v['user_id']);
			if($is_plan){
				if ($is_plan['has_repay'] == 1) {
					//已回款
					unset($load_repay['self_money']);
					unset($load_repay['repay_money']);
					unset($load_repay['interest_money']);
					unset($load_repay['manage_money']);
					unset($load_repay['repay_manage_money']);
					unset($load_repay['manage_interest_money']);
					unset($load_repay['manage_interest_money_rebate']);
					unset($load_repay['has_repay']);
				}
				$dealLoadRepayDao->update($load_repay,array('deal_id'=>$load_repay['deal_id'],'l_key'=>$load_repay['l_key'],'user_id'=>$v['user_id']));
			}else{
				$dealLoadRepayDao->insert($load_repay);
			}
		}
	}
}