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
			$result = $loanbidDao->update($loanbid,$loan_id);
			if(!$result) {
				return false;
			}

			if(\Core::arrayGet($loanbid,'is_send_bad_msg') == 1) {
				//todo 发邮件和短信
			}

			if($is_autobid && $loanbid_info['is_autobid']) {
				//todo 触发自动投标
			}

			return $loanbid;
			
		}
}