<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_dealload extends Business {
	public function business() {
		
	}
	//处理投标人投标金额-满标放款
	public function dealLoadUserLoanMoney($load_list){
		$result = array();
		$result['status'] = 0;
		if(!$load_list) {
			$result['status'] = 1;
			return $result;
		}
		$userDao = \Core::dao('user_user');
		$loanBaseDao = \Core::dao('loan_loanbase');
		foreach ($load_list as $v) {
			//扣除投资人的冻结金额，金额为投标金额 - 红包金额 - 优惠券金额
			//实际扣款金额
			$realmoney = 0;
			//获取用户冻结金额
			$lockmoney = 0;
			$lockmoney = $userDao->getUserLockMoneyById($v['user_id']);
			if($v['is_old_loan'] == 0) {
				$ecv_money = 0;
				if ($v['ecv_id'] > 0) {
					//红包金额
					$ecv_money =  \Core::dao('loan_ecv')->getMoneyById($v['ecv_id']);
				}
				$bonus_money = 0;
				if ($v['bonus_user_id'] > 0) {
					//优惠券金额
					$bonus_rule_id = \Core::dao('user_bonususer')->getBonusRuleIdByUserId($v['bonus_user_id']);
					$bonus_money = \Core::dao('user_bonusrule')->getMoneyById($bonus_rule_id);
				}
				//实际扣款金额
				$realmoney = $v['money'] - $ecv_money - $bonus_money;
				if (($v['money'] - $ecv_money > 0) && ($lockmoney > $realmoney)) {
					$editLockMoneyStatus = false;
					$url = \Core::getUrl("deal","","deal", array("id" => $v['deal_id']));
					$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBaseDao->getName($v['deal_id']) . "</a>],投标成功";
					$editLockMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($v['user_id'],-$realmoney,$log_msg,2);
					if($editLockMoneyStatus === false){
						$result['message'] = "放款失败，扣除投资金额失败";
						$result['status'] = 1;
						return $result;
					}else {
						//修改放款状态为已放款
						$updateloan = array();
						$updatewhere = array();
						$updateloan['is_has_loans'] = 1;
						$updatewhere['is_has_loans'] = 0;
						$updatewhere['id'] = $v['id'];
						$updatewhere['user_id'] = $v['user_id'];
						$updateloanstatus = \Core::dao('loan_dealload')->update($updateloan,$updatewhere);
						if($updateloanstatus === false) {
							$result['message'] = "放款失败，修改放款状态失败";
							$result['status'] = 1;
							return $result;
						}
					}
				}else {
					$result['message'] = "放款失败，投资人冻结资金不足";
					$result['status'] = 1;
					return $result;
				}
			}
			//TODO 管理员提成 暂无
			//获取投标用户的所属管理员id
			/*addGerman('loanSuccess',array('user_id'=>$v['user_id']),'admin');
			$admin_id = \Core::dao('user_user')->getUser($v['user_id'],'id,admin_id,platform_code');
			if($admin_id[$v['user_id']]) {
				$loanBid = $loanBidDao->getOneLoanById($v['deal_id'],'load_money');
				$loanBase = $loanBaseDao->getloanbase($v['deal_id'],'id,name,repay_time_type,repay_time');
				$admin_deal_info = array_merge($loanBase,$loanBid);
				$adminstatus = \Core::business('sys_admin_admin')->adminReferrals($admin_id[$v['user_id']],$admin_deal_info);
				if($adminstatus['status'] == 1) {
					$result['message'] = $adminstatus['message'];
					$result['status'] = 1;
				}
			}
			//是否优投用户
			if($adminstatus['is_post_yott']) {
				//记录优投用户id
				$result['yott_users'][] = $v['user_id'];
			}*/
			//TODO 返利给用户
			$loanBase = $loanBaseDao->getloanbase($v['deal_id'],'id,name,repay_time_type,repay_time');
			if((floatval($v['rebate_money']) != 0 || intval($v['bid_score']) != 0) && $v['is_rebate'] == 0 ) {
				//修改is_rebate状态
				$rebateStatus = \Core::dao('loan_dealload')->update(array('is_rebate'=>1),array('is_rebate'=>0,'id'=>$v['id'],'user_id'=>$v['user_id']));
				//返利
				if($rebateStatus !== false) {
					//返利
					if(floatval($v['rebate_money']) != 0) {
						//返利并记录日志
						$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
						$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返利";
						$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($v['user_id'],floatval($v['rebate_money']),$log_msg,24);
						if($editMoneyStatus === false) {
							$result['message'] = "放款失败，投资返利出错";
							$result['status'] = 1;
							return $result;
						}
					}
					//返积分
					if(intval($v['bid_score']) != 0) {
						$editScoreStatus = false;
						//返积分并记录积分日志
						$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
						$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返积分";
						$editScoreStatus = \Core::business('user_userinfo')->editUserScore($v['user_id'],intval($v['bid_score']),$log_msg,2);
						if($editScoreStatus === false) {
							$result['message'] = "放款失败，积分返还出错";
							$result['status'] = 1;
							return $result;
						}
					}
					//TODO VIP奖励 暂时没用到
				}
			}
		}
		return $result;
	}
	//处理投标人投标金额-流标返款
	public function dealLoadUserBackMoney($load_list){
		$result = array();
		$result['status'] = 0;
		$dealLoadDao = \Core::dao('loan_dealload');
		$loanBaseDao = \Core::dao('loan_loanbase');
		if(!$load_list) {
			$result['status'] = 1;
			$result['message'] = '投标列表不存在';
			return $result;
		}
		foreach ($load_list as $v) {
			//退还使用的优惠券
			$bonus_msg = '';
			$ecv_money = 0;
			if ($v['ecv_id'] > 0) {
				//红包金额
				$ecv_money =  \Core::dao('loan_ecv')->getMoneyById($v['ecv_id']);
				//红包使用数量-1
				$use_count =  \Core::dao('loan_ecv')->getUseCountById($v['ecv_id']);
				$update_status = \Core::dao('loan_ecv')->update(array('id'=>$v['ecv_id']),array('use_count'=>$use_count-1));
				if($update_status === false) {
					$result['status'] = 1;
					$result['message'] = '返还红包失败';
					return $result;
				}
			}
			$bonus_money = 0;
			if ($v['bonus_user_id'] > 0) {
				//优惠券金额
				$bonus_rule_id = \Core::dao('user_bonususer')->getBonusRuleIdByUserId($v['bonus_user_id']);
				$bonus_info = \Core::dao('user_bonusrule')->find(array('id'=>$bonus_rule_id));
				$bonus_name = \Core::dao('user_bonustype')->getBonusTypeById($bonus_info['bonus_type_id']);
				$bonus_money = $bonus_info['money'];
				$update_bonus_info = array();
				$update_bonus_info['module'] = '';
				$update_bonus_info['module_pk_Id'] = 0;
				$update_bonus_info['used_time'] = 0;
				//返还优惠券
				$update_status = \Core::dao('user_bonususer')->update(array('id'=>$bonus_rule_id),$v['bonus_user_id']);
				if($update_status === false) {
					$result['status'] = 1;
					$result['message'] = '返还优惠券失败';
					return $result;
				}
				$bonus_msg .= ",返还所使用的优惠券[" . $bonus_name['bonus_type_name'] . "]金额" . $bonus_money;
			}
			if ($v['money'] - $ecv_money > 0) {
				$url = \Core::getUrl("deal","","deal", array("id" => $v['deal_id']));
				$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBaseDao->getName($v['deal_id']) . "</a>],流标返还";
				if($v['is_old_loan'] == 0) {
					//使用了优惠券
					//将用户冻结资金返回到用户余额
					//记录用户金钱日志和用户日志
					$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBaseDao->getName($v['deal_id']). "</a>],流标返还" . $bonus_msg;

				}
				$return_money = ($v['money']-$ecv_money-$bonus_money);
				$editMoneyStatus = false;
				//修改用户余额
				$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($v['user_id'],$return_money,$log_msg,19);
				if($editMoneyStatus === false) {
					$result['message'] = "返还失败，返还用户余额出错";
					$result['status'] = 1;
					return $result;
				}
				$editMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($v['user_id'],-$return_money,$log_msg,19);
				if($editMoneyStatus === false) {
					$result['message'] = "返还失败，扣除冻结资金出错";
					$result['status'] = 1;
					return $result;
				}
			}
			//修改返还状态
			$update_status = $dealLoadDao->update(array('is_repay'=>1),array('id'=>$v['id']));
			if($update_status === false) {
				$result['status'] = 1;
				$result['message'] = '更新返回状态失败';
				return $result;
			}
		}
		return $result;

	}
	//是否本地标，扣除本地标风险保证金
	public function dealLoadBond($deal_id,$user_id){
		$deal_id = intval($deal_id);
		$user_id = intval($user_id);
		if($deal_id == 0 || $user_id == 0) return false;
		$loanextDao = \Core::dao('loan_loanext');
		$loanBaseDao = \Core::dao('loan_loanbase');
		$amt_config = $loanextDao->getAmtconfig($deal_id);
		$l_guarantees_amt = \Core::arrayKeyExists('l_guarantees_amt',$amt_config)?\Core::arrayGet($amt_config,'l_guarantees_amt'):0;
		if($l_guarantees_amt != 0){
			//更新保证金
			$amt_config['real_freezen_l_amt'] = $l_guarantees_amt;
			$amt_config = serialize($amt_config);
			$loanextDao->update(array('config_amt'=>$amt_config),array('loan_id'=>$deal_id));
			//扣除保证金
			$url = \Core::getUrl("deal","","deal", array("id" => $deal_id));
			$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBaseDao->getName($deal_id) . "</a>],咨询服务费";
			return \Core::business('user_userinfo')->editUserLockMoney($user_id,-$l_guarantees_amt,$log_msg,120);
		}
		return true;
	}

}