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
		//借款者管理费
		$borrow_manage = $load_repay['manage_money'];
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
			if ($idx + 1 == $true_repay_time) {
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
					$load_repay['self_money'] = round(\Core::business('sys_dealrepay')->get_self_money($idx, $v['money'], $repaymoney['month_repay_money'], $loan['rate']), 2);
					@$total_money[$v['id']] += round((float)$load_repay['self_money'],2);
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
			//从借款者均摊下来的管理费（借款者管理费）
			if($k+1 == count($load_users)){
				$load_repay['repay_manage_money'] = $borrow_manage - round($borrow_manage / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
				$load_repay['mortgage_fee'] = $loan['mortgage_fee'] - round($loan['mortgage_fee'] / $loan['buy_count'],2) * ($loan['buy_count'] - 1);
			}
			else{
				$load_repay['repay_manage_money'] = $borrow_manage/ $loan['buy_count'];
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

			}
			//VIP利息管理费
			$config_common = unserialize($loan['config_common']);
			$user_loan_interest_manage_fee = \Core::arrayKeyExists('user_loan_interest_manage_fee',$config_common)?\Core::arrayGet($config_common,'user_loan_interest_manage_fee'):0;
			$load_repay['manage_interest_money'] = $load_repay['interest_money']*floatval($user_loan_interest_manage_fee)/100;
			//投资者 授权服务机构获取的利息管理费抽成
			$load_repay['manage_interest_money_rebate'] = $load_repay['manage_interest_money']* floatval(C('INVESTORS_COMMISSION_RATIO'))/100;
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
	//更新回款计划
	public function updateLoadRepayPlan($user_load,$money,$status=0,$impose_money=0,$manage_impose_money=0,$is_site_repay=0){
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		//$user_load = $dealLoadRepayDao->getSomeOneLkeyPlan($deal_id,$l_key,$user_id);
		if(!$user_load) {
			return false;
		}
		//整合更新数据
		$user_load_data = array();
		//确认还款时间
		$user_load_data['true_repay_time'] = time();
		//是否网站代还
		$user_load_data['is_site_repay'] = $is_site_repay;
		//是否收到还款
		$user_load_data['has_repay'] = 1;
		//还款是否逾期状态
		$user_load_data['status'] = $status;
		//真实还款金额
		$user_load_data['true_repay_money'] = (float)$user_load['repay_money'];
		//真实还本金
		$user_load_data['true_self_money'] = (float)$user_load['self_money'];
		//真实利息
		$user_load_data['true_interest_money'] = (float)$user_load['interest_money'];
		//真实管理费
		$user_load_data['true_manage_money'] = (float)$user_load['manage_money'];
		//利息管理费
		$user_load_data['true_manage_interest_money'] = (float)$user_load['manage_interest_money'];
		//从借款者均摊下来的管理费
		$user_load_data['true_repay_manage_money'] = (float)$user_load['repay_manage_money'];
		//实际收到：利息管理费,是在还款时生成
		$user_load_data['true_manage_interest_money_rebate'] = (float)$user_load['manage_interest_money_rebate'];
		//逾期罚息
		$user_load_data['impose_money'] = number_format($impose_money*($user_load['repay_money']/$money),2);
		//罚息管理费
		$user_load_data['repay_manage_impose_money'] = number_format($manage_impose_money*($user_load['repay_money']/$money),2);
		//实际奖励收益
		$user_load_data['true_reward_money'] = (float)$user_load['reward_money'];
		//抵押物管理费
		$user_load_data['true_mortgage_fee'] = (float)$user_load['mortgage_fee'];
		$update_status = $dealLoadRepayDao ->update($user_load_data,array('deal_id'=>$user_load['deal_id'],'l_key'=>$user_load['l_key'],'user_id'=>$user_load['user_id']));
		return $update_status;
	}
	//判断当前期是否还款完毕
	public function isRepayedByLkey($deal_id,$l_key){
		$where_no_repay = array();
		$where_no_repay['deal_id'] = $deal_id;
		$where_no_repay['l_key'] = $l_key;
		$where_no_repay['has_repay'] = 0;
		return \Core::dao('loan_dealloadrepay')->getCount($where_no_repay);
	}
}