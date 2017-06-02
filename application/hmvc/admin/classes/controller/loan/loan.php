<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_loan_loan extends controller_sysBase {
	
	public function before($method, $args) {
		\Language::read('loan');
	}

	public function do_index() {
		$this -> do_all();
	}
	
	//全部贷款
	public function do_all(){
		$loanBusiness=\Core::business('loan_loanenum');
		//贷款类型数据
		\Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
		->set('loantype',$loanBusiness->enumLoanType())
		->set('dealcate',$loanBusiness->enumDealCate())
		->set('dealusetype',$loanBusiness->enumDealUseType())
		->set('sorcode',$loanBusiness->enumSorCode())
		->set('dealstatus',$loanBusiness->enumDealStatus());
		\Core::view() -> load('loan_loanlist');
	}
	
	//新增贷款
	public function do_add(){
		$loanBusiness=\Core::business('loan_loanenum');
		\Core::view()->set('loantype',$loanBusiness->enumLoanType())
		->set('dealcate',$loanBusiness->enumDealCate())
		->set('dealusetype',$loanBusiness->enumDealUseType())
		->set('dealloantype',$loanBusiness->enumDealLoanType())
		->set('sorcode',$loanBusiness->enumSorCode());
		\Core::view() -> load('loan_loanadd');
	}
	//还款计划
	public function do_repay_plan(){
		// 用户余额
		$loan_id = \Core::get('loan_id',0);
		$userDao = \Core::dao('user_user');
		$user = \Core::dao('loan_loanbase')->getLoan($loan_id,'id,user_id');
		$user_id = $user[$loan_id]['user_id'];
		$user_money = $userDao->getUser($user_id,'id,AES_DECRYPT(money_encrypt,'."'__FANWEP2P__'".') AS money');
		$money = $user_money[$user_id]['money']?$user_money[$user_id]['money']:0.00;
		//TODO 需还总额 $data['l_key']=$money
		//$loanBusiness = \Core::business('loan_loanenum');
		//$loan_data = $loanBusiness->enumLoanRepay($loan_id);
		//\Core::dump($loan_data);die();
		\Core::view()->set('loan_id',$loan_id)->set('usermoney',$money)->load('loan_repayplan');
	}
	//手动还款
	public function do_manual_repay(){
		$data = array();
		$data['code'] = 000;
		$loan_id = \Core::get('id',0);
		if(!$loan_id) {
			$data['message'] = \Core::L('fail');
			echo @json_encode($data);
			exit;
		}
		$l_key = \Core::get('lkey',-1);
		$user = \Core::dao('loan_loanbase')->getLoan($loan_id,'id,user_id');
		if(!$user){
			$data['message'] = \Core::L('no_loan');
			echo @json_encode($data);
			exit;
		}
		$user_id = $user[$loan_id]['user_id'];
		//提前还款（批量所有（未还）期）
		/*if($l_key < 0) {

		}else {
			//正常还款（单期）
		}*/
		//执行还款
		$status = \Core::business('loan_loanenum')->repayLoanBills($loan_id,$l_key,$user_id);
		if($status == 0) {
			$data['code'] = 200;
			echo @json_encode($data);
		}else {
			$data['message'] = $status['show_err'];
			echo @json_encode($data);
		}

	}
	//投标详情
	public function do_detail(){
		$loan_id = \Core::get('loan_id',0);
		$loanbidDao = \Core::dao('loan_loanbid');
		$loanbaseDao = \Core::dao('loan_loanbase');
		$loan_bid_info = $loanbidDao->getLoan($loan_id,'loan_id,start_time,load_money,loan_time,repay_start_time,bad_time,deal_status,buy_count,is_has_loans,end_time,is_has_received');
		$loan_base_info = $loanbaseDao->getLoan($loan_id,'id,name,borrow_amount,repay_time_type');
		$loan = array_merge($loan_base_info[$loan_id],$loan_bid_info[$loan_id]);
		if($loan['repay_time_type'] == 1) {
			$loan['repay_time_type'] = '按月还款';
		}else {
			$loan['repay_time_type'] = '按天还款';
		}
		$loan_time = $loan['start_time']+$loan['end_time'];
		if(($loan_time - 1)<time()){
			$loan['is_over_time'] = 1;
		}else {
			$loan['is_over_time'] = 0;
		}
		$loan['need_money'] = number_format($loan['borrow_amount'] - $loan['load_money'],2);
		\Core::view()->set('loan',$loan)->set('loan_id',$loan_id)->load('loan_detail');
	}
	/**
	 * 满标放款（真放款）
	 * mark：生成还款计划/回款计划，扣除理财人冻结资金，发放邀请返利等
	 */
	public function do_loans(){
		$nowtime = time();
		$result = array();
		$loanbid_info = array();
		$loanbase_info = array();
		$result['code'] = '000';
		$deal_id = \Core::get('id',0);
		$loanbid_info['repay_start_time'] = \Core::get('repay_start_time','');
		if(!$deal_id) {
			$result['message'] = '贷款不存在';
			return @json_encode($result);
		}
		if($loanbid_info['repay_start_time'] == '') {
			$result['message'] = '放款失败，还款时间不能为空';
			return @json_encode($result);
		}else {
			$loanbid_info['loan_time'] = strtotime($loanbid_info['repay_start_time']);
			$loanbid_info['repay_start_time'] = strtotime(date('Y-m-d', $loanbid_info['loan_time']));
		}
		//实例化dao
		$loanBaseDao = \Core::dao('loan_loanbase');
		$loanBidDao = \Core::dao('loan_loanbid');
		$loanextDao = \Core::dao('loan_loanext');
		//获取标信息，确认是否符合放款条件
		//要验证的字段
		$fields = 'loan_id,deal_status,load_money,repay_start_time,buy_count,loan_time';
		//获取数据
		$loanBid = $loanBidDao->getOneLoanById($deal_id,$fields);
		$loanBase = $loanBaseDao->getloanbase($deal_id,'id,name,user_id,borrow_amount,repay_time_type,repay_time,rate');
		$loanExt = $loanextDao->getExtByLoanId($deal_id);
		if(!$loanBid || !$loanBase || !$loanExt) {
			$result['message'] = '贷款不存在';
			return @json_encode($result);
		}
		if(!in_array($loanBid['deal_status'],array(2, 4, 5))) {
			$result['message'] = "放款失败，借款不是满标状态";
			return @json_encode($result);
		}
		$borrow_money = $loanBaseDao->getloanbase($deal_id,'id,borrow_amount');

		if($borrow_money['borrow_amount'] < $loanBid['load_money']) {
			$result['message'] = "放款失败，问题标";
			return @json_encode($result);
		}
		//放款给用户，放款时，将贷款设置为无效
		$loanbase_info['is_effect'] = 0;
		$loanBid['deal_status'] = $loanbid_info['deal_status'] = 4;
		$loanbid_info['is_has_loans'] = 1;
		//TODO 开启事务操作
		//更新贷款状态
		$effectBidNumbers =$loanBidDao->updateData(array('loan_id'=>$deal_id),$loanbid_info);
		$effectBaseNumbers = $loanBaseDao->update($loanbase_info,array('id'=>$deal_id));
		if($effectBidNumbers && $effectBaseNumbers){

			//放款，修改用户余额
			$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($loanBase['user_id'],$loanBase['borrow_amount']);
			if($editMoneyStatus === false) {
				$result['message'] = "放款失败，修改余额出错";
				return @json_encode($result);
			}else {
				$editMoneyStatus = false;
				//记录用户金钱日志和用户日志
				$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
				$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],招标成功";
				$loginfoid = \Core::business('user_userlog')->addUserLog($loanBase['user_id'],$log_msg,array('money'=>$borrow_money['borrow_amount']));
				$moneyloginfoid = \Core::business('user_userlog')->addUserMoneyLog($loanBase['user_id'],$log_msg,$borrow_money['borrow_amount'],3);
			}
			//收取服务费
			//获取普通配置中的服务费率等配置 loan_ext表的config_common字段
			$servicesfee = 0.05;
			$services_fee = $loanBase['borrow_amount'] * floatval($servicesfee) / 100;
			//服务费，修改用户余额
			if($services_fee){
				$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($loanBase['user_id'],-$services_fee);
				if($editMoneyStatus === false) {
					$result['message'] = "放款失败，收取服务费出错";
					return @json_encode($result);
				}else {
					//记录用户金钱日志和用户日志
					$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
					$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],服务费";
					$loginfoid = \Core::business('user_userlog')->addUserLog($loanBase['user_id'],$log_msg,array('money'=>-$services_fee));
					$moneyloginfoid = \Core::business('user_userlog')->addUserMoneyLog($loanBase['user_id'],$log_msg,-$services_fee,14);
				}
			}
			//是否本地标，扣除本地标风险保证金
			$amt_common = $loanextDao->getAmtconfig($deal_id);
			if($amt_common['l_guarantees_amt'] != 0){
				$editLockMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($loanBase['user_id'],-$amt_common['l_guarantees_amt']);
				if($editLockMoneyStatus === false){
					$result['message'] = "放款失败，扣除本地标风险保证金失败";
					return @json_encode($result);
				}else {
					$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
					$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],咨询服务费";
					$lockmoneyloginfoid = \Core::business('user_userlog')->addUserLockMoneyLog($loanBase['user_id'],$log_msg,-$amt_common['l_guarantees_amt'],120);
				}
			}
			//TODO 积分变动
			//扣除投资人金额
			$load_list = \Core::dao('loan_dealload')->getLoads($deal_id,'id,user_id,money,is_old_loan,rebate_money,bid_score,is_winning,income_type,income_value,ecv_id,bonus_user_id');
			if($load_list) {
				$yott_users = array();
				$userDao = \Core::dao('user_user');
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
							$editLockMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($v['user_id'],-$realmoney);
							if($editLockMoneyStatus === false){
								$result['message'] = "放款失败，扣除投资金额失败";
								return @json_encode($result);
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
								//记录日志
								$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
								$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投标成功";
								$lockmoneyloginfoid = \Core::business('user_userlog')->addUserLockMoneyLog($v['user_id'],$log_msg,-$realmoney,120);
							}
						}
					}
					//管理员提成
					//获取投标用户的所属管理员id
					$admin_id = \Core::dao('user_user')->getUser($v['user_id'],'id,admin_id,platform_code');

					if($admin_id[$v['user_id']]) {
						$adminstatus = \Core::business('sys_admin_admin')->adminreferrals($admin_id[$v['user_id']],$loanBase,$loanBid);
						if($adminstatus['status'] == 1) {
							$result['message'] = $adminstatus['message'];
							return @json_encode($result);
						}
					}
					//是否优投用户
					if($adminstatus['is_post_yott']) {
						//记录优投用户id
						$yott_users[] = $v['user_id'];
					}
					//返利给用户
					if(floatval($v['rebate_money']) != 0 || intval($v['bid_score']) != 0) {
						//修改is_rebate状态
						$rebateStatus = \Core::dao('loan_dealload')->update(array('is_rebate'=>1),array('is_rebate'=>0,'id'=>$v['id'],'user_id'=>$v['user_id']));
						if($rebateStatus !== false) {
							//返利
							if(floatval($v['rebate_money']) != 0) {
								$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($v['user_id'],floatval($v['rebate_money']));
								if($editMoneyStatus === false) {
									$result['message'] = "放款失败，投资返利出错";
									return @json_encode($result);
								}else{
									//记录日志
									$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
									$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返利";
									$loginfoid = \Core::business('user_userlog')->addUserLog($v['user_id'],$log_msg,array('money'=>floatval($v['rebate_money'])));
									$moneyloginfoid = \Core::business('user_userlog')->addUserMoneyLog($v['user_id'],$log_msg,floatval($v['rebate_money']),24);
								}
							}
							//返积分
							if(intval($v['bid_score']) != 0) {
								$editScoreStatus = false;
								$editScoreStatus = \Core::business('user_userinfo')->editUserScore($v['user_id'],intval($v['bid_score']));
								if($editScoreStatus === false) {
									$result['message'] = "放款失败，积分返还出错";
									return @json_encode($result);
								}else{
									//记录积分日志
									$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
									$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],投资返积分";
									$scoreloginfoid = \Core::business('user_userlog')->addUserScoreLog($v['user_id'],$log_msg,intval($v['bid_score']),2);
									$loginfoid = \Core::business('user_userlog')->addUserLog($loanBase['user_id'],$log_msg,array('score'=>intval($v['bid_score'])));
								}
							}
							//TODO VIP奖励 暂时没用到
						}
					}
				}
			}else {
				$result['message'] = "放款失败，投资不存在";
				return @json_encode($result);
			}
		}
		//更新贷款状态为已放款
		$load_loan = \Core::dao('loan_dealload')->update(array('is_has_loans'=>1),array('deal_id'=>$deal_id));
		//TODO 分销相关
		//生成还款计划
		$repayplan = \Core::business('sys_dealrepay')->makeRepayPlan($loanBase,$loanBid,$loanExt,$loanbid_info['loan_time']);
		if($repayplan){
			//放款成功
			$loanBaseDao->update(array('is_effect'=>1,'loan_status'=>1),array('id'=>$deal_id));
			//发送短信发送邮件

			//是否存在优投用户，存在发送推送
			if($yott_users) {

				$result['code'] = 200;
				$result['message'] = "放款成功,还/回款计划已生成,发送优投推送";
			}
			//TODO 发借款成功邮件

			//TODO 发借款成功站内信

			//TODO 发送借款协议范本
			//手机端自动提现
			if ($loanBase['is_mobile'] > 0) {

			}
			$result['code'] = 200;
			$result['message'] = "放款成功,还/回款计划已生成";
			return @json_encode($result);
		}else{
			$result['message'] = "放款失败";
			return @json_encode($result);
		}

	}

	/**
	 * 流标还返
	 * $id deal_id
	 * $type 0 普通返回，  1资金托管
	 */
	public function do_received(){
		$result = array();
		$result['code'] = '000';
		$deal_id = \Core::get('id',0);
		$reason = \Core::get('reason','');
		if($deal_id == 0) {
			$result['message'] = '返还失败，借款不存在';
			return @json_encode($result);
		}
		if($reason == '') {
			$result['message'] = '请填写流标原因';
			return @json_encode($result);
		}
		//实例化dao
		$loanBaseDao = \Core::dao('loan_loanbase');
		$loanBidDao = \Core::dao('loan_loanbid');
		$loanextDao = \Core::dao('loan_loanext');
		//获取标信息，确认是否符合放款条件
		//要验证的字段
		$fields = 'loan_id,deal_status,load_money,repay_start_time,buy_count,loan_time';
		//获取数据
		$loanBid = $loanBidDao->getOneLoanById($deal_id,$fields);
		$loanBase = $loanBaseDao->getloanbase($deal_id,'id,name,user_id,borrow_amount,repay_time_type,repay_time,rate');
		$loanExt = $loanextDao->getExtByLoanId($deal_id);
		if(!$loanBid || !$loanBase || !$loanExt) {
			$result['message'] = '返还失败，借款不存在';
			return @json_encode($result);
		}
		if (intval($loanBid['deal_status']) >= 4) {
			$result['info'] = "返还失败，借款状态为还款状态";
			return @json_encode($result);
		}
		//流标时返还
		//投资人列表
		$dealLoadDao = \Core::dao('loan_dealload');
		$load_list = $dealLoadDao->getLoads($deal_id,'id,user_id,money,is_old_loan,rebate_money,bid_score,is_winning,income_type,income_value,ecv_id,bonus_user_id');
		if($load_list) {
			$yott_users = array();
			$userDao = \Core::dao('user_user');
			foreach ($load_list as $v) {
				//退还使用的优惠券
				$bonus_msg = '';
				$ecv_money = 0;
				if ($v['ecv_id'] > 0) {
					//红包金额
					$ecv_money =  \Core::dao('loan_ecv')->getMoneyById($v['ecv_id']);
					//红包使用数量-1
					$use_count =  \Core::dao('loan_ecv')->getUseCountById($v['ecv_id']);
					\Core::dao('loan_ecv')->update(array('id'=>$v['ecv_id']),array('use_count'=>$use_count-1));
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
					\Core::dao('user_bonususer')->update(array('id'=>$bonus_rule_id),$v['bonus_user_id']);
					$bonus_msg .= ",返还所使用的优惠券[" . $bonus_name['bonus_type_name'] . "]金额" . $bonus_money;
				}
				if ($v['money'] - $ecv_money > 0) {
					$url = \Core::getUrl("deal","","deal", array("id" => $loanBase['id']));
					$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],流标返还";
					if($v['is_old_loan'] == 0) {
						//使用了优惠券
						//将用户冻结资金返回到用户余额
						//记录用户金钱日志和用户日志
						$log_msg = "[<a href='".$url."' target='_blank'>" . $loanBase['name'] . "</a>],流标返还" . $bonus_msg;

					}
					$return_money = ($v['money']-$ecv_money-$bonus_money);
					$editMoneyStatus = false;
					//修改用户余额
					$editMoneyStatus = \Core::business('user_userinfo')->editUserMoney($v['user_id'],$return_money);
					$editMoneyStatus = \Core::business('user_userinfo')->editUserLockMoney($v['user_id'],-$return_money);
					if($editMoneyStatus === false) {
						$result['message'] = "返还失败，修改余额出错";
						return @json_encode($result);
					}else {
						//修改成功，记录日志
						$loginfoid = \Core::business('user_userlog')->addUserLog($v['user_id'], $log_msg, array('money' => $return_money));
						$moneyloginfoid = \Core::business('user_userlog')->addUserMoneyLog($v['user_id'], $log_msg, $return_money, 19);
						$lockmoneyloginfoid = \Core::business('user_userlog')->addUserLockMoneyLog($v['user_id'], $log_msg, -$return_money, 19);
						//未使用优惠券
					}
				}
				//修改返还状态
				$dealLoadDao->update(array('is_repay'=>1),array('id'=>$v['id']));
				//获取投标用户的用户码
				$admin_id = \Core::dao('user_user')->getUser($v['user_id'],'id,admin_id,platform_code');
				//是否优投用户
				if($admin_id[$v['user_id']]['platform_code'] == 'yott') {
					//记录优投用户id
					$yott_users[] = $v['user_id'];
				}
			}
			//TODO 推送优投信息
			//修改贷款状态，并返回结果信息
			$bad_data['bad_msg'] = $reason;
			if($dealLoadDao->find(array('is_repay'=>0,'deal_id'=>$deal_id))) {
				$loanBidDao->update($bad_data,array('loan_id'=>$deal_id));
				$result['message'] = '部分返还';
				$result['code'] = 200;
			}else{
				$bad_data['is_has_received'] = 1;
				$bad_data['bad_time'] = getGmtime()+C('time_zone')*3600;
				//$bad_data['bad_date'] = to_date(TIME_UTC, "Y-m-d");
				$bad_data['deal_status'] = 3;
				$loanBidDao->update($bad_data,array('loan_id'=>$deal_id));
				$result['message'] = '返还成功';
				$result['code'] = 200;
			}
		}else{
			$result['message'] = '返还失败，投资不存在';
			return @json_encode($result);
		}
		//保存贷款状态更改信息
		$deal_log = array();
		$deal_log['deal_id'] = $deal_id;
		$deal_log['user_id'] = $loanBase['user_id'];
		$deal_log['type'] = 19;
		$deal_log['create_time'] = getGmtime()+C('time_zone')*3600;
		\Core::dao('loan_dealstatuslog')->insert($deal_log);
		return @json_encode($result);
	}
	//贷款详细信息编辑
	public function do_loan_show_edit(){
		if(chksubmit()) {
			\Core::dump('test');die();
			//提交保存

		}else {
			$loan_id = \Core::get('loan_id',0);
			$loanBusiness=\Core::business('loan_loanenum');
			//根据借款id，获取贷款基本信息
			$basefields = 'id,deal_sn,name,user_id,type_id,loantype,borrow_amount,repay_time,rate,is_referral_award,use_type,repay_time_type,use_type';
			$loanbase = \Core::dao('loan_loanbase')->getloanbase($loan_id,$basefields);
			//获取会员名称
			$user_id = $loanbase['user_id'];
			$user = \Core::dao('user_user')->getUser($user_id,'id,user_name,real_name,pid');
			$username = \Core::arrayKeyExists($user_id, $user)?\Core::arrayGet(\Core::arrayGet($user, $user_id),'user_name').'('.\Core::arrayGet(\Core::arrayGet($user, $user_id),'real_name').')':'';
			if($username != '') {
				$username = '<a href="#&user_id='.$user_id.'">'.$username.'</a>';
			}
			//获取借款拓展字段
			$loanextDao =  \Core::dao('loan_loanext');
			$contractid = $loanextDao->getContract($loan_id);
			$amtConfig = $loanextDao->getAmtconfig($loan_id);
			$l_guarantees_amt = \Core::arrayKeyExists('l_guarantees_amt',$amtConfig)?\Core::arrayGet(\Core::arrayGet($amtConfig, 'l_guarantees_amt')):'';
			$guarantees_amt = \Core::arrayKeyExists('guarantees_amt',$amtConfig)?\Core::arrayGet(\Core::arrayGet($amtConfig, 'guarantees_amt')):0;
			if(!$l_guarantees_amt) {
				$l_guarantees_amt = number_format($loanbase['borrow_amount'] * $guarantees_amt / 100,2);
			}
			//commconfig
			$commonConfig = $loanextDao->getCommonconfig($loan_id);
			//获取合同范本
			$contract =  \Core::dao('loan_contract')->getContractList('id,title');
			//根据借款id，获取标基本信息
			$bidfields = 'loan_id,min_loan_money,max_loan_money,deal_status,start_time,end_time,uloadtype,portion,max_portion,use_ecv,risk_rank,risk_security';
			$loanbid = \Core::dao('loan_loanbid')->getOneLoanById($loan_id,$bidfields);
			\Core::view()->set('loantype',$loanBusiness->enumLoanType())
				->set('dealcate',$loanBusiness->enumDealCate())
				->set('dealusetype',$loanBusiness->enumDealUseType())
				->set('dealloantype',$loanBusiness->enumDealLoanType())
				->set('plathtml',$loanBusiness->userPlatRegVerified($loan_id,$user_id))
				->set('loanbase',$loanbase)
				->set('loanbid',$loanbid)
				->set('username',$username)
				->set('contract',$contract)
				->set('contractid',$contractid)
				->set('l_guarantees_amt',$l_guarantees_amt)
				->set('commonConfig',$commonConfig)
				->set('user_detail',$loanBusiness->userDetail($user_id))
				->set('sorcode',$loanBusiness->enumSorCode());
			\Core::view()->load('loan_showEdit');
		}
	}
	//投资人列表
	public function do_bidlist_json(){
		$deal_id = \Core::get('loan_id',0);
		$where = array();
		$where['deal_id'] = $deal_id;
		$fields = 'deal_id,user_name,money,create_time,is_auto,is_has_loans,is_repay';
		$data = \Core::dao('loan_dealload')->getList($where,$fields);
		if($data){
			foreach ($data as $k=>$v) {
				//状态
				$data[$k]['is_auto'] = $v['is_auto']?'自动':'手动';
				//是否转账
				$data[$k]['is_has_loans'] = $v['is_has_loans']?'已转账':'未转账';
				//流标返还
				$data[$k]['is_repay'] = $v['is_repay']?'已返还':'无返还';
				//投标时间
				$data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
			}
		}
		showJSON('200','',$data);
	}
	//投资人回款列表
	public function do_viewloanitem(){
		\Core::view()->set('id',\Core::get('loan_id'))
			->set('lkey',\Core::get('l_key'))
			->load('loan_viewloaditem');
	}
	//导出还款计划
	public function do_repayplan_export(){
		$where = array();
		$deal_id = \Core::get('deal_id');
		$l_key = \Core::get('l_key');
		if($deal_id != null) {
			$where['deal_id'] = $deal_id;
		}
		//贷款名
		$loanName = \Core::dao('loan_loanbase')->getName($deal_id);
		if($l_key != null) {
			$where['l_key'] = $l_key;
			$loanName .= '/第'.($l_key+1).'期';
		}
		//Excel头部
		$header = array();
		$header['第几期'] = 'integer';
		$header['还款日'] = 'date';
		$header['已还总额'] = 'string';
		$header['待还总额'] = 'string';
		$header['待还本息'] = 'string';
		$header['管理费'] = 'string';
		$header['逾期费用'] = 'string';
		$header['逾期管理费'] = 'string';
		$header['还款情况'] = 'string';
		$header['还款时间'] = 'datetime';
		$header['逾期天数'] = 'integer';
		$fields = 'id,deal_id,l_key,repay_time,repay_money,manage_money,true_repay_time,status,has_repay,impose_money,manage_impose_money,true_repay_money,true_manage_money';
		$repayPlanDao = \Core::dao('loan_dealrepay');
		$data = $repayPlanDao->getRepayPlan($where,$fields);
		unset($where);
		//获取普通配置中的罚息利率等配置 loan_ext表的config_common字段
		$loanextDao = \Core::dao('loan_loanext');
		$config_common = $loanextDao->getConfig('config_common');
		$loadrepayDao = \Core::dao('loan_dealloadrepay');
		$loanenumBusiness = \Core::business('loan_loanenum');
		//Excel内容
		foreach ($data as $v) {
			$row = array();
			$overdue_day = 0;
			//判断是否逾期，计算应还金额等
			//是否还款
			if($v['has_repay'] == 1) {
				//已还总额
				$isrepay = $v['true_repay_money'];
				//待还总额
				$repay_all_money = '0.00';
				//待还本息
				$repay_money = '0.00';
				//管理费
				$manage_money = $v['true_manage_money'];
				//逾期/违约金
				$impose_money = $v['impose_money'];
				//逾期/违约金管理费
				$manage_impose_money = $v['manage_impose_money'];
				//还款情况
				$status = $v['status'] + 1;
				$repaydate = date('Y-m-d H:i:s',$v['true_repay_time']);
			}elseif($v['has_repay'] == 0) {
				//未还款状态
				//已还总额
				$isrepay = '0.00';
				//判断是否罚息
				$time = time();
				if($time > ($v['repay_time'] + 24 * 3600 - 1) && $v['repay_money'] > 0){
					$status = 3;
					//计算逾期时间,设置还款状态
					$overdue_day = ceil((strtotime(date('Y-m-d',$time)) - $v['repay_time']) / (3600 * 24));
					//根据日期判断是否严重逾期 获取费率
					//费率修改为拓展表loan_ext中普通配置字段config_common中获取
					if ($overdue_day >= C('YZ_IMPSE_DAY')) {
						$status = 4;
						$impose_fee = trim($config_common['impose_fee_day2']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day2']);
					}else {
						$impose_fee = trim($config_common['impose_fee_day1']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day1']);
					}
					$impose_fee = floatval($impose_fee);
					$manage_impose_fee = floatval($manage_impose_fee);
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = number_format($repay_money * $impose_fee * $overdue_day / 100,2);
					//罚管理费
					$manage_impose_money = number_format($repay_money * $manage_impose_fee * $overdue_day / 100,2);
					$impose_all_money = $impose_money + $manage_impose_money;
					$repay_all_money = $repay_money + $manage_money + $impose_all_money;
					$repaydate = '';
				}else {
					//未逾期
					$status = $v['status'];
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = 0;
					//罚管理费
					$manage_impose_money = 0;
					$repay_all_money = $repay_money + $manage_money;
					$repaydate = '';
				}
			}
			$l_key = $v['l_key'] + 1;
			$row['l_key'] = $l_key;
			$row['repay_time'] = date('Y-m-d',$v['repay_time']);
			//已还总额
			$row['true_repay_money'] = '￥'.$isrepay;

			//待还总额
			$row['need_repay_money'] = '￥'.$repay_all_money;

			//待还本息
			$row['repay_money'] = '￥'.$repay_money;
			//管理费
			$row['manage_money'] = '￥'.$manage_money;
			//逾期/违约金
			$row['impose_money'] = '￥'.$impose_money;
			//逾期/违约金管理费
			$row['manage_impose_money'] = '￥'.$manage_impose_money;
			//还款状态
			$row['repay_status'] = strip_tags($loanenumBusiness->enumLoanRepayType($status));
			//还款时间
			$row['true_repay_time'] = $repaydate;
			//逾期天数
			$row['over_day'] = ($overdue_day?$overdue_day:0);
			$json['rows'][] = $row;
		}
		unset($data);
		//导出
		$this -> log($loanName.'/还款计划列表', 'export');
		exportExcel($loanName.'/还款计划列表', $header, $json['rows']);
		unset($json);
	}
	//获取全部贷款分页JSON数据
	public function do_all_json(){
		//每页显示行数
		$pagesize = \Core::postGet('rp');
		//当前页
		$page = \Core::postGet('curpage');
		//需要获取的字段
		$fields = 'id,name,user_id,borrow_amount,rate,repay_time,loantype,loan_status,sor_code,first_audit_admin_id,repay_time_type,second_audit_admin_id';
		//查询条件
		$where = array();
		$bidwhere = array();
		//排序
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//固定查询条件
		$where['is_delete']=0;
		$where['is_effect']=1;
		$where['publish_wait']=0;
		//简易查询条件
		if (\Core::postGet('query')) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//高级查询条件
		if(\Core::get('id')!=null && is_numeric(\Core::get('id'))){
			$where['id like']="%".\Core::get('id')."%";
		}
		if(\Core::get('name')!=null){
			$where['name like']="%".\Core::get('name')."%";
		}
		if(\Core::get('borrow_amount')!=null && is_numeric(\Core::get('borrow_amount'))){
			$where['borrow_amount like']="%".\Core::get('borrow_amount')."%";
		}
		if(\Core::get('rate')!=null && is_numeric(\Core::get('rate'))){
			$where['rate like']="%".\Core::get('rate')."%";
		}
		if(\Core::get('repay_time')!=null && is_numeric(\Core::get('repay_time'))){
			$where['repay_time like']="%".\Core::get('repay_time')."%";
		}
		if(\Core::get('repay_time_type')!=null && \Core::get('repay_time_type')!='-1'){
			$where['repay_time_type']=\Core::get('repay_time_type');
		}
		if(\Core::get('loantype')!=null && \Core::get('loantype')!='-1'){
			$where['loantype']=\Core::get('loantype');
		}
		if(\Core::get('cate_id')!=null && \Core::get('cate_id')!='-1'){
			$where['cate_id']=\Core::get('cate_id');
		}
		if(\Core::get('use_type')!=null && \Core::get('use_type')!='-1'){
			$where['use_type']=\Core::get('use_type');
		}
		if(\Core::get('sor_code')!=null && \Core::get('sor_code')!='-1'){
			$where['sor_code']=\Core::get('sor_code');
		}
		//贷款状态，转到loan_bid表
		if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
			$bidwhere['deal_status']=\Core::get('deal_status');
			$loanId = \Core::dao('loan_loanbid')->getIds($bidwhere);
			$where['id '] = $loanId;
		}
		//已迁移到loan_bid表 修改为根据流标状态获取贷款id，获取在该id数组中的贷款
		if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
			$bidwhere['is_has_received'] =\Core::get('is_has_received');
			$loanId = \Core::dao('loan_loanbid')->getIds($bidwhere);
			$where['id '] = $loanId;
		}
		if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
			$where['user_id like']="%".\Core::get('user_id')."%";
		}
		$userDao=\Core::dao('user_user');
		//贷款人姓名模糊查询
		if(\Core::get('user_name')!=null && (\Core::get('user_mobile') == null && !is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(real_name_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;
		}
		//TODO贷款人手机查询
		if(\Core::get('user_name') == null && (\Core::get('user_mobile')!=null && is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;

		}
		//手机号和姓名组合
		if(\Core::get('user_name') != null && (\Core::get('user_mobile')!=null && is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(real_name_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchIdsWhere["AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_mobile')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;

		}
		//简易排序条件
		if (\Core::postGet('sortorder')) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		
		$data = \Core::dao('loan_loanbase') -> getFlexPage($page, $pagesize, $fields, $where, $orderby,'id');
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		
		$loanBusiness=\Core::business('loan_loanenum');
		
		//查询用户名称与管理员名称
		$userIds=array();
		$adminFirstIds=array();
		$adminSecondIds=array();
		$loanIds = array();
		$userPids = array();
		$pidNames = array();
		if(!($data['rows'])) {
			echo @json_encode($json);
			exit;
		}
		foreach ($data['rows'] as $v) {
			$userIds[]=$v['user_id'];
			$adminFirstIds[]=$v['first_audit_admin_id'];
			$adminSecondIds[]=$v['second_audit_admin_id'];
			$loanIds[] = $v['id'];
		}

		$adminDao=\Core::dao('sys_admin_admin');
		$loanbidDao = \Core::dao('loan_loanbid');
		$userNames=$userDao->getUser($userIds,'id,user_name,real_name,pid');
		$firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');
		$secondAdminNames=$adminDao->getAdmin($adminSecondIds,'admin_id,admin_name,admin_real_name,admin_mobile');
		$loanInfos = $loanbidDao->getLoan($loanIds,'loan_id,is_has_loans,is_has_received,buy_count,deal_status');

		foreach ($userNames as $v) {
			$userPids[] = $v['pid'];
		}
		$pidNames=$userDao->getUser($userPids,'id,user_name,real_name');

		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";

			if($v['loan_status']>=1)
			{
				$opration.="<li><a href='javascript:loan_repay_plan(".$v['id'].")'>还款计划</a></li>";

			}
			$opration.="<li><a href='javascript:loan_detail(".$v['id'].")'>投标详情</a></li>";
			$opration.="<li><a href='javascript:loan_preview(".$v['id'].")'>预览</a></li>";
			$opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
			$opration.="</ul></span>";
			$row['cell'][] = $opration;
			$row['cell'][] = $v['id'];
			$row['cell'][] = "<a href='javascript:loan_show_edit(".$v['id'].")'>".$v['name']."</a>";
			$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
			$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $pidNames)?\Core::arrayGet(\Core::arrayGet($pidNames, $userNames[$v['user_id']]['pid']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($pidNames, $userNames[$v['user_id']]['pid']),'real_name').')':'';

			$row['cell'][] = "￥".$v['borrow_amount'];
			$row['cell'][] = $v['rate']."%";
			$row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
			$row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
			$row['cell'][] = $loanBusiness->enumDealStatus(\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'deal_status'):1);
			$row['cell'][] = (\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'is_has_loans'):0)?\Core::L('yes'):\Core::L('no');
			$row['cell'][] = (\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'is_has_received'):0)?\Core::L('yes'):\Core::L('no');
			$row['cell'][] = \Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'buy_count'):0;
			$row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);

			$row['cell'][] = \Core::arrayKeyExists($v['first_audit_admin_id'], $firstAdminNames)?\Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name'):($v['first_audit_admin_id']=='-1'?'自动审核':'');
			$row['cell'][] = \Core::arrayKeyExists($v['second_audit_admin_id'], $secondAdminNames)?\Core::arrayGet(\Core::arrayGet($secondAdminNames, $v['second_audit_admin_id'],''),'admin_real_name'):'';
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
	//获取还款计划JSON数据
	public function do_all_repay_plan_json(){
		$where = array();
		$deal_id = \Core::get('loan_id');
		$where['deal_id'] = $deal_id;
		$repayPlanDao = \Core::dao('loan_dealrepay');
		$data = $repayPlanDao->getRepayPlan($where,'*');
		unset($where);
		//处理返回结果
		$json = array();
		$json['total'] = 0;
		if(!($data)) {
			echo @json_encode($json);
			exit;
		}
		//获取普通配置中的罚息利率等配置 loan_ext表的config_common字段
		$loanextDao = \Core::dao('loan_loanext');
		$config_common = $loanextDao->getConfig('config_common');
		$loadrepayDao = \Core::dao('loan_dealloadrepay');
		$loanenumBusiness = \Core::business('loan_loanenum');
		foreach ($data as $v) {
			$row = array();
			$overdue_day = 0;
			//判断是否逾期，计算应还金额等
			//是否还款
			if($v['has_repay'] == 1) {
				//已还总额
				$isrepay = $v['true_repay_money'];
				//待还总额
				$repay_all_money = '0.00';
				$need_repay_money = '0.00';
				//待还本息
				$repay_money = '0.00';
				//管理费
				$manage_money = $v['true_manage_money'];
				//逾期/违约金
				$impose_money = $v['impose_money'];
				//逾期/违约金管理费
				$manage_impose_money = $v['manage_impose_money'];
				//还款情况
				$status = $v['status'] + 1;
				$repaydate = date('Y-m-d H:i:s',$v['true_repay_time']);
			}elseif($v['has_repay'] == 0) {
				//未还款状态
				//已还总额
				$isrepay = '0.00';
				//判断是否罚息
				$time = time();
				if($time > ($v['repay_time'] + 24 * 3600 - 1) && $v['repay_money'] > 0){
					$status = 3;
					//计算逾期时间,设置还款状态
					$overdue_day = ceil((strtotime(date('Y-m-d',$time)) - $v['repay_time']) / (3600 * 24));
					//根据日期判断是否严重逾期 获取费率
					//费率修改为拓展表loan_ext中普通配置字段config_common中获取
					if ($overdue_day >= C('YZ_IMPSE_DAY')) {
						$status = 4;
						$impose_fee = trim($config_common['impose_fee_day2']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day2']);
					}else {
						$impose_fee = trim($config_common['impose_fee_day1']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day1']);
					}
					$impose_fee = floatval($impose_fee);
					$manage_impose_fee = floatval($manage_impose_fee);
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = number_format($repay_money * $impose_fee * $overdue_day / 100,2);
					//罚管理费
					$manage_impose_money = number_format($repay_money * $manage_impose_fee * $overdue_day / 100,2);
					$impose_all_money = $impose_money + $manage_impose_money;
					$need_repay_money = $repay_all_money = $repay_money + $manage_money + $impose_all_money;
					$repaydate = '';
				}else {
					//未逾期
					$status = $v['status'];
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = 0;
					//罚管理费
					$manage_impose_money = 0;
					$need_repay_money = $repay_all_money = $repay_money + $manage_money;
					$repaydate = '';
				}
			}
			$opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
			if($v['has_repay'] == 0) {
				$opration.="<li><a href='javascript:manual_repay(".$v['deal_id'].",".$v['l_key'].",".$repay_all_money.")'>手动还款</a></li>";
			}
			//网站资金代还判断 deal_load_repay表is_site_repay字段
			$where = array();
			$where['deal_id'] = $v['deal_id'];
			$where['l_key'] = $v['l_key'];
			if($loadrepayDao->getIsSiteRepay($where) == 0){
				$opration.="<li><a href='javascript:site_repay(".$v['deal_id'].")'>网站资金代还款</a></li>";
			}
			$opration.="<li><a href='javascript:repay_plan_export_load(".$v['deal_id'].",".$v['l_key'].")'>导出还款计划列表</a></li>";
			$opration.="</ul></span>";
			$row['cell'][] = $opration;
			$l_key = $v['l_key'] + 1;
			$row['cell'][] = '第'  .$l_key.'期';
			$row['cell'][] = date('Y-m-d',$v['repay_time']);
			//已还总额
			$row['cell'][] = '￥'.$isrepay;
			//待还总额
			$row['cell'][] = '￥'.$repay_all_money;
			//还需还金额
			$row['cell'][] = '￥'.$need_repay_money;
			//待还本息
			$row['cell'][] = '￥'.$repay_money;
			//管理费
			$row['cell'][] = '￥'.$manage_money;
			//逾期/违约金
			$row['cell'][] = '￥'.$impose_money;
			//逾期/违约金管理费
			$row['cell'][] = '￥'.$manage_impose_money;
			//还款状态
			$row['cell'][] = $loanenumBusiness->enumLoanRepayType($status);
			//还款时间
			$row['cell'][] = $repaydate;
			//逾期天数
			$row['cell'][] = $overdue_day?$overdue_day:0;
			$row['cell'][] = '<a href="javascript:viewloanitem('.$v['deal_id'].','.$v['l_key'].');" >查看</a>';
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		$json['total'] =count($data);
		unset($data);
		echo @json_encode($json);

	}
	//投资人列表
	public function do_all_loaditem_json(){
		$json = array();
		$id = \Core::get('id',0);
		if(!$id){
			echo @json_encode($json);
			exit();
		}
		$lkey = \Core::get('lkey',0);
		if($lkey == null){
			echo @json_encode($json);
			exit();
		}
		$fields = 'id,deal_id,l_key,user_id,status,is_site_repay,has_repay,impose_money,repay_money,repay_time,manage_money,interest_money,true_reward_money,t_user_id,true_manage_money,manage_interest_money,true_manage_interest_money,manage_interest_money_rebate,true_manage_interest_money_rebate,manage_early_interest_money';
		//获取投资列表
		$data = \Core::dao('loan_dealloadrepay')->getLoadRepayByLkey($id,$lkey,$fields);
		if($data) {
			//借款人是否还款
			$realrepay = \Core::dao('loan_dealrepay')->getRepayStstus($id,$lkey);
			//当前时间
			$now_time = time();
			//获取普通配置中的罚息利率等配置 loan_ext表的config_common字段
			$config_common = \Core::dao('loan_loanext')->getConfig('config_common');
			//用户名称
			foreach ($data as $v) {
				$userIds[]=$v['user_id'];
				$tuserIds[]=$v['t_user_id'];
			}
			$userNames=\Core::dao('user_user')->getUser($userIds,'id,user_name,real_name,pid');
			$tuserNames=\Core::dao('user_user')->getUser($tuserIds,'id,user_name,real_name,pid');
			$loanenumBusiness=\Core::business('loan_loanenum');
			//\Core::dump($tuserNames);die();
			foreach ($data as $v) {
				$row = array();
				//TODO 1.未收到还款或借款用户未还款 2.已收到还款
				if($v['has_repay'] == 0 || $realrepay == 0) {
					$status = $v['status'];
					$impose_money = 0;
					$site_repay = "";
					$realrepaymoney = 0;
					//判断是否逾期
					if($now_time > ($v['repay_time'] + 24 * 3600 - 1)) {

						$time_span = strtotime(date("Y-m-d",$now_time));
						$next_time_span = $v['repay_time'];
						$impose_day = $day = ceil(($time_span - $next_time_span) / 24 / 3600);
						//是否严重逾期
						if($impose_day > C('YZ_IMPSE_DAY')) {
							$status = 4;
							$impose_fee = trim($config_common['impose_fee_day2']);
						}else {
							$status = 3;
							$impose_fee = trim($config_common['impose_fee_day1']);
						}
						$impose_fee = floatval($impose_fee);
						//罚息/违约金
						$impose_money = number_format($v['repay_money'] * $impose_fee * $impose_day / 100,2);

					}
				}else {
					$status = $v['status'] + 1;
					$impose_money = $v['impose_money'];
					if ($v['is_site_repay'] == 0) {
						$site_repay = "会员";
					} elseif ($v['is_site_repay'] == 1) {
						$site_repay = "网站";
					} elseif ($v['is_site_repay'] == 2) {
						$site_repay = "机构";
					}
					$realrepaymoney =  $v['true_reward_money'];
				}
				//借款id
				$row['cell'][] = $v['deal_id'];
				//会员
				$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name'):'';
				//承接人
				$row['cell'][] = \Core::arrayKeyExists($v['t_user_id'], $tuserNames)?\Core::arrayGet(\Core::arrayGet($tuserNames, $v['t_user_id']),'user_name'):'';
				//还款金额
				$row['cell'][] = '￥'.$v['repay_money'];
				//管理费
				$row['cell'][] = '￥'.$v['manage_money'];
				//利息管理费
				$row['cell'][] = '￥'.$v['manage_interest_money'];
				//提前还款利息管理费
				$row['cell'][] = '￥'.$v['manage_early_interest_money'];
				//逾期/违约金
				$row['cell'][] = '￥'.$impose_money;
				//预期收益
				$row['cell'][] = '￥'.$v['interest_money'];
				//实际收益
				$row['cell'][] = '￥'.$realrepaymoney;
				//还款状态
				$row['cell'][] = $loanenumBusiness->enumLoanRepayType($status);
				//还款人
				$row['cell'][] = $site_repay;
				$row['cell'][] = '';
				$json['rows'][] = $row;
			}
		}
		echo @json_encode($json);
	}
	//导出投资人列表
	public function do_bidlist_export(){
		$where = array();
		$deal_id = \Core::get('loan_id');
		if($deal_id != null) {
			$where['deal_id'] = $deal_id;
		}
		//Excel头部
		$header = array();
		$header['贷款编号'] = 'integer';
		$header['投标人'] = 'string';
		$header['投标金额'] = 'string';
		$header['投标时间'] = 'datetime';
		$header['流标返还'] = 'string';
		$header['是否转账'] = 'string';
		$header['转账备注'] = 'string';
		//获取数据
		$fields = 'deal_id,user_name,money,create_time,is_has_loans,is_repay,msg';
		$data = \Core::dao('loan_dealload')->getList($where,$fields);
		if($data){
			foreach ($data as $k=>$v) {
				//是否转账
				$data[$k]['is_has_loans'] = $v['is_has_loans']?'已转账':'未转账';
				//流标返还
				$data[$k]['is_repay'] = $v['is_repay']?'已返还':'无返还';
				//投标时间
				$data[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
			}
		}
		$this -> log('投标列表', 'export');
		exportExcel('投标列表', $header, $data);
		unset($where);
		unset($data);
	}
}
