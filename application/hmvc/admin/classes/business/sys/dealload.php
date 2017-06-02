<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_dealload extends Business {
	public function business() {
		
	}
	//处理投标人投标金额
	public function dealLoadUserMoney($load_list){
		$result = array();
		$result['yott_users'] = array();
		$result['status'] = 0;
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
							return @json_encode($result);
						}
					}
				}else {
					$result['message'] = "放款失败，投资人冻结资金不足";
					$result['status'] = 1;
				}
			}
			//管理员提成
			//获取投标用户的所属管理员id
			$admin_id = \Core::dao('user_user')->getUser($v['user_id'],'id,admin_id,platform_code');

			if($admin_id[$v['user_id']]) {
				$adminstatus = \Core::business('sys_admin_admin')->adminreferrals($admin_id[$v['user_id']],$loanBase,$loanBid);
				if($adminstatus['status'] == 1) {
					$result['message'] = $adminstatus['message'];
					$result['status'] = 1;
				}
			}
			//是否优投用户
			if($adminstatus['is_post_yott']) {
				//记录优投用户id
				$result['yott_users'][] = $v['user_id'];
			}
			//返利给用户
			if(floatval($v['rebate_money']) != 0 || intval($v['bid_score']) != 0) {
				//修改is_rebate状态
				$rebateStatus = \Core::dao('loan_dealload')->update(array('is_rebate'=>1),array('is_rebate'=>0,'id'=>$v['id'],'user_id'=>$v['user_id']));
				//返利
				if($rebateStatus !== false) {
					//返利
					if(floatval($v['rebate_money']) != 0) {
						//记录日志
						$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
						$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返利";
						$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($v['user_id'],floatval($v['rebate_money']),$log_msg,24);
						if($editMoneyStatus === false) {
							$result['message'] = "放款失败，投资返利出错";
							$result['status'] = 1;
						}
					}
					//返积分
					if(intval($v['bid_score']) != 0) {
						$editScoreStatus = false;
						//记录积分日志
						$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
						$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返积分";
						$editScoreStatus = \Core::business('user_userinfo')->editUserScore($v['user_id'],intval($v['bid_score']),$log_msg,2);
						if($editScoreStatus === false) {
							$result['message'] = "放款失败，积分返还出错";
							$result['status'] = 1;
						}
					}
					//TODO VIP奖励 暂时没用到
				}
			}
		}
		return $result;
	}
	//是否本地标，扣除本地标风险保证金
	public function dealLoadBond($deal_id,$user_id){
		$loanextDao = \Core::dao('loan_loanext');
		$loanBaseDao = \Core::dao('loan_loanbase');
		$amt_common = $loanextDao->getAmtconfig($deal_id);
		if($amt_common['l_guarantees_amt'] != 0){
			$url = \Core::getUrl("deal","","deal", array("id" => $deal_id));
			$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBaseDao->getName($deal_id) . "</a>],咨询服务费";
			$editLockMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($user_id,-$amt_common['l_guarantees_amt'],$log_msg,120);
			if($editLockMoneyStatus === false){
				$result['message'] = "放款失败，扣除本地标风险保证金失败";
				return @json_encode($result);
			}
		}
	}
}