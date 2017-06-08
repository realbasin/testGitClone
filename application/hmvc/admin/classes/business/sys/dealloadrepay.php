<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_dealloadrepay extends Business {
	public function business() {
		
	}
	//生成回款计划
	public function makeLoadRepayPlan($loan,$load_repay, $idx,$repay_id , &$total_money){
		//贷款不存在
		if(!$loan){
			return false;
		}
		//查询投标人id 每个人生成一条回款信息
		$load_users = \Core::dao('loan_dealload')->getList(array('deal_id'=>$loan['id']),'id,user_id,money,is_winning,income_type,income_value');

		if(!$load_users){
			//投资者不存在
			return false;
		}
		$return = false;
		unset($load_repay['manage_money_rebate']);
		unset($load_repay['repay_money']);
		unset($load_repay['self_money']);
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		//转让标表dao
		$dealLoadTransferDao = \Core::dao('loan_dealloadtransfer');
		$true_repay_time = $loan['repay_time'];
		foreach ($load_users as $k=>$v){
			$loan['borrow_amount'] = $v['money'];
			$repaymoney = \Core::business('sys_dealrepay')->deal_repay_money($loan);
			$load_repay['user_id'] = $v['user_id'];
			$load_repay['repay_id'] = $repay_id;
			$load_repay['load_id'] = $v['id'];
			$load_repay['has_repay'] = 0;
			$load_repay['t_user_id'] = 0;

			//TODO 根据不同还款方式，生成不同计划
			if ($k + 1 == $true_repay_time) {
				//最后一期 $loan['loantype'] 0 等额本息 1付息还本 2到期本息
				if($loan['loantype'] == 0) {
					$load_repay['repay_money'] = $repaymoney['last_month_repay_money'];
					$load_repay['self_money'] = $loan['borrow_amount'] - $total_money[$v['id']];
					unset($total_money[$v['id']]);
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
					$load_repay['self_money'] = number_format(\Core::business('sys_dealrepay')->get_self_money($idx, $v['money'], $repaymoney['month_repay_money'], $loan['rate']), 2);
					@$total_money[$v['id']] += round((float)$load_repay['self_money'],2);
					//$self_money = \Core::arrayKeyExists($v['id'],$total_money)?\Core::arrayGet($total_money,$v['id']):0;
					//$self_money += round((float)$load_repay['self_money'],2);
					//\Core::arraySet($total_money,$v['id'],$self_money);
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

			if($k+1 == count($load_users)){
				$load_repay['repay_manage_money'] = $load_repay['manage_money'] - round($load_repay['manage_money'] / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
				$load_repay['mortgage_fee'] = $loan['mortgage_fee'] - round($loan['mortgage_fee'] / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
			}
			else{
				$load_repay['repay_manage_money'] = $load_repay['manage_money']/ $loan['buy_count'];
				$load_repay['mortgage_fee'] = $loan['mortgage_fee'] / $loan['buy_count'];
			}

			$load_repay['interest_money'] =  $load_repay['repay_money'] - $load_repay['self_money'];
			//投资者管理费率 从config_common字段中获取
			$config_common = unserialize($loan['config_common']);
			$user_loan_manage_fee = \Core::arrayKeyExists('user_loan_manage_fee',$config_common)?\Core::arrayGet($config_common,'user_loan_manage_fee'):0;
			$load_repay['manage_money'] = $v['money']* floatval($user_loan_manage_fee)/100;
			if($v['is_winning']==1 && (int)$v['income_type']==2 && (float)$v['income_value']!=0){
				$load_repay['reward_money'] = $load_repay['interest_money'] * (float)$v['income_value'] * 0.01;
			}
			//TODO 获取已转让的标
			$transfer = $dealLoadTransferDao->getTransInfoByLoanId($v['id']);
			if($transfer) {
				//存在转让标
				$load_repay['t_user_id'] = $transfer['t_user_id'];
				//$repay_data['loantype'] = $loan['loantype'];
				//VIP利息管理费
				//$deal =  get_user_load_fee((int)$vv['t_user_id'] > 0 ? $vv['t_user_id'] : $vv['user_id'],0,$deal);
				//$repay_data['manage_interest_money'] = $repay_data['interest_money']*floatval($deal["user_loan_interest_manage_fee"])/100;

				//投资者 授权服务机构获取的利息管理费抽成
				//$rebate_rs = get_rebate_fee((int)$vv['t_user_id'] > 0 ? $vv['t_user_id'] : $vv['user_id'],"invest");
				$load_repay['manage_interest_money_rebate'] = $load_repay['manage_interest_money']* floatval(C('INVESTORS_COMMISSION_RATIO'))/100;
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
				$return = $dealLoadRepayDao->update($load_repay,array('deal_id'=>$load_repay['deal_id'],'l_key'=>$load_repay['l_key'],'user_id'=>$v['user_id']));
			}else{
				$return = $dealLoadRepayDao->insert($load_repay);
			}
			$load_repay_plan[] = $load_repay;
			$load_ids[] = $v['id'];
		}


		if($return === false) {
			return $return;
		}else {
			return $load_repay_plan;
		}

	}
}