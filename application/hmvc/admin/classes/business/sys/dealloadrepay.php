<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_dealloadrepay extends Business {
	public function business() {
		
	}
	//生成回款计划
	public function makeLoadRepayPlan($loan,$load_repay, $repay_id){
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
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		//转让标表dao
		$dealLoadTransferDao = \Core::dao('loan_dealloadtransfer');
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
			//投资者管理费率 从config_common字段中获取
			$config_common = unserialize($loan['config_common']);
			$user_loan_manage_fee = \Core::arrayKeyExists('user_loan_manage_fee',$config_common)?\Core::arrayGet($config_common,'user_loan_manage_fee'):0;
			$load_repay['manage_money'] = $v['money']* floatval($user_loan_manage_fee)/100;
			if($v['is_winning']==1 && (int)$v['income_type']==2 && (float)$v['income_value']!=0){
				$load_repay['reward_money'] = $load_repay['interest_money'] * (float)$v['income_value'] * 0.01;
			}
			//TODO 获取已转让的标

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