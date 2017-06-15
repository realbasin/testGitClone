<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 贷款各种类型的列表逻辑
 * 数据库类型列表默认使用缓存
 * 任意类型表在增删改后需要手动清除缓存
 * 如没有清除缓存，则需要在后台手动清除缓存才能同步
 * 按照严格要求，所有文字都必须使用语言库，如果任意时候都只有一种语言，则无需语言库
 */
class  business_loan_loanenum extends Business {
		/*
		 * 还款类型
		 * 0等额本息 1先息后本 2到期本息 3等额本金
		 */
		public function enumLoanType($loantype=''){
			$loanTypeArr=array('0'=>\Core::L('average_capital_plus_interest'),
			'1'=>\Core::L('each_interest_principal_due_time'),
			'2'=>\Core::L('repayment_at_maturity'),
			'3'=>'等额本金'
			);
			return ($loantype!='')?\Core::arrayGet($loanTypeArr, $loantype,''):$loanTypeArr;
		}
		
		/*
		 * 贷款状态
		 */
		public function enumDealStatus($status=''){
			$delStatusArr=array();
			$delStatusArr['0']=\Core::L('waiting_files');
			$delStatusArr['1']=\Core::L('invite_tenders');
			$delStatusArr['2']=\Core::L('tender_full');
			$delStatusArr['3']=\Core::L('tender_failure');
			$delStatusArr['4']=\Core::L('repayment');
			$delStatusArr['5']=\Core::L('pay_off');
			$delStatusArr['6']='已过期';
			$delStatusArr['12']='有逾期';
			$delStatusArr['13']='无逾期';
			$delStatusArr['14']='待垫付';
			$delStatusArr['15']='已垫付';
			$delStatusArr['17']='满标(待放款)';
			$delStatusArr['18']='满标(已放款)';
			return ($status!='')?\Core::arrayGet($delStatusArr, $status,''):$delStatusArr;
		}
		
		/*
		 * 客户端来源
		 */
		public function enumSorCode($sorcode=''){
			$sorcodeList=\Core::cache()->get('sor_code');
			if(!$sorcodeList){
				$sorcodeDao=\Core::dao('loan_sorcode');
			    $sorcodeList=$sorcodeDao->getSorList();
				if($sorcodeList){
					\Core::cache()->set('sor_code',$sorcodeList);
				}
			}
			return $sorcode?(\Core::arrayKeyExists($sorcode, $sorcodeList)?\Core::arrayGet(\Core::arrayGet($sorcodeList, $sorcode),'code_name'):''):$sorcodeList;
		}
		
		//还款天/月
		public function enumRepayTimeType($repaytimetype=''){
			$rTimeType=array();
			$rTimeType['0']=\Core::L('repay_time_type_day');
			$rTimeType['1']=\Core::L('repay_time_type_month');
			return ($repaytimetype!='')?\Core::arrayGet($rTimeType, $repaytimetype,''):$rTimeType;
		}
		
		//放标类型
		public function enumDealCate($dealcate=''){
			$dealCateList=\Core::cache()->get('deal_cate');
			if(!$dealCateList){
				$dealCateDao=\Core::dao('loan_dealcate');
				$dealCateList=$dealCateDao->getAllDealCate();
				if($dealCateList){
					\Core::cache()->set('deal_cate',$dealCateList);
				}
			}
			return $dealcate?\Core::arrayGet(\Core::arrayGet($dealCateList, $dealcate,''),'name',''):$dealCateList;
		}

		//贷款用途
		public function enumDealUseType($usetype=''){
			$useTypeList=\Core::cache()->get('deal_use_type');
			if(!$useTypeList){
				$dealUseTypeDao=\Core::dao('loan_dealusetype');
				$useTypeList=$dealUseTypeDao->getAllDealUseType();
				if($useTypeList){
					\Core::cache()->set('deal_use_type',$useTypeList);
				}
			}
			return $usetype?\Core::arrayGet(\Core::arrayGet($useTypeList, $usetype,''),'name',''):$useTypeList;
		}
	
		//资金源类型
		public function enumDealFundType($fundtype='') {
			$fundTypeList=\Core::cache()->get('deal_fund_type');
			if(!$fundTypeList){
				$dealFundTypeDao=\Core::dao('loan_dealfundtype');
				$fundTypeList=$dealFundTypeDao->getAllDealFundType();
				if($fundTypeList){
					\Core::cache()->set('deal_fund_type',$fundTypeList);
				}
			}
			return $fundtype?\Core::arrayGet(\Core::arrayGet($fundTypeList, $fundtype,''),'name',''):$fundTypeList;
		}
		
		//贷款类型
		public function enumDealLoanType($dealloantype=''){
			$dealLoanTypeList=\Core::cache()->get('deal_loan_type');
			if(!$dealLoanTypeList){
				$dealLoanTypeDao=\Core::dao('loan_dealloantype');
				$dealLoanTypeList=$dealLoanTypeDao->getDealLoanTypes('id,name');
				if($dealLoanTypeList){
					\Core::cache()->set('deal_loan_type',$dealLoanTypeList);
				}
			}
			return $dealloantype?\Core::arrayGet(\Core::arrayGet($dealLoanTypeList, $dealloantype,''),'name',''):$dealLoanTypeList;
		}
		//还款状态
		public function enumLoanRepayType($status=''){
			$repayType=array();
			$repayType['0']=\Core::L('loan_repay_stay');
			$repayType['1']=\Core::L('loan_repay_advanced');
			$repayType['2']=\Core::L('loan_repay_nomal');
			$repayType['3']=\Core::L('loan_repay_overdue');
			$repayType['4']=\Core::L('loan_repay_serious_overdue');
			$repayType['5']=\Core::L('loan_repay_overdue_nopay');
			$repayType['6']=\Core::L('loan_repay_serious_overdue_nopay');
			return ($status!=='')?\Core::arrayGet($repayType, $status,''):$repayType;
		}
		//手动单期还款
		public function repayLoanBills($id,$l_key,$user_id){
			$id = intval($id);
			$l_key = intval($l_key);
			$user_id = intval($user_id);
			$root = array();
			$root['status'] = 0;//0:出错;1:正确;
			if ($id == 0) {
				$root['show_err'] = '操作失败！';
				return $root;
			}
			if ($user_id <= 0) {
				$root['show_err'] = '用户不存在！';
				return $root;
			}
			$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
			$dealRepayDao = \Core::dao('loan_dealrepay');
			$dealLoadDao = \Core::dao('loan_dealload');
			$loanbaseDao = \Core::dao('loan_loanbase');
			$loanextDao = \Core::dao('loan_loanext');
			$userDao = \Core::dao('user_user');
			$userBusiness = \Core::business('user_userinfo');
			$dealLoadRepayBusiness = \Core::business('sys_dealloadrepay');
			//单期
			//获取该借款的投资用户列表
			$load_user_list = $dealLoadDao->getList(array('deal_id'=>$id),'id,deal_id,user_id,money');
			if(!$load_user_list) {
				$root['show_err'] = '投资不存在';
				return $root;
			}
			$invest_user_id = 0;
			$getManage = $dealRepayDao->isGetManage($id,$l_key,$user_id);
			//借款用户还款计划
			$user_repay = $dealRepayDao->getOneRepayPlan($id,$l_key,'*');
			if(!$user_repay) {
				$root['show_err'] = '还款计划不存在';
				return $root;
			}
			//判断是否逾期
			$userRepayImposeInfo = \Core::business('sys_dealrepay')->repayPlanImpose($id,$l_key);
			$need_repay_money = $userRepayImposeInfo['need_repay_money'];
			$status = ($userRepayImposeInfo['status']<2)?($userRepayImposeInfo['status']+1):$userRepayImposeInfo['status'];
			$impose_money = $userRepayImposeInfo['impose_money'];
			$manage_impose_money  = $userRepayImposeInfo['manage_impose_money'];
			//当前用户余额
			$user_total_money = $userDao->getUserMoney($user_id);
			if ($user_total_money < $need_repay_money) {
				$root['show_err'] = '余额不足，还款还需'.$need_repay_money-$user_total_money.'，请先充值';
				return $root;
			}else {
				//进行还款系列操作 启用事务
				$userDao->getDb()->begin();
				try{
					//1.投资人回款
					foreach ($load_user_list as $v) {
						//获取所有投资用户该期的回款计划
						$user_load = $dealLoadRepayDao->getSomeOneLkeyPlan($id,$l_key,$v['user_id']);
						//TODO 网站代还 或已收到回款
						if($user_load['is_site_repay'] == 1 || $user_load['has_repay'] == 1){
							continue;
						}
						$update_status = $dealLoadRepayBusiness->updateLoadRepayPlan($user_load,$v['money'],$status,$impose_money,$manage_impose_money);
						if($update_status === false) {
							$root['show_err'] = '回款修改失败';
							return $root;
						}else {
							//更新成功，修改相关投资人余额等
							//是否有转让
							if ($user_load['t_user_id'] != 0) {
								$invest_user_id = $v['t_user_id'];
								$log_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($l_key + 1) . '期，回报本息';
							} else {
								$invest_user_id = $v['user_id'];
								$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($l_key + 1) . '期，回报本息';
							}
							//修投资人余额
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['repay_money'], $log_msg, 5);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，回报本息发放失败';
							}
							if ($user_load['manage_money'] > 0) {
								$log_msg = '[<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>]第' . ($l_key + 1) . '期，投标管理费';
								$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_money'], $log_msg, 20);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，扣除投标管理费失败';
								}
							}
							if ($user_load['manage_interest_money'] > 0) {
								$log_msg = '[<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>]第' . ($l_key + 1) . '期，投标利息管理费';
								$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_interest_money'], $log_msg, 20);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，扣除投标利息管理费失败';
								}
							}
							//逾期罚息
							if (($impose_money*($user_load['repay_money']/$v['money'])) != 0) {
								if ($user_load['t_user_id'] == 0) { //无债权转让
									$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($l_key + 1) . '期，逾期罚息';
									$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_msg, 21);
									if($editMoneyStatus === false){
										$root['show_err'] = '回款失败，逾期罚息发放失败';
									}
								} else {//有债权转让
									$log_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($l_key + 1) . '期，逾期罚息';
									$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_msg, 21);
									if($editMoneyStatus === false){
										$root['show_err'] = '回款失败，逾期罚息发放失败';
									}
								}
							}
							//投资者奖励
							if ($user_load['reward_money'] != 0) {
								$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($l_key + 1) . '期，奖励收益';
								$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['reward_money'], $log_msg, 28);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，奖励收益发放失败';
								}
							}
							//TODO 普通会员邀请返利
							//投资者返佣金
							if ($user_load['manage_interest_money_rebate'] != 0) {
								//是否有上级，有上级则给上级返佣
								$rebate_user = $userDao->getUser($invest_user_id, 'id,pid');
								if ($rebate_user[$invest_user_id]['pid'] != 0) {
									$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($l_key + 1) . '期，返佣金';
									$editMoneyStatus = $userBusiness->editUserMoney($rebate_user[$invest_user_id]['pid'], $user_load['manage_interest_money_rebate'], $log_msg, 23);
									if($editMoneyStatus === false){
										$root['show_err'] = '回款失败，返佣金发放失败';
									}
								}
							}
							//TODO 短信通知回款
						}
					}
					//2.借款人扣款
					$dealRepayLogBusiness = \Core::business('loan_dealrepaylog');
					$dealRepayBusiness = \Core::business('sys_dealrepay');
					//判断当前期是否还款完毕
					$no_repay_count = $dealLoadRepayBusiness->isRepayedByLkey($id,$l_key);
					$ext_str = "";
					if($no_repay_count == 0) {
						$hasRepayTotal = $dealLoadRepayDao->getHasRepayTotal($id,$l_key);
						//\Core::dump($hasRepayTotal);die();
						//整合借款人更新数据

						//TODO 修改借款者还款计划信息
						$dealRepayStatus = $dealRepayBusiness->updateRepayPlan($hasRepayTotal,$impose_money,$manage_impose_money,$getManage,$status);
						if($dealRepayStatus === false) {
							$root['show_err'] = '还款失败，修改还款计划失败';
							return $root;
						}
						//借款人扣款
						$log_repay_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，偿还本息'.$ext_str;
						$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$hasRepayTotal['total_repay_money'],$log_repay_msg,4);
						//记录还款日志
						$repay_msg = '会员还款，本息：' . $hasRepayTotal['total_repay_money'];
						$dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						//罚息
						if($hasRepayTotal['total_impose_money'] != 0) {
							$log_impose_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，逾期罚息'.$ext_str;
							$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$hasRepayTotal['total_impose_money'],$log_impose_msg,11);
							$repay_msg = '会员还款，逾期费用：' . $hasRepayTotal['total_impose_money'];
							$dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						}
						//借款管理费
						if ($user_repay['manage_money'] > 0 && $getManage == 0) {
							$log_manage_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，借款管理费'.$ext_str;
							$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$user_repay['manage_money'],$log_manage_msg,10);
							$repay_msg = '会员还款，管理费：' . $user_repay['manage_money'];
							$dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						}
						//抵押物管理费
						if($user_repay['mortgage_fee'] > 0 ) {
							$log_mortgage_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，抵押物管理费'.$ext_str;
							$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$user_repay['mortgage_fee'],$log_mortgage_msg,27);
							$repay_msg = '会员还款，抵押物管理费：' . $user_repay['mortgage_fee'];
							$dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						}
						//逾期管理费
						if($manage_impose_money > 0) {
							$log_impose_manage_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，逾期管理费'.$ext_str;
							$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$manage_impose_money,$log_impose_manage_msg,12);
							$repay_msg = '会员还款，逾期管理费：' . $manage_impose_money;
							$dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						}
						//逾期扣除信用积分point
						if($status == 3 ) {
							//严重逾期
							$log_impose_point_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，严重逾期'.$ext_str;
							$point = C('YZ_IMPOSE_POINT');
							$editMoneyStatus = $userBusiness->editUserPoint($user_id,-$point,$log_impose_point_msg,11);

						}elseif($status == 2) {
							//普通逾期
							$log_impose_point_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($l_key+1).'期，逾期还款'.$ext_str;
							$point = C('IMPOSE_POINT')?C('IMPOSE_POINT'):10;
							$editMoneyStatus = $userBusiness->editUserPoint($user_id,-$point,$log_impose_point_msg,11);
						}
						//借款者返佣
						$true_manage_money_rebate = $user_repay['manage_money'] * floatval(C('BORROWER_COMMISSION_RATIO')) / 100;
						if($true_manage_money_rebate != 0 ) {
							//是否有上级，有上级则给上级返佣
							$rebate_user = $userDao->getUser($user_id,'id,pid');
							if($rebate_user[$invest_user_id]['pid'] != 0) {
								$log_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($v['l_key']+1).'期，返佣金';
								$editMoneyStatus = $userBusiness->editUserMoney($rebate_user[$invest_user_id]['pid'],$true_manage_money_rebate,$log_msg, 23);
							}
						}
						//TODO 修改代还款表信息
						//\Core::dao('loan_generationrepay')->update(array('status'=>1),array('deal_id'=>$id,'repay_id'=>$user_repay['id']));
						$notices['has_next_loan'] = 0;
						//下一期还款|**没有下一期是否代表已还最后一期**|
						$next_loan = $dealRepayDao->getNextLoan($id,$l_key);
						if($next_loan) {
							$notices['has_next_loan'] = 1;
							$notices['next_repay_time'] = date("Y年m月d日",$next_loan['repay_time']);
							$notices['next_repay_money'] = number_format($next_loan['repay_money'], 2);
						}
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，修改余额失败';
						}else {
							//判断是否最后一期还款
							//全部还清
							$bid_no_repay = $dealRepayDao->getAllNoRepay($id);
							if($bid_no_repay == 0) {
								$bidflag = \Core::dao('loan_loanbid')->update(array('deal_status'=>5,'pay_off_status'=>1),array('loan_id'=>$id));
								if($bidflag === false){
									$root['show_err'] = '还款失败';
								}
								//TODO 用户获得信用
								//判断获取的信用是否超过限制
								//TODO 用户获得额度
							}
							$root['show_err'] = '还款成功';
							$root['status'] = 1;
						}

					}else {
						//部分还款
						$notices['repay_status'] = "本期部分还款";
						$notices['left_user_count'] = $no_repay_count;
						$updateData = array();
						$updateWhere = array();
						$updateData['has_repay'] = 2;
						$updateWhere['deal_id'] = $id;
						$updateWhere['l_key'] = $l_key;
						$updateStatus = $dealRepayDao->update($updateData,$updateWhere);
						if($updateStatus === false) {
							$root['show_err'] = '部分还款失败，修改还款计划状态失败';
						}else{
							$root['show_err'] = '部分还款成功';
							$root['status'] = 1;
						}
					}
				}catch (\Exception $e){
					$root['show_err'] = '系统错误';
					return $root;
				}finally{
					if($root['status'] == 1) {
						$userDao->getDb()->commit();
						return $root;
					}else {
						$userDao->getDb()->rollback();
						return $root;
					}
				}
			}

		}
		//手动多期提前还款
		public function repayAllLoanBills($id,$user_id){
			$id = intval($id);
			$root = array();
			$root['status'] = 0;//0:出错;1:正确;
			if ($id == 0) {
				$root['show_err'] = '操作失败！';
				return $root;
			}
			$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
			$dealRepayDao = \Core::dao('loan_dealrepay');
			$dealLoadDao = \Core::dao('loan_dealload');
			$loanBaseDao = \Core::dao('loan_loanbase');
			$loanBidDao = \Core::dao('loan_loanbid');
			$loanextDao = \Core::dao('loan_loanext');
			$dealInrepayDao = \Core::dao('loan_dealinrepayrepay');
			$load_user_list = $dealLoadDao->getList(array('deal_id'=>$id),'id,deal_id,user_id,money,is_winning,income_type,income_value');
			if(!$load_user_list) {
				$root['show_err'] = '投资不存在';
				return $root;
			}
			//借款用户还款计划
			$user_repay = $dealRepayDao->getAllNoRepayLoan($id);
			if(!$user_repay) {
				$root['show_err'] = '还款计划不存在';
				return $root;
			}
			//获取要提前还的首期
			$start_lkey = $dealRepayDao->findCol('l_key',array('deal_id'=>$id,'has_repay'=>0));
			$start_repay_time = $dealLoadRepayDao->findCol('repay_time',array('deal_id'=>$id,'l_key'=>$start_lkey));
			//贷款基本信息
			$loan = $loanBaseDao->getloanbase($id,'id,borrow_amount,rate,repay_time,loantype');
			//贷款基本配置信息
			$comon_config = $loanextDao->getCommonconfig($id);
			if($comon_config){
				unserialize($comon_config);
				$user_loan_early_interest_manage_fee = \Core::arrayKeyExists('user_loan_early_interest_manage_fee',$comon_config)?\Core::arrayGet($comon_config,'user_loan_early_interest_manage_fee'):1;
			}
			$time = time();

			//开启事务
			$loanBaseDao->getDb()->begin();
			try{
				//1.投资人回款
				foreach ($load_user_list as $v) {
					$loan['borrow_amount'] = $v['money'];
					$user_inrepay_info = \Core::business('sys_dealrepay')->inrepayRepay($loan, $start_lkey);
					unset($user_inrepay_info['true_manage_money_rebate']);
					$user_inrepay_info['true_interest_money'] = $user_inrepay_info['true_repay_money'] - $user_inrepay_info['true_self_money'];
					$user_load_data = array();
					$user_inrepay_info['true_repay_time'] = $user_load_data['true_repay_time'] = $time;
					$user_inrepay_info['is_site_repay'] = $user_load_data['is_site_repay'] = 0;
					$user_load_data['status'] = 0;
					$user_inrepay_info['has_repay'] = $user_load_data['has_repay'] = 1;
					//计算提前还款利息管理费，利息*管理费率（且当提前首期为当月期时，不计入提前还款,当前第一期预计还款时间与当前时间对比）
					//先判断当前期是否为当天还款
					if ($start_repay_time == strtotime(date('Y-m-d',$time))) {
						//需要计算利息管理费的起始期数
						$need_interest_money_lkey = $start_lkey + 1;
						//显示为正常还款
						$user_inrepay_info['status'] = 1;
					}else {
						$need_interest_money_lkey = $start_lkey;
						//显示为提前还款
						$user_inrepay_info['status'] = 0;
					}
					//获取要收取利息管理费的利息金额
					$need_interest_money = $dealLoadRepayDao->getAllInterest($id,$need_interest_money_lkey,$v['user_id']);
					//计算利息管理费
					$user_inrepay_info['true_manage_early_interest_money'] = $user_inrepay_info['manage_early_interest_money'] = round(floatval($need_interest_money['total_interest_money'])*floatval($user_loan_early_interest_manage_fee)/100,2);
					//计算投资奖励
					$user_inrepay_info['true_reward_money'] = 0;
					if ((int)$v['is_winning'] == 1 && (int)$v['income_type'] == 2 && (float)$v['income_value'] != 0) {
						$user_inrepay_info['true_reward_money'] = round($need_interest_money * (float)$v['income_value'] * 0.01, 2);
					}
					$dealload_status = $dealLoadRepayDao->update($user_inrepay_info,array('deal_id'=>$v['deal_id'],'l_key'=>$start_lkey,'user_id'=>$v['user_id']));
					if($dealload_status === false) {
						$root['show_err'] = '回款失败，修改提前期回款数据失败';
						return $root;
					}
					$where_after_inrepay = array();
					$where_after_inrepay['deal_id'] = $v['deal_id'];
					$where_after_inrepay['user_id'] = $v['user_id'];
					$where_after_inrepay['l_key >'] = $start_lkey;
					$dealload_status = $dealLoadRepayDao->update($user_load_data,$where_after_inrepay);
					if($dealload_status === false) {
						$root['show_err'] = '回款失败，修改提前期之后回款数据失败';
						return $root;
					}
					//修改loanbid表中的pay_off_status为1,表示所有投资人已回款
					$loanbid_status = $loanBidDao->update(array('pay_off_status'=>1),array('loan_id'=>$id));
					if($dealload_status === false) {
						$root['show_err'] = '回款失败，修改投资人已回款状态失败';
						return $root;
					}
					//TODO 投资人资金变动
					$repay_user_id = $v['user_id'];
					//判断是否有转标
					$log_msg = '回报本息';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,round($user_inrepay_info['true_repay_money'],2),$log_msg,5);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！发放回报本息';
					}
					if($user_inrepay_info['impose_money'] >0 ) {
						$log_msg = '提前回收违约金';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,round($user_inrepay_info['impose_money'],2),$log_msg,7);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！提前回收违约金';
							return $root;
						}
					}

					$log_msg = '投标管理费';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_money'],2),$log_msg,20);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除投标管理费出错';
						return $root;
					}
					if($user_inrepay_info['true_reward_money'] > 0){
						$log_msg = '投标奖励';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,-round($user_inrepay_info['true_reward_money'],2),$log_msg,28);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除投标奖励出错';
							return $root;
						}
					}

					if ($user_inrepay_info['true_manage_early_interest_money'] > 0) {
						$log_msg = '提前还款利息管理费';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_early_interest_money'],2),$log_msg,7);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除提前还款利息管理费出错';
							return $root;
						}
					}
					if($user_inrepay_info['true_manage_interest_money'] > 0) {
						$log_msg = '投标利息管理费';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_interest_money'],2),$log_msg,28);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除投标奖励出错';
							return $root;
						}
					}
					//TODO 投资者返佣
					//TODO 发送通知短信、邮件、站内信
				}
				//2.借款人扣款
				//判断回款计划是否修改已还完
				$no_repay_count = $dealLoadRepayDao->getNoRepayCountByDealId($id);
				if($no_repay_count == 0) {
					//全部回款
					//贷款基本信息
					$loan = $loanBaseDao->getloanbase($id,'id,borrow_amount,rate,repay_time,loantype');
					//提前第一期数据
					$inrepay_info = \Core::business('sys_dealrepay')->inrepayRepay($loan, $start_lkey);
					unset($inrepay_info['true_manage_interest_money']);
					//整理更新数据
					//还款计划信息，（提前非第一期）
					$repay_data = array();
					$repay_data['has_repay'] = $inrepay_info['has_repay'] = 1;
					$repay_data['true_repay_time'] = $inrepay_info['true_repay_time'] = $time;
					$repay_data['status'] = 0;
					//提前还款表数据
					$inrepay_date = array();
					$inrepay_data['deal_id'] = $id;
					$inrepay_data['user_id'] = $user_id;
					$inrepay_data['repay_money'] = $inrepay_info['true_repay_money'];
					$inrepay_data['self_money'] = $inrepay_info['true_self_money'];
					$inrepay_data['manage_money'] = $inrepay_info['true_manage_money'];
					$inrepay_data['mortgage_fee'] = $inrepay_info['true_mortgage_fee'];
					$inrepay_data['repay_time'] = $start_repay_time;
					$inrepay_data['true_repay_time'] = $time;
					//先判断当前期是否为当天还款
					if ($start_repay_time == strtotime(date('Y-m-d',$time))) {
						//显示为正常还款
						$inrepay_info['status'] = 1;
					}else {
						//显示为提前还款
						$inrepay_info['status'] = 0;
					}
					//更新还款计划,添加提前还款表
					//当前提前第一期
					$repay_status = $dealRepayDao->update($inrepay_info,array('deal_id'=>$id,'user_id'=>$user_id,'l_key'=>$start_lkey));
					if($repay_status === false) {
						$root['show_err'] = '修改当前期数据失败';
						return $root;
					}
					//第一期之后的期数
					$where_after_start_lkey = array();
					$where_after_start_lkey['deal_id'] = $id;
					$where_after_start_lkey['user_id'] = $user_id;
					$where_after_start_lkey['l_key > '] = $start_lkey;
					$repay_status = $dealRepayDao->update($repay_data,$where_after_start_lkey);
					if($repay_status === false) {
						$root['show_err'] = '修改后续期数据失败';
						return $root;
					}
					$insert_inrepay = $dealInrepayDao->insert($inrepay_data);
					if($insert_inrepay === false) {
						$root['show_err'] = '添加提前还款数据失败';
						return $root;
					}
					//数据修改成功，扣除借款人资金
					//提前还款违约金
					if($inrepay_info['impose_money'] >0 ) {
						$log_msg = '提前还款违约金';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['impose_money'],2),$log_msg,6);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除提前还款违约金失败';
							return $root;
						}
					}
					//提前还款管理费
					if($inrepay_info['true_manage_money'] >0 ) {
						$log_msg = '提前还款管理费';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_manage_money'],2),$log_msg,10);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除提前还款管理费失败';
							return $root;
						}
					}
					//提前还款抵押物管理费
					if($inrepay_info['true_mortgage_fee'] >0 ) {
						$log_msg = '提前还款抵押物管理费';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_mortgage_fee'],2),$log_msg,27);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除提前还款抵押物管理费失败';
							return $root;
						}
					}
					//提前还款本息
					if($inrepay_info['true_repay_money'] >0 ) {
						$log_msg = '提前还款本息';
						$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_repay_money'],2),$log_msg,6);
						if($edit_user_money === false){
							$root['show_err'] = '还款失败！扣除提前还款本息失败';
							return $root;
						}
					}
					$update_deal_status = \Core::dao('loan_loanbid')->update(array('deal_status'=>5),array('loan_id'=>$id));
					if($update_deal_status === false) {
						$root['show_err'] = '更新贷款状态出错！';
						return $root;
					}else {
						$root['status'] = 1;
						$root['show_err'] = '还款成功';
					}
					//TODO 用户获得额度
					//TODO 借款者返佣金
					//TODO 判断借款人是否获得信用
				}
			}catch (\Exception $e){
				$root['show_err'] = '系统错误';
				return $root;
			}finally{
				if($root['status'] == 1) {
					$loanBaseDao->getDb()->commit();
					return $root;
				}else {
					$loanBaseDao->getDb()->rollback();
					return $root;
				}
			}
		}
		//网站资金代还
		public function siteRepay($id,$lkey,$user_id){
			$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
			$dealRepayDao = \Core::dao('loan_dealrepay');
			$dealLoadDao = \Core::dao('loan_dealload');
			$loanbaseDao = \Core::dao('loan_loanbase');
			$generationDao = \Core::dao('loan_generationrepay');
			$siteMoneyLogDao = \Core::dao('loan_sitemoneylog');
			$userDao = \Core::dao('user_user');
			$userBusiness = \Core::business('user_userinfo');
			$dealLoadRepayBusiness = \Core::business('sys_dealloadrepay');
			//获取该借款的投资用户列表
			$load_user_list = $dealLoadDao->getList(array('deal_id'=>$id),'id,deal_id,user_id,money');
			if(!$load_user_list) {
				$root['show_err'] = '投资不存在';
				return $root;
			}
			$repay_id = $dealRepayDao->findCol('id',array('deal_id'=>$id,'l_key'=>$lkey));
			$time = time();
			//判断是否逾期
			$userRepayImposeInfo = \Core::business('sys_dealrepay')->repayPlanImpose($id,$lkey);

			$need_repay_money = $userRepayImposeInfo['need_repay_money'];
			$status = ($userRepayImposeInfo['status']<2)?($userRepayImposeInfo['status']+1):$userRepayImposeInfo['status'];
			$impose_money = $userRepayImposeInfo['impose_money'];
			$manage_impose_money  = $userRepayImposeInfo['manage_impose_money'];
			//TODO 开启事务
			$loanbaseDao->getDb()->begin();
			try{
				//1.投资人回款
				foreach ($load_user_list as $v) {
					//获取所有投资用户该期的回款计划
					$user_load = $dealLoadRepayDao->getSomeOneLkeyPlan($id,$lkey,$v['user_id']);

					//TODO 网站代还 或已收到回款
					if($user_load['is_site_repay'] == 1 || $user_load['has_repay'] == 1){
						continue;
					}
					$update_status = $dealLoadRepayBusiness->updateLoadRepayPlan($user_load,$v['money'],$status,$impose_money,$manage_impose_money,1);
					if($update_status === false) {
						$root['show_err'] = '回款修改失败';
						return $root;
					}else {
						//更新成功，修改相关投资人余额等
						//是否有转让
						if ($user_load['t_user_id'] != 0) {
							$invest_user_id = $v['t_user_id'];
							$log_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($lkey + 1) . '期，回报本息';
						} else {
							$invest_user_id = $v['user_id'];
							$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($lkey + 1) . '期，回报本息';
						}
						//修投资人余额
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['repay_money'], $log_msg, 5);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，回报本息发放失败';
						}
						if ($user_load['manage_money'] > 0) {
							$log_msg = '[<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>]第' . ($lkey + 1) . '期，投标管理费';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_money'], $log_msg, 20);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，扣除投标管理费失败';
							}
						}
						if ($user_load['manage_interest_money'] > 0) {
							$log_msg = '[<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>]第' . ($lkey + 1) . '期，投标利息管理费';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_interest_money'], $log_msg, 20);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，扣除投标利息管理费失败';
							}
						}
						//逾期罚息
						if (($impose_money*($user_load['repay_money']/$v['money'])) != 0) {
							if ($user_load['t_user_id'] == 0) { //无债权转让
								$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($lkey + 1) . '期，逾期罚息';
								$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_msg, 21);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，逾期罚息发放失败';
								}
							} else {//有债权转让
								$log_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($lkey + 1) . '期，逾期罚息';
								$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_msg, 21);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，逾期罚息发放失败';
								}
							}
						}
						//投资者奖励
						if ($user_load['reward_money'] != 0) {
							$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($lkey + 1) . '期，奖励收益';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['reward_money'], $log_msg, 28);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，奖励收益发放失败';
							}
						}
						//TODO 普通会员邀请返利
						//投资者返佣金
						if ($user_load['manage_interest_money_rebate'] != 0) {
							//是否有上级，有上级则给上级返佣
							$rebate_user = $userDao->getUser($invest_user_id, 'id,pid');
							if ($rebate_user[$invest_user_id]['pid'] != 0) {
								$log_msg = '<a href="" target="_blank">' . $loanbaseDao->getName($id) . '</a>第' . ($lkey + 1) . '期，返佣金';
								$editMoneyStatus = $userBusiness->editUserMoney($rebate_user[$invest_user_id]['pid'], $user_load['manage_interest_money_rebate'], $log_msg, 23);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，返佣金发放失败';
								}
							}
						}
						//TODO 短信通知回款
					}
				}
				//2.记录网站资金
				//判断投资人是否回款完毕
				$no_repay_count = $dealLoadRepayDao->getCount(array('deal_id'=>$id,'l_key'=>$lkey,'has_repay'=>0));
				if($no_repay_count == 0) {
					//统计已还金额
					$repay_sum = $dealLoadRepayDao->getHasRepayTotal($id,$lkey);
					$get_manage = $dealRepayDao->isGetManage($id,$lkey,$user_id);
					//整理还款计划更新数据
					$repay_data = array();
					$repay_data['status'] = $status;
					$repay_data['true_repay_time'] = $time;
					//by xssd zqf 2015-11-2 代还的不记为已还
					//$repay_data['has_repay'] = 1;
					$repay_data['impose_money'] = round($repay_sum['total_impose_money'], 2);
					$repay_data['true_self_money'] = round($repay_sum['total_self_money'], 2);
					$repay_data['true_repay_money'] = round($repay_sum['total_repay_money'], 2);

					if ($get_manage == 0) {
						$repay_data['true_manage_money'] = round($repay_sum['total_repay_manage_money'], 2);
					}
					$repay_data['true_mortgage_fee'] = round($repay_sum['total_mortgage_fee'], 2);
					$repay_data['true_interest_money'] = round($repay_sum['total_interest_money'], 2);
					$repay_data['manage_impose_money'] = round($repay_sum['total_repay_manage_impose_money'], 2);

					$repay_data['true_manage_money_rebate'] = round($repay_data['true_manage_money'] * floatval(C('BORROWER_COMMISSION_RATIO')) / 100, 2);
					//借款者返佣
					if($repay_data['true_manage_money_rebate'] != 0 ) {
						//是否有上级，有上级则给上级返佣
						$rebate_user = $userDao->getUser($user_id,'id,pid');
						if($rebate_user[$invest_user_id]['pid'] != 0) {
							$log_msg = '<a href="" target="_blank">'.$loanbaseDao->getName($id).'</a>第'.($v['l_key']+1).'期，返佣金';
							$editMoneyStatus = $userBusiness->editUserMoney($rebate_user[$invest_user_id]['pid'],$repay_data['true_manage_money_rebate'],$log_msg, 23);
						}
					}
					//更新数据
					$update_status = $dealRepayDao->update($repay_data,array('deal_id'=>$id,'l_key'=>$lkey,'has_repay'=>0));
					if($update_status === false ) {
						$root['show_err'] = '代还失败，更新还款计划数据失败';
						return $root;
					}
					//TODO 逾期扣积分
					if($userRepayImposeInfo['overday'] > 0 ) {
						if($userRepayImposeInfo['overday'] < C('YZ_IMPSE_DAY')) {
							//普通逾期扣分
							$point = C('IMPOSE_POINT')?C('IMPOSE_POINT'):0;
							$log_msg = "[<a href='' target='_blank'>" . $loanbaseDao->getName($id). "</a>],第" . ($lkey + 1) . "期,逾期还款";
						}else {
							//严重逾期扣分
							$point = C('YZ_IMPOSE_POINT')?C('YZ_IMPOSE_POINT'):0;
							$log_msg = "[<a href='' target='_blank'>" . $loanbaseDao->getName($id). "</a>],第" . ($lkey + 1) . "期,严重逾期";
						}
						if($point != 0) {
							\Core::business('user_userinfo')->editUserPoint($user_id,$point,$log_msg,11);
						}
					}
					//整理还款记录数据
					//网站代还统计
					$site_repay_sum = $dealLoadRepayDao->getHasSiteRepayTotal($id,$lkey);
					$dealRepayLogBusiness = \Core::business('loan_dealrepaylog');
					if($site_repay_sum) {
						//记录还款日志
						$repay_msg = '网站代还款，本息：' . $site_repay_sum['total_repay_money'];
						$dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						//罚息
						if($site_repay_sum['total_impose_money'] != 0) {
							$repay_msg = '网站代还款，逾期费用：' . $site_repay_sum['total_impose_money'];
							$dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						}
						//借款管理费
						if ($site_repay_sum['total_manage_money'] > 0 && $get_manage == 0) {
							$repay_msg = '网站代还款，管理费：' .$site_repay_sum['total_manage_money'];
							$dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						}
						//抵押物管理费
						if($site_repay_sum['total_mortgage_fee'] > 0 ) {
							$repay_msg = '网站代还款，抵押物管理费：' . $site_repay_sum['total_mortgage_fee'];
							$dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						}
						//逾期管理费
						if($site_repay_sum['total_repay_manage_impose_money'] > 0) {
							$repay_msg = '会员还款，逾期管理费：' . $site_repay_sum['total_repay_manage_impose_money'];
							$dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						}
					}
					//判断代还款表中是否存在该期数据
					$generation_count = $generationDao->getCount(array('deal_id'=>$id,'repay_id'=>$repay_id));
					if($generation_count == 0) {
						$cookie_admin = \Core::cookie('admin');
						$admin = unserialize(\Core::decrypt($cookie_admin));
						//录入数据
						$generation_repay['deal_id'] = $id;
						$generation_repay['repay_id'] = $repay_id;
						$generation_repay['admin_id'] = $admin['id'];
						$generation_repay['agency_id'] = $loanbaseDao->findCol('agency_id',array('id'=>$id));
						$generation_repay['repay_money'] = $site_repay_sum['total_repay_money'];
						$generation_repay['self_money'] = $site_repay_sum['total_self_money'];
						$generation_repay['impose_money'] = $site_repay_sum['total_impose_money'];
						if ($get_manage == 0) {
							$generation_repay['manage_money'] = $site_repay_sum['total_manage_money'];
						}
						$generation_repay['mortgage_fee'] = $site_repay_sum['total_mortgage_fee'];
						$generation_repay['manage_impose_money'] = $site_repay_sum['total_repay_manage_impose_money'];
						$generation_repay['create_time'] = $time;
						$generation_repay['is_auto_site_repay'] = 0; //网站垫付操作: 0-手动,1-自动
						//insert
						$generationDao->insert($generation_repay);
						//记录网站代还日志
						$site_money_data['user_id'] = $user_id;
						$site_money_data['create_time'] = $time;
						if($repay_sum['total_repay_manage_money'] != 0 && $get_manage == 0) {
							$site_money_data['memo'] = "[<a href='' target='_blank'>" . $loanbaseDao->getName($id) . "</a>],第" . ($lkey+1) . "期,借款管理费";
							$site_money_data['type'] = 10;
							$site_money_data['money'] = $repay_sum['total_repay_manage_money'];
							$siteMoneyLogDao->insert($site_money_data);
						}
						if($repay_sum['total_repay_manage_impose_money'] != 0 ) {
							$site_money_data['memo'] = "[<a href='' target='_blank'>" .$loanbaseDao->getName($id). "</a>],第" . ($lkey+1) . "期,逾期管理费";
							$site_money_data['type'] = 12;
							$site_money_data['money'] = $repay_sum['total_repay_manage_impose_money'];
							$siteMoneyLogDao->insert($site_money_data);
						}
					}
				}
				$bid_no_repay = $dealRepayDao->getAllNoRepay($id);
				if($bid_no_repay == 0) {
					$bidflag = \Core::dao('loan_loanbid')->update(array('deal_status'=>5,'pay_off_status'=>1),array('loan_id'=>$id));
					if($bidflag === false){
						$root['show_err'] = '还款失败';
						return $root;
					}
				}
				$root['show_err'] = '网站代还款成功';
				$root['status'] = 1;
			}catch (\Exception $e){
				$root['show_err'] = '系统错误';
				return $root;
			}finally{
				if($root['status'] == 1) {
					$userDao->getDb()->commit();
					return $root;
				}else {
					$userDao->getDb()->rollback();
					return $root;
				}
			}

		}
		//会员详情
		public function userDetail($user_id){
			$user_sta = \Core::dao('user_usersta')->getByUserId($user_id);
			//会员详情
			return "总的借款数: " . \Core::arrayGet($user_sta,'borrow_amount',0) . " <br/>总借入笔数：" . \Core::arrayGet($user_sta,'deal_count',0) . " <br/>成功借款：" . \Core::arrayGet($user_sta,'success_deal_count',0) . " <br/>还清笔数：" . \Core::arrayGet($user_sta,'repay_deal_count',0) . " <br/>提前还清：" . \Core::arrayGet($user_sta,'tq_repay_deal_count',0) . " <br/>正常还清：" . \Core::arrayGet($user_sta,'zc_repay_deal_count',0) . " <br/>未还清：" . \Core::arrayGet($user_sta,'wh_repay_deal_count',0) . " <br/>逾期次数：" . \Core::arrayGet($user_sta,'yuqi_count',0) . " <br/>严重逾期次数：" . \Core::arrayGet($user_sta,'yz_yuqi_count',0) . " <br/>提前还款违约金：" . \Core::arrayGet($user_sta,'load_tq_impose',0) . " <br/>逾期还款违约金：" . \Core::arrayGet($user_sta,'load_yq_impose',0);

		}
		//用户其他平台注册情况
		public function userPlatRegVerified($loan_id,$user_id){
			//所有平台
			$platfroms = \Core::dao('user_userregplatform')->getPlatforms('id,name');
			//验证过的平台
			$verified = \Core::dao('user_userregplatverified')->getVerified($loan_id,$user_id);
			//\Core::dump($verified);die();
			$has_html = '<span>已注册平台：</span> ';
			$no_html = '<span>未注册平台：</span> ';
			$fail_html = '<span>无法判断平台：</span> ';
			foreach ($platfroms as $k=>$v) {
				if(\Core::arrayKeyExists($k,$verified)) {
					if($verified[$k]['is_register'] == 1) {
						$has_html .= \Core::arrayGet($v,'name').'&nbsp;';
					}else if($verified[$k]['is_register'] == 0) {
						$no_html .= \Core::arrayGet($v,'name').'&nbsp;';
					}else{
						$fail_html .= \Core::arrayGet($v,'name').'&nbsp;';
					}
				}else{
					$fail_html .= \Core::arrayGet($v,'name').'&nbsp;';
				}
			}
			return $has_html.'<br>'.$no_html.'<br>'.$fail_html;
		}
		
		//当前使用的贷款类型List
		public function enumDealLoanTypeActive(){
			$dealLoanTypeList=\Core::cache()->get('deal_loan_type_active');
			if(!$dealLoanTypeList){
				$dealLoanTypeDao=\Core::dao('loan_dealloantype');
				$dealLoanTypeList=$dealLoanTypeDao->getDealLoanTypes('id,name',array('is_effect'=>1,'is_delete'=>0));
				if($dealLoanTypeList){
					\Core::cache()->set('deal_loan_type_active',$dealLoanTypeList);
				}
			}
			return $dealLoanTypeList;
		}

		//还款方式列表
		public function loanTypeList() {
			$loanTypeList[0] = array('name'=>'等额本息','repay_time_type'=>array(1));
			$loanTypeList[1] = array('name'=>'先息后本','repay_time_type'=>array(1));
			$loanTypeList[2] = array('name'=>'到期还本息','repay_time_type'=>array(0,1));
			$loanTypeList[3] = array('name'=>'等额本金','repay_time_type'=>array(1));
		}
	
		//当前用户贷款次数
		public function enumDealTimes($user_id) {
			$loan_ids = \Core::dao('loan_loanbid')->findCol('loan_id',array('deal_status'=>3),true); //流标的贷款
			$flow_loan_ids = \Core::dao('loan_loanbase')->findCol('id',array('user_id'=>$user_id,'is_delete <>'=>3),true);
			$ids = array_diff($loan_ids,$flow_loan_ids);
			return count($ids);
		}

		//当前用户逾期记录逾期
		public function enumOverRepayTimes($user_id) {
			return \Core::dao('loan_dealloadrepay')->getCount(array('user_id'=>$user_id,'status >'=>1));
		}
	
		//检测并更正借款状态,触发自动投标，并发送提示信息
		public function synDealStatus($loan_id,$is_autobid=true) {
			$loanbidDao = \Core::dao('loan_loanbid');
			$loanbaseDao = \Core::dao('loan_loanbase');
			$dealloadDao = \Core::dao('loan_dealload');
			$deals_time = time();
			$loanbase_info = $loanbaseDao->getloanbase($loan_id,'borrow_amount,repay_time,repay_time_type,loantype');
			$loanbid_info = $loanbidDao->getOneLoanById($loan_id,"load_money,deal_status,repay_start_time,success_time,is_autobid,(start_time+enddate*24*3600=".$deals_time.") as remain_time,(load_money/".$loanbase_info['borrow_amount']."*100) as progress_point");
			if($loanbid_info['deal_status'] == 5) {
				return true;
			}

			if($loanbid_info['deal_status'] != 3) {
				if ($loanbid_info['progress_point'] < 100) {
					$loanbid['load_money'] = $dealloadDao->getLoadMoneyByLoanId($loan_id);
					$progress_point = $loanbid_info['progress_point'] = round($loanbid['loan_money'] / $loanbase_info['borrow_amount'] * 100,2);
				}

				if(($progress_point >= 100 || $loanbid_info['progress_point'] >= 100) && floatval($loanbid_info['load_money']) >= floatval($loanbase_info['borrow_amount'])) {
					if(Core::dao('loan_dealinrepayrepay')->exists($loan_id)) {
						$loanbid['deal_status'] = 5;
						$loanbid['pay_off_status'] = 1;
						$all_repay_money = \Core::dao('loan_dealloadrepay')->getAllRepayMoney($loan_id);
						if ($all_repay_money) {
							$loanbid['repay_money'] = round($all_repay_money,2);
						}
						$loanbid['last_repay_time'] = \Core::dao('loan_dealrepay')->getLastReapayTime($loan_id);
					} elseif (($loanbid_info['deal_status'] == 4 && $loanbid_info['repay_start_time'] > 0) || ($loanbid_info['deal_status'] == 2 && $loanbid_info['repay_start_time'] > 0 && $loanbid_info['repay_start_time'] <= $deals_time)) { 
						$all_repay_money = \Core::dao('loan_dealloadrepay')->getAllRepayMoney($loan_id);
						if ($all_repay_money) {
							$loanbid['repay_money'] = round($all_repay_money,2);
							$last_repay_time = \Core::dao('loan_dealrepay')->getLastRepayTime($loan_id);
							$loanbid['last_repay_time'] = $last_repay_time;
							$loanbid['next_repay_time'] = next_replay_month($last_repay_time);
						} elseif ($loanbid_info['deal_status'] == 4) {
							if ($loanbase_info['repay_time_type'] == 0) {
								$loanbid['next_repay_time'] = $loanbid_info['repay_start_time'] + $loanbase_info['repay_time'] * 24 *3600;
							} else {
								$is_last_repay = \Core::business('sys_dealrepay')->isLastRepay($loanbase_info['loantype']);
								if ($is_last_repay) {
									$loanbid['next_repay_time'] = next_replay_month($loanbid_info['repay_start_time'],$loanbase_info['repay_time']);
								} else {
									$loanbid['next_repay_time'] = next_replay_month($loanbid_info['repay_start_time']);
								}
							}
						}
						
						//判断是否完成还款【投资用户回款完毕】
						$dealrepayDao = \Core::dao('loan_dealrepaydao');
						if($dealrepayDao->isAllRepay($loan_id)) {
							$loanbid['deal_status'] = 5;
							$loanbid['pay_off_status'] = 1;
						} else {
							$loanbid['deal_status'] = 4;
						}
					} else {
						//获取最后一次投标记录
						if ($loanbid_info['success_time'] == 0) {
							$loanbid['success_time'] = $dealloadDao->getLastBidTime($loan_id);
						}
						$loanbid['deal_status'] = 2;
					}
				} elseif ($loanbid_info['remain_time'] <= 0 && $loanbid_info['deal_status'] == 1) {
					//投标时间超出,更新为流标
					$loanbid_info['deal_status'] = 1;
					$loanbid_info['bad_time'] = time();
				}
			}
			//投标人数及投标总额
			$bid_info = $dealloadDao->getDealLoad('count(id) as buy_count,sum(money) as load_money',array('deal_id'=>$loan_id));
			$loanbid['buy_count'] = $bid_info['buy_count'];
			$loanbid['load_money'] = $bid_info['load_money'];
			
			//发送流标通知
			if(($loanbid_info['deal_status'] == 3 || $loanbid['deal_status'] == 3) && $loanbid_info['is_send_bad_msg'] == 0) {
				$loanbid['is_send_bad_msg'] = 1;
				//todo 添加到动态
				//todo 站内信
			}

			//更新数据
			$loanbidDao->update($loanbid,$loan_id);

			if(\Core::arrayGet($loanbid,'is_send_bad_msg') == 1) {
				//todo 发邮件和短信
			}

			if($is_autobid && $loanbid_info['is_autobid']) {
				//todo 触发自动投标
			}

			return $loanbid;
			
		}
}