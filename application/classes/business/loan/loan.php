<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_loan extends Business {
	public function business() {
		
	}
	//TODO 发送放款成功短信邮件
	public function sendDealSuccessMessage($loan_id){
		if(intval($loan_id) == 0) {
			return false;
		}
		if (C('MAIL_ON') == 0 && C('SMS_ON') == 0) {
			return false;
		}
		$time = time();
		//获取贷款信息
		$loanBaseDao = \Core::dao('loan_loanbase');
		$loanBidDao = \Core::dao('loan_loanbid');
		$userDao=\Core::dao('user_user');
		$msgTemplateDao = \Core::dao('msg_msgtemplate');
		$loan_base_info = $loanBaseDao->getloanbase($loan_id,'id,name,user_id,create_time');
		if(!$loan_base_info) return false;
		//借款者信息
		$is_send_success_msg = $loanBidDao->findCol('is_send_success_msg',array('loan_id'=>$loan_id));
		$bad_msg = $loanBidDao->findCol('bad_msg',array('loan_id'=>$loan_id));
		if(intval($is_send_success_msg) == 1) return false;
		$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$loan_base_info['user_id']))->row();
		//模板信息
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $loan_base_info['name'];
		$notice['deal_publish_time'] = date("Y年m月d日",$loan_base_info['create_time']);
		$notice['bad_msg'] = $bad_msg;
		//发送数据
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "您的借款“" . $loan_base_info['name'] . "”已满标！";
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = $time;
		$msg_data['user_id'] = $loan_base_info['user_id'];
		$dealMsgListDao = \Core::dao('msg_dealmsglist');
		//获取短信和邮件模板
		if(C('MAIL_ON') == 1) {
			$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_DEAL_SUCCESS','id,content,is_html');
			//模板内容
			$msg = '【小树时代测试】<p>尊敬的用户'.$user_info['user_name'].'：&nbsp; </p>';
			$msg .= '<p>很高兴的通知您，您于'.$notice['deal_publish_time'].'发布的借款“'.$loan_base_info['name'].'”满标，您的本次借款行为成功。&nbsp;</p><br><br>';
			$msg .= '<p>点击 <a href="">这里</a>查看您所发布借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>小树时代测试&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>';
			$msg .= '<p>如果您有任何疑问，请您查看 <a href="" target="_blank">帮助</a>，或访问 <a href="" target="_blank">客服中心</a></p>';
			$msg .= '<p>如果您觉得收到过多邮件，可以点击 <a href="" target="_blank">这里</a>进行设置&nbsp; </p>';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = $tmpl['is_html'];
			$msg_data['dest'] = $user_info['email'];
			$dealMsgListDao->insert($msg_data);
		}
		if(C('SMS_ON') == 1) {
			$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_DEAL_SUCCESS','id,content,is_html');
			$msg = '【小树时代测试】尊敬的用户'.$user_info['user_name'].'，很高兴的通知您，您于'.$notice['deal_publish_time'].'发布的借款“'.$loan_base_info['name'].'”满标。';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = $tmpl['is_html'];
			$msg_data['dest'] = $user_info['mobile'];
			$dealMsgListDao->insert($msg_data);
		}
		//投资者通知信息
		$load_user_list = \Core::dao('loan_dealload')->getLoads($loan_id,'id,user_id,user_name,create_time,money');
		$msgConfDao = \Core::dao('msg_msgconf');
		if($load_user_list) {
			foreach ($load_user_list as $v){
				//获取个人邮件设置
				$mail_bidsuccess = $msgConfDao->findCol('mail_bidsuccess',array('user_id'=>$v['user_id']));
				$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$v['user_id']))->row();
				//是否发送邮件
				$msg_data['send_type'] = 1;
				$msg_data['title'] = "您的所投的借款“" . $loan_base_info['name'] . "”已满标！";
				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = $time;
				$msg_data['user_id'] = $v['user_id'];
				if($mail_bidsuccess == 1 && C('MAIL_ON') == 1) {
					$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_LOAD_SUCCESS','id,content,is_html');
					$msg = '【小树时代测试】<p>尊敬的用户'.$user_info['user_name'].'：&nbsp; </p>';
					$msg .= '<p>很高兴的通知您，您于'.date('Y年m月d日',$v['create_time']).'所投的借款“{$notice.deal_name}”满标，您的本次投标行为成功。&nbsp;</p><br><br>';
					$msg .= '<p>点击 <a href="">这里</a>查看您所投的借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
					$msg .= '<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
					$msg .= '<p>小树时代测试&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
					$msg .= '<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>';
					$msg .= '<p>如果您有任何疑问，请您查看 <a href="" target="_blank">帮助</a>，或访问 <a href="" target="_blank">客服中心</a></p>';
					$msg .= '<p>如果您觉得收到过多邮件，可以点击 <a href="" target="_blank">这里</a>进行设置 &nbsp; </p>';
					$msg_data['content'] = addslashes($msg);
					$msg_data['is_html'] = $tmpl['is_html'];
					$msg_data['dest'] = $user_info['email'];
					$dealMsgListDao->insert($msg_data);
				}
				//是否发送短信
				if(C('SMS_ON') == 1) {
					//
					$msg = '【小树时代测试】尊敬的用户'.$user_info['user_name'].'，很高兴的通知您，您于'.date('Y年m月d日',$v['create_time']).'所投的借款“'.$loan_base_info['name'].'”满标，扣除投标的冻结资金'.$v['money'].'。';
					$msg_data['content'] = addslashes($msg);
					$msg_data['is_html'] = $tmpl['is_html'];
					$msg_data['dest'] = $user_info['mobile'];
					$dealMsgListDao->insert($msg_data);
				}
			}
		}


	}
	//发送放款成功站内信
	public function sendDealSiteMessage($loan_id){
		if(intval($loan_id) == 0) return false;
		$loanBaseDao = \Core::dao('loan_loanbase');
		$loan_base_info = $loanBaseDao->getloanbase($loan_id,'id,name,create_time');
		if(!$loan_base_info) return false;
		$loanBidDao = \Core::dao('loan_loanbid');
		$is_send_success_msg = $loanBidDao->findCol('is_send_success_msg',array('loan_id'=>$loan_id));
		if(intval($is_send_success_msg) == 1) return false;
		$dealLoadDao = \Core::dao('loan_dealload');
		$user_load_list = $dealLoadDao->getLoads($loan_id,'id,user_name,user_id,create_time');
		if($user_load_list) {
			$msgConfDao = \Core::dao('msg_msgconf');
			foreach ($user_load_list as $v) {
				$sms_bid_success = $msgConfDao->findCol('sms_bidsuccess',array('user_id'=>$v['user_id']));
				//未设置或设置为1时发送（设置为0时不发送）
				if( $sms_bid_success != 0) {
					$notice['shop_title'] = C("SHOP_TITLE");
					$notice['time'] = date("Y年m月d日",$v['create_time']);
					$notice['deal_name'] = "“<a href=\"" . \Core::getUrl("index", "detail","deal", array("id" => $loan_base_info['id'])) . "\">" . $loan_base_info['name'] . "</a>”";
					$content = '【小树时代测试】<p>感谢您使用'.$notice['shop_title'].'贷款融资，很高兴的通知您，您于'.$notice['time'].'投标的借款列表'.$notice['deal_name'].'满标';
					//TODO 保存站内信
					\Core::dao('msg_msgbox')->sendUserMsg("", $content, 0, $v['user_id'], time(), 0, true, 16);
				}
			}
		}
	}
	//TODO 发送回款成功短信邮件站内信to投资人(区分是否提前还款)
	public function sendRepayRebackMessage($loan_id,$l_key,$user_id,$repay_money,$interest_money,$impose_money,$is_advance=0,$time=0){
		if($time == 0) {
			$time = time();
		}
		$msgConfDao = \Core::dao('msg_msgconf');
		$userDao = \Core::dao('user_user');
		$loanBaseDao = \Core::dao('loan_loanbase');
		$msgTemplateDao = \Core::dao('msg_msgtemplate');
		$dealMsgListDao = \Core::dao('msg_dealmsglist');
		$dealRepay = \Core::dao('loan_dealrepay');
		//贷款基本
		$loan_base_info = $loanBaseDao->getloanbase($loan_id,'id,name,user_id,create_time');
		//下期还款信息
		$next_loan = $dealRepay->getNextLoan($loan_id,$l_key);
		//获取个人邮件设置
		$mail_bidrepaid = $msgConfDao->findCol('mail_bidrepaid',array('user_id'=>$user_id));
		$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$user_id))->row();
		//是否发送邮件
		$msg_data['send_type'] = 1;
		$msg_data['title'] = "您的所投的借款“" . $loan_base_info['name'] . "”已回款！";
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = $time;
		$msg_data['user_id'] = $user_id;
		if($mail_bidrepaid == 1 && C('MAIL_ON') == 1) {
			//【小树时代测试】<p>尊敬的用户{$notice.user_name}：&nbsp; </p>
			//<p>您好，您在{$notice.site_name}所投的的投标“<a href="{$notice.deal_url}">{$notice.deal_name}</a>”成功还款{$notice.repay_money}元 </p>
			//{if $notice.need_next_repay}
			//<p>本笔投标的下个还款日为{$notice.next_repay_time}，需还本息{$notice.next_repay_money}元。</p>
			//{else}
			//<p>本次投标共获得收益:{$notice.all_repay_money}元,{if $notice.impose_money}其中违约金为:{$notice.impose_money}元,{/if}本次投标已回款完毕！</p>
			//{/if}
			//<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>
			//<p>{$notice.site_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>
			//<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>
			//<p>如果您有任何疑问，请您查看 <a href="{$notice.help_url}" target="_blank">帮助</a>，或访问 <a href="{$notice.site_url}" target="_blank">客服中心</a></p>
			//<p>如果您觉得收到过多邮件，可以点击 <a href="{$notice.msg_cof_setting_url}" target="_blank">这里</a>进行设置
			//&nbsp; </p>
			$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_LOAD_SUCCESS','id,content,is_html');
			$msg = '【小树时代测试】<p>尊敬的用户'.$user_info['user_name'].'：&nbsp; </p>';
			$msg .= '您好，您在'.'小树时代'.'所投的的投标“'.$loan_base_info['name'].'”成功还款'.$repay_money.'元。&nbsp;</p><br><br>';

			if($next_loan && $is_advance == 0) {
				$msg .= '<p>本笔投标的下个还款日为'.date('Y年m月d日',$next_loan['repay_time']).'，需还本息'.$next_loan['repay_money'].'元。&nbsp;</p><br><br>';
			}else{
				$msg .='<p>本次投标共获得收益:'.($interest_money+$impose_money).'元，';
				if($impose_money != 0) {
					$msg .= '其中违约金为:'.$impose_money.'元,';
				}
				$msg .= '本次投标已回款完毕！</p>';
			}
			$msg .= '<p>点击 <a href="">这里</a>查看您所投的借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>小树时代测试&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>';
			$msg .= '<p>如果您有任何疑问，请您查看 <a href="" target="_blank">帮助</a>，或访问 <a href="" target="_blank">客服中心</a></p>';
			$msg .= '<p>如果您觉得收到过多邮件， <a href="" target="_blank">这里</a>进行设置 &nbsp; </p>';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = $tmpl['is_html'];
			$msg_data['dest'] = $user_info['email'];
			$dealMsgListDao->insert($msg_data);
		}
		//是否发送短信
		if(C('SMS_ON') == 1) {
			//TODO 获取短信模板
			//【小树时代测试】尊敬的{$notice.site_name}用户{$notice.user_name}，您所投的标“{$notice.deal_name}”回款{$notice.repay_money}元，感谢您的关注和支持。
			$msg = '【小树时代测试】尊敬的用户'.$user_info['user_name'].'，您所投的标“'.$loan_base_info['name'].'”回款。'.($repay_money+$impose_money).'元，感谢您的关注和支持。';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = 0;
			$msg_data['dest'] = $user_info['mobile'];
			$dealMsgListDao->insert($msg_data);
		}
		//记录回款站内信
		//【小树时代测试】您好，您在{$notice.site_title}的投标{$notice.url}成功还款{$notice.repay_money}元
		//{if $notice.has_next_loan}本笔投标的下个还款日为{$notice.next_repay_time}，
		//需还本息{$notice.next_repay_money}元。{else}本次投标共获得收益:{$notice.all_shouyi_money}元,
		//其中违约金为:{$notice.all_impose_money}元,本次投标已回款完毕！{/if}
		if($mail_bidrepaid == 1) {
			$content = '【小树时代测试】您好，您在小树时代的投标'.$loan_base_info['name'].'成功还款'.$repay_money.'元';
			if($next_loan && $is_advance == 0) {
				$content .= '<p>本笔投标的下个还款日为'.date('Y年m月d日',$next_loan['repay_time']).'，需还本息'.$next_loan['repay_money'].'元。&nbsp;</p><br><br>';
			}else{
				$content .='<p>本次投标共获得收益:'.($interest_money+$impose_money).'元，';
				if($impose_money != 0) {
					$content .= '其中违约金为:'.$impose_money.'元,';
				}
				$content .= '本次投标已回款完毕！</p>';
			}
			\Core::dao('msg_msgbox')->sendUserMsg("", $content, 0, $user_id, time(), 0, true, 9);
		}
	}
	//TODO 发送还款成功短信站内信to借款人（是否提前还款）
	/**
	 * 发送还款成功短信站内信to借款人
	 * @param $loan_id 借款id
	 * @param $l_key 还的第几期
	 * @param $user_id 还款人id
	 * @param $repay_money 还款本息
	 * @param $manage_money 管理费
	 * @param $impose_money 逾期违约金
	 * @param $impose_manage_money 逾期违约金管理费
	 * @param $is_advance  是否提前还款
	 * @param $time 还款时间
	 * **/
	public function sendRepayMessage($loan_id,$l_key,$user_id,$repay_money,$manage_money,$impose_money=0,$impose_manage_money=0,$is_advance=0,$time=0){
		if($time == 0) {
			$time = time();
		}
		$msgConfDao = \Core::dao('msg_msgconf');
		$userDao = \Core::dao('user_user');
		$loanBaseDao = \Core::dao('loan_loanbase');
		$dealMsgListDao = \Core::dao('msg_dealmsglist');
		$dealRepay = \Core::dao('loan_dealrepay');
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		//贷款基本
		$loan_base_info = $loanBaseDao->getloanbase($loan_id,'id,name,user_id,create_time');
		//下期还款信息
		$next_loan = $dealRepay->getNextLoan($loan_id,$l_key);
		//获取个人邮件设置
		$mail_bidrepaid = $msgConfDao->findCol('mail_bidrepaid',array('user_id'=>$user_id));
		$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$user_id))->row();
		//未回款投资人
		$no_repay_count = $dealLoadRepayDao->getNoRepayCountByDealId($loan_id,$l_key);
		$msg_data['send_type'] = 0;
		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = $time;
		$msg_data['user_id'] = $user_id;
		//是否发送短信
		if(C('SMS_ON') == 1) {
			//TODO 获取短信模板
			if($is_advance == 1){
				$msg_data['title'] = "提前还款短信通知";
				//【小树时代测试】尊敬的{$notice.site_name}用户{$notice.user_name}，
				//您的借款“{$notice.deal_name}”在第{$notice.index}期{$notice.status}还款{$notice.all_money}元，感谢您的关注和支持。
				$user_msg = '【小树时代测试】您的借款“'.$loan_base_info['name'].'”在第'.($l_key+1).'期还款'.($repay_money+$manage_money+$impose_money+$impose_manage_money).'元，感谢您的关注和支持。';
			}else {
				$msg_data['title'] = "还款短信通知";
				//【小树时代测试】您好，您在{$notice.site_title}的借款{$notice.url}的借款第{$notice.index}期还款{$notice.repay_money}元
				//{$notice.repay_status}{if $notice.left_user_count gt 0}，还有{$notice.left_user_count}个投资人待还{/if}。
				//{if $notice.has_next_loan}本笔借款的下个还款日为{$notice.next_repay_time}，需要本息{$notice.next_repay_money}元。{/if}
				$user_msg = '【小树时代测试】您好，您在'.'小树时代'.'的借款“'.$loan_base_info['name'].'”第'.($l_key+1).'期还款'.($repay_money+$manage_money+$impose_money+$impose_manage_money).'元';
				if($no_repay_count > 0 ) {
					$user_msg .= '本期部分还款，还有'.$no_repay_count.'个投资人待还';
				}

			}
			$msg_data['content'] = addslashes($user_msg);
			$msg_data['is_html'] = 0;
			$msg_data['dest'] = $user_info['mobile'];
			$dealMsgListDao->insert($msg_data);
		}
		if($is_advance == 1) {
			//站内信内容
			//【小树时代测试】您好，您在{$notice.shop_title}的借款{$notice.url}成功提前还款{$notice.repay_money}元，
			//其中违约金为:{$notice.impose_money}元,本笔借款已还款完毕！
			$user_msg = '【小树时代测试】您好，您在'.'小树时代'.'，的借款“'.$loan_base_info['name'].'”成功提前还款'.$repay_money.'元，其中违约金为:'.$impose_money.'元,本笔借款已还款完毕！';
		}
		//发送站内信
		\Core::dao('msg_msgbox')->sendUserMsg("", addslashes($user_msg), 0, $user_id, time(), 0, true, 8);

	}
	//发送电子协议邮件
	public function sendDealContractEmail($loan_id){
		if(intval($loan_id) == 0) return false;
	}
	//发送流标通知邮件短信站内信
	public function sendDealFaildMessage($loan_id){
		if(intval($loan_id) == 0) {
			return false;
		}
		if (C('MAIL_ON') == 0 && C('SMS_ON') == 0) {
			return false;
		}
		$time = time();
		//获取贷款信息
		$loanBaseDao = \Core::dao('loan_loanbase');
		$loanBidDao = \Core::dao('loan_loanbid');
		$userDao=\Core::dao('user_user');
		$msgTemplateDao = \Core::dao('msg_msgtemplate');
		$msgConf = \Core::dao('msg_msgconf');
		$loan_base_info = $loanBaseDao->getloanbase($loan_id,'id,name,user_id,create_time');
		if(!$loan_base_info) return false;
		$mail_myfail = $msgConf->findCol('mail_myfail',array('user_id'=>$loan_base_info['user_id']));
		$sms_myfail = $msgConf->findCol('sms_myfail',array('user_id'=>$loan_base_info['user_id']));
		//借款者信息
		$bad_msg = $loanBidDao->findCol('bad_msg',array('loan_id'=>$loan_id));
		$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$loan_base_info['user_id']))->row();
		//模板信息
		$notice['user_name'] = $user_info['user_name'];
		$notice['deal_name'] = $loan_base_info['name'];
		$notice['deal_publish_time'] = date("Y年m月d日",$loan_base_info['create_time']);
		$notice['bad_msg'] = $bad_msg;
		//发送数据

		$msg_data['send_time'] = 0;
		$msg_data['is_send'] = 0;
		$msg_data['create_time'] = $time;
		$msg_data['user_id'] = $loan_base_info['user_id'];
		$dealMsgListDao = \Core::dao('msg_dealmsglist');
		//获取短信和邮件模板
		if(C('MAIL_ON') == 1 && $mail_myfail == 1) {
			$msg_data['send_type'] = 1;
			$msg_data['title'] = "您的所投的借款“" . $loan_base_info['name'] . "”已流标！";
			$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_DEAL_SUCCESS','id,content,is_html');
			//模板内容

			$msg = '【小树时代测试】<p>尊敬的用户'.$user_info['user_name'].'：&nbsp; </p>';
			$msg .= '<p>遗憾的通知您，您于'.$notice['deal_publish_time'].'发布的借款“'.$loan_base_info['name'].'”流标，您的本次借款行为失败。&nbsp;</p><p>您借款失败的可能原因为：&nbsp; </p><br><br>';
			$msg .= '1. 您没能按时提交四项必要信用认证的材料。<br><br>2. 您在招标期间没有筹集到足够的借款。&nbsp;&nbsp;';
			$msg .= '<p>如果您属于认证未通过流标，为了您能够成功贷款，请凑齐申请贷款所需要的材料。';
			$msg .= '您可以点击<a href="#" target="_blank">需要提供哪些材料？</a>来了解更多所需材料的详情。进行更多的信用认证将有助您获得更高的贷款额度。</p>';
			$msg .= '<p>如果您属于招标到期流标，为了您能够成功贷款，请适度提高贷款利率，将有助您更快的获得贷款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>点击 <a href="">这里</a>重新发布借款。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>小树时代测试&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
			$msg .= '<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>';
			$msg .= '<p>如果您有任何疑问，请您查看 <a href="" target="_blank">帮助</a>，或访问 <a href="" target="_blank">客服中心</a></p>';
			$msg .= '<p>如果您觉得收到过多邮件，可以点击 <a href="" target="_blank">这里</a>进行设置&nbsp; </p>';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = $tmpl['is_html'];
			$msg_data['dest'] = $user_info['email'];
			$dealMsgListDao->insert($msg_data);
		}
		if(C('SMS_ON') == 1) {
			$msg_data['send_type'] = 0;
			$msg_data['title'] = $user_info['user_name'] . "的借款“" . $loan_base_info['name'] . "”流标通知";
			$tmpl = $msgTemplateDao->getTemplateByName('TPL_SMS_DEAL_FAILED','id,content,is_html');
			//【小树时代测试】尊敬的用户{$notice.user_name}，遗憾的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”流标。
			$msg = '【小树时代测试】尊敬的用户'.$user_info['user_name'].'，遗憾的通知您，您于'.$notice['deal_publish_time'].'发布的借款“'.$loan_base_info['name'].'”流标。';
			$msg_data['content'] = addslashes($msg);
			$msg_data['is_html'] = $tmpl['is_html'];
			$msg_data['dest'] = $user_info['mobile'];
			$dealMsgListDao->insert($msg_data);
		}
		//站内信

		if($sms_myfail == 1) {
			$content = '【小树时代测试】<p>感谢您使用'.'小树时代'.'贷款融资，';
			$content .= '但有一些遗憾的通知您，您于'.$notice['deal_publish_time'].'发布的借款'.$loan_base_info['name'].'流标，';
			$content .= '导致您本次所投的贷款列表流标的原因可能包括的原因：</p>1. 借款者没能按时提交四项必要信用认证的材料。<br>2. 借款者在招标期间没有筹集到足够的借款。';
			\Core::dao('msg_msgbox')->sendUserMsg("", addslashes($content), 0, $loan_base_info['user_id'], $time, 0, true, 10);
		}
		//投资者通知信息
		$load_user_list = \Core::dao('loan_dealload')->getLoads($loan_id,'id,user_id,user_name,create_time,money,bonus_user_id');
		$msgConfDao = \Core::dao('msg_msgconf');
		if($load_user_list) {
			foreach ($load_user_list as $v){
				// 获取所使用的优惠券金额
				$bonus_money = 0;
				if ($v['bonus_user_id'] > 0) {
					$bonus_rule_id = \Core::dao('user_bonususer')->getBonusRuleIdByUserId($v['bonus_user_id']);
					$bonus_money = \Core::dao('user_bonusrule')->findCol('money',array('id'=>$bonus_rule_id));
				}
				//获取个人邮件设置
				$mail_bidsuccess = $msgConfDao->findCol('mail_bidsuccess',array('user_id'=>$v['user_id']));
				$user_info = $userDao->getUserInfo('user_name,mobile,email',array('id'=>$v['user_id']))->row();
				//是否发送邮件

				$msg_data['send_time'] = 0;
				$msg_data['is_send'] = 0;
				$msg_data['create_time'] = $time;
				$msg_data['user_id'] = $v['user_id'];
				if($mail_bidsuccess == 1 && C('MAIL_ON') == 1) {
					$msg_data['send_type'] = 1;
					$msg_data['title'] = "您的所投的借款“" . $loan_base_info['name'] . "”已流标！";
					$tmpl = $msgTemplateDao->getTemplateByName('TPL_MAIL_LOAD_SUCCESS','id,content,is_html');

					$msg = '【小树时代测试】<p>尊敬的用户'.$user_info['user_name'].'：&nbsp; </p>';
					$msg .= '<p>遗憾的通知您，您于'.date('Y年m月d日',$v['create_time']).'所投的借款“{$notice.deal_name}”流标，您的本次投标行为失败。&nbsp;</p><p>您所投的借款失败的可能原因为：&nbsp; </p><br><br>';
					$msg .= '1. 借款者没能按时提交四项必要信用认证的材料。<br><br>2. 借款者在招标期间没有筹集到足够的借款。&nbsp;&nbsp;';
					$msg .= '<p>感谢您对我们的支持与关注。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
					$msg .= '<p>小树时代测试&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>';
					$msg .= '<p>注：此邮件由系统自动发送，请勿回复！&nbsp; </p>';
					$msg .= '<p>如果您有任何疑问，请您查看 <a href="" target="_blank">帮助</a>，或访问 <a href="" target="_blank">客服中心</a></p>';
					$msg .= '<p>如果您觉得收到过多邮件，可以点击 <a href="" target="_blank">这里</a>进行设置 &nbsp; </p>';
					$msg_data['content'] = addslashes($msg);
					$msg_data['is_html'] = $tmpl['is_html'];
					$msg_data['dest'] = $user_info['email'];
					$dealMsgListDao->insert($msg_data);
				}
				//是否发送短信
				if(C('SMS_ON') == 1) {
					$msg_data['send_type'] = 0;
					$msg_data['title'] = $user_info['user_name'] . "的投标“" . $loan_base_info['name'] . "”流标通知";
					//【小树时代测试】尊敬的用户{$notice.user_name}，遗憾的通知您，您于{$notice.deal_publish_time}发布的借款“{$notice.deal_name}”流标，解冻投标资金{$notice.money}。
					$msg = '【小树时代测试】尊敬的用户'.$user_info['user_name'].'，遗憾的通知您，您于'.date('Y年m月d日',$v['create_time']).'所投的借款“'.$loan_base_info['name'].'”满标，解冻投标资金'.($v['money']-$bonus_money).'。';
					$msg_data['content'] = addslashes($msg);
					$msg_data['is_html'] = $tmpl['is_html'];
					$msg_data['dest'] = $user_info['mobile'];
					$dealMsgListDao->insert($msg_data);
				}
				//站内信
				if($sms_myfail) {
					$content = '【小树时代测试】<p>感谢您使用'.'小树时代'.'贷款融资，';
					$content .= '但有一些遗憾的通知您，您于'.$notice['deal_publish_time'].'投标的借款'.$loan_base_info['name'].'流标，';
					$content .= '导致您本次所投的贷款列表流标的原因可能包括的原因：</p>1. 借款者没能按时提交四项必要信用认证的材料。<br>2. 借款者在招标期间没有筹集到足够的借款。';
					\Core::dao('msg_msgbox')->sendUserMsg("", addslashes($content), 0, $v['user_id'], $time, 0, true, 10);
				}

			}
		}
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
		$loanBaseDao = \Core::dao('loan_loanbase');
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
			//贷款名称
			$loan_name = $loanBaseDao->getName($id);
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
						$root['show_err'] = '更新回款计划失败';
						return $root;
					}else {
						//更新成功，修改相关投资人余额等
						//是否有转让
						if ($user_load['t_user_id'] != 0) {
							$invest_user_id = $v['t_user_id'];
							$log_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($l_key + 1) . '期，回报本息';
							$log_impose_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($l_key + 1) . '期，逾期罚息';
						} else {
							$invest_user_id = $v['user_id'];
							$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($l_key + 1) . '期，回报本息';
							$log_impose_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($l_key + 1) . '期，逾期罚息';
						}
						//修投资人余额
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['repay_money'], $log_msg, 5);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，回报本息发放失败';
							return $root;
						}
						if ($user_load['manage_money'] > 0) {
							$log_msg = '[<a href="" target="_blank">' . $loan_name . '</a>]第' . ($l_key + 1) . '期，投标管理费';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_money'], $log_msg, 20);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，扣除投标管理费失败';
								return $root;
							}
						}
						if ($user_load['manage_interest_money'] > 0) {
							$log_msg = '[<a href="" target="_blank">' . $loan_name . '</a>]第' . ($l_key + 1) . '期，投标利息管理费';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_interest_money'], $log_msg, 20);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，扣除投标利息管理费失败';
								return $root;
							}
						}
						//逾期罚息
						$user_impose_money = $impose_money*($user_load['repay_money']/$v['money']);
						if ($user_impose_money != 0) {
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_impose_msg, 21);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，逾期罚息发放失败';
								return $root;
							}
						}
						//投资者奖励
						if ($user_load['reward_money'] != 0) {
							$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($l_key + 1) . '期，奖励收益';
							$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['reward_money'], $log_msg, 28);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，奖励收益发放失败';
								return $root;
							}
						}
						//TODO 普通会员邀请返利
						//判断该标是否参与分销返利
						if ($loanBaseDao->findCol('is_referral_award',array('id'=>$id)) != 0) {
							$this->getReferrals($id,$user_load['id'],$invest_user_id);
						}
						//投资者返佣金
						if ($user_load['manage_interest_money_rebate'] != 0) {
							//是否有上级，有上级则给上级返佣
							$rebate_user = $userDao->findCol('pid',array('id'=>$invest_user_id));
							if ($rebate_user != 0) {
								$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($l_key + 1) . '期，返佣金';
								$editMoneyStatus = $userBusiness->editUserMoney($rebate_user, $user_load['manage_interest_money_rebate'], $log_msg, 23);
								if($editMoneyStatus === false){
									$root['show_err'] = '回款失败，返佣金发放失败';
									return $root;
								}
							}
						}
						//TODO 短信通知回款
						//站内信。邮件短信
						//发送站内信。邮件短信
						$this->sendRepayRebackMessage($id,$l_key,$invest_user_id,$user_load['repay_money'],$user_load['interest_money'],$user_impose_money);
					}
				}
				//2.借款人扣款
				$dealRepayLogBusiness = \Core::business('loan_dealrepaylog');
				$dealRepayBusiness = \Core::business('sys_dealrepay');
				//判断当前期是否还款完毕
				$no_repay_count = $dealLoadRepayBusiness->isRepayedByLkey($id,$l_key);
				if($no_repay_count == 0) {
					$hasRepayTotal = $dealLoadRepayDao->getHasRepayTotal($id,$l_key);
					//\Core::dump($hasRepayTotal);die();
					//整合借款人更新数据

					//TODO 修改借款者还款计划信息
					$dealRepayStatus = $dealRepayBusiness->updateRepayPlan($hasRepayTotal,$impose_money,$manage_impose_money,$getManage,$status);
					if($dealRepayStatus === false) {
						$root['show_err'] = '还款失败，更新还款计划失败';
						return $root;
					}
					//借款人扣款
					$log_repay_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，偿还本息';
					$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$hasRepayTotal['total_repay_money'],$log_repay_msg,4);
					if($editMoneyStatus === false) {
						$root['show_err'] = '还款失败，扣除借款人本息失败';
						return $root;
					}
					//记录还款日志
					$repay_msg = '会员还款，本息：' . $hasRepayTotal['total_repay_money'];
					$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
					if($add_repay_log_status === false) {
						$root['show_err'] = '还款失败，保存还款记录失败';
						return $root;
					}
					//罚息
					if($hasRepayTotal['total_impose_money'] != 0) {
						$log_impose_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，逾期罚息';
						$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$hasRepayTotal['total_impose_money'],$log_impose_msg,11);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人逾期罚息失败';
							return $root;
						}
						$repay_msg = '会员还款，逾期费用：' . $hasRepayTotal['total_impose_money'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '还款失败，保存还款记录失败';
							return $root;
						}

					}
					//借款管理费
					if ($user_repay['manage_money'] > 0 && $getManage == 0) {
						$log_manage_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，借款管理费';
						$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$user_repay['manage_money'],$log_manage_msg,10);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人借款管理费失败';
							return $root;
						}
						$repay_msg = '会员还款，管理费：' . $user_repay['manage_money'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '还款失败，保存还款记录失败';
							return $root;
						}
					}
					//抵押物管理费
					if($user_repay['mortgage_fee'] > 0 ) {
						$log_mortgage_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，抵押物管理费'.$ext_str;
						$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$user_repay['mortgage_fee'],$log_mortgage_msg,27);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人借款管理费失败';
							return $root;
						}
						$repay_msg = '会员还款，抵押物管理费：' . $user_repay['mortgage_fee'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '还款失败，保存还款记录失败';
							return $root;
						}
					}
					//逾期管理费
					if($manage_impose_money > 0) {
						$log_impose_manage_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，逾期管理费'.$ext_str;
						$editMoneyStatus = $userBusiness->editUserMoney($user_id,-$manage_impose_money,$log_impose_manage_msg,12);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人借款管理费失败';
							return $root;
						}
						$repay_msg = '会员还款，逾期管理费：' . $manage_impose_money;
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($user_repay['id'],$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '还款失败，保存还款记录失败';
							return $root;
						}
					}
					//逾期扣除信用积分point
					if($status == 3 ) {
						//严重逾期
						$log_impose_point_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，严重逾期'.$ext_str;
						$point = C('YZ_IMPOSE_POINT');
						$editMoneyStatus = $userBusiness->editUserPoint($user_id,-$point,$log_impose_point_msg,11);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人逾期信用积分失败';
							return $root;
						}

					}elseif($status == 2) {
						//普通逾期
						$log_impose_point_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($l_key+1).'期，逾期还款'.$ext_str;
						$point = C('IMPOSE_POINT')?C('IMPOSE_POINT'):10;
						$editMoneyStatus = $userBusiness->editUserPoint($user_id,-$point,$log_impose_point_msg,11);
						if($editMoneyStatus === false) {
							$root['show_err'] = '还款失败，扣除借款人逾期信用积分失败';
							return $root;
						}
					}
					//借款者返佣
					$true_manage_money_rebate = $user_repay['manage_money'] * floatval(C('BORROWER_COMMISSION_RATIO')) / 100;
					if($true_manage_money_rebate != 0 ) {
						//是否有上级，有上级则给上级返佣
						$rebate_user = $userDao->getUser($user_id,'id,pid');
						if($rebate_user[$invest_user_id]['pid'] != 0) {
							$log_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($v['l_key']+1).'期，返佣金';
							$editMoneyStatus = $userBusiness->editUserMoney($rebate_user[$invest_user_id]['pid'],$true_manage_money_rebate,$log_msg, 23);
							if($editMoneyStatus === false) {
								$root['show_err'] = '还款失败，发放借款者返佣失败';
								return $root;
							}
						}
					}
					//TODO 发送还款短信站内信
					$this->sendRepayMessage($id,$l_key,$user_id,$hasRepayTotal['total_repay_money'],$user_repay['manage_money'],$hasRepayTotal['total_impose_money'],$manage_impose_money,0);
					//TODO 修改代还款表信息
					//\Core::dao('loan_generationrepay')->update(array('status'=>1),array('deal_id'=>$id,'repay_id'=>$user_repay['id']));

					//全部还清
					$bid_no_repay = $dealRepayDao->getAllNoRepay($id);
					if($bid_no_repay == 0) {
						$bidflag = \Core::dao('loan_loanbid')->update(array('deal_status'=>5,'pay_off_status'=>1),array('loan_id'=>$id));
						if($bidflag === false){
							$root['show_err'] = '还款失败，更新借款状态失败';
							return $root;
						}
						//TODO 用户获得信用
						//判断获取的信用是否超过限制
						//TODO 用户获得额度
						$log_msg = "[<a href='' target='_blank'>" .$loan_name. "</a>],还清借款获得额度";
						$update_quota_status = $userBusiness->editUserQuota($user_id,trim(C('USER_REPAY_QUOTA')),$log_msg, 6);
						if($update_quota_status === false) {
							$root['show_err'] = '还款失败，更新用户额度失败';
							return $root;
						}
					}
					//TODO 保存催收日志
					//查看催收表中该借款是否正在催收（正在催收则记录日志）
					//TODO 债权回滚
					//标对应的正在转让中的债权
					$this->transReback($id);
					$root['show_err'] = '还款成功';
					$root['status'] = 1;
					return $root;

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
					//TODO 保存催收日志
					//查看催收表中该借款是否正在催收（正在催收则记录日志）
					if($updateStatus === false) {
						$root['show_err'] = '部分还款失败，更新还款计划状态失败';
						return $root;
					}else{
						//站内信。邮件短信
						$this->sendDealSuccessMessage($id);
						$this->sendDealSiteMessage($id);
						$root['show_err'] = '部分还款成功';
						$root['status'] = 1;
						return $root;
					}
				}

			}catch (\Exception $e){
				$userDao->getDb()->rollback();
				$root['show_err'] = '系统错误';
				return $root;
			}finally{
				if($root['status'] == 1) {
					$userDao->getDb()->commit();
				}else {
					$userDao->getDb()->rollback();
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
		$userDao = \Core::dao('user_user');
		$userBusiness = \Core::business('user_userinfo');
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
			$user_loan_early_interest_manage_fee = \Core::arrayKeyExists('user_loan_early_interest_manage_fee',$comon_config)?\Core::arrayGet($comon_config,'user_loan_early_interest_manage_fee'):1;
			$user_loan_interest_manage_fee = \Core::arrayKeyExists('user_loan_interest_manage_fee',$comon_config)?\Core::arrayGet($comon_config,'user_loan_interest_manage_fee'):0;
		}
		$time = time();
		//贷款名称
		$loan_name = $loanBaseDao->getName($id);
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
				//利息管理费
				$user_inrepay_info["true_manage_interest_money"] = round($need_interest_money['total_interest_money']*floatval($user_loan_interest_manage_fee)/100,2);
				//投资者返佣金额
				$user_inrepay_info["true_manage_interest_money_rebate"] = round($user_inrepay_info['true_manage_interest_money']* floatval(C('INVESTORS_COMMISSION_RATIO'))/100,2);
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
				if($loanbid_status === false) {
					$root['show_err'] = '回款失败，修改投资人已回款状态失败';
					return $root;
				}
				//TODO 投资人资金变动
				$repay_user_id = $v['user_id'];
				//TODO 判断是否有转标
				if ($t_user_id = $dealLoadRepayDao->findCol('t_user_id',array('deal_id'=>$id,'user_id'=>$user_id,'l_key'=>$start_lkey)) != 0) {
					$repay_user_id = $t_user_id;
				}

				$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，回报本息';
				$edit_user_money = $userBusiness->editUserMoney($repay_user_id,round($user_inrepay_info['true_repay_money'],2),$log_msg,5);
				if($edit_user_money === false){
					$root['show_err'] = '还款失败！发放回报本息';
				}
				if($user_inrepay_info['impose_money'] >0 ) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前回收违约金';
					$edit_user_money = $userBusiness->editUserMoney($repay_user_id,round($user_inrepay_info['impose_money'],2),$log_msg,7);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！提前回收违约金';
						return $root;
					}
				}

				$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，投标管理费';
				$edit_user_money = $userBusiness->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_money'],2),$log_msg,20);
				if($edit_user_money === false){
					$root['show_err'] = '还款失败！扣除投标管理费出错';
					return $root;
				}
				if($user_inrepay_info['true_reward_money'] > 0){
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，投标奖励';
					$edit_user_money = $userBusiness->editUserMoney($repay_user_id,-round($user_inrepay_info['true_reward_money'],2),$log_msg,28);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除投标奖励出错';
						return $root;
					}
				}

				if ($user_inrepay_info['true_manage_early_interest_money'] > 0) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前还款利息管理费';
					$edit_user_money = $userBusiness->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_early_interest_money'],2),$log_msg,7);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除提前还款利息管理费出错';
						return $root;
					}
				}
				if($user_inrepay_info['true_manage_interest_money'] > 0) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，投标利息管理费';
					$edit_user_money = $userBusiness->editUserMoney($repay_user_id,-round($user_inrepay_info['true_manage_interest_money'],2),$log_msg,28);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除投标奖励出错';
						return $root;
					}
				}
				//TODO 普通会员邀请返利
				//判断该标是否参与分销返利
				if ($loanBaseDao->findCol('is_referral_award',array('id'=>$id)) != 0) {
					$deal_load_repay_id = $dealLoadRepayDao->findCol('id',array('deal_id'=>$id,'user_id'=>$user_id,'l_key'=>$start_lkey));
					$this->getReferrals($id,$deal_load_repay_id,$repay_user_id);
				}
				//TODO 投资者返佣
				if ($user_inrepay_info['true_manage_interest_money_rebate'] != 0) {
					//是否有上级，有上级则给上级返佣
					$rebate_user = $userDao->findCol('pid',array('id'=>$repay_user_id));
					if ($rebate_user != 0) {
						$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，返佣金';
						$editMoneyStatus = $userBusiness->editUserMoney($rebate_user, $user_inrepay_info['true_manage_interest_money_rebate'], $log_msg, 23);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，返佣金发放失败';
						}
					}
				}
				//TODO 发送通知短信、邮件、站内信
				//发送站内信。邮件短信
				$this->sendRepayRebackMessage($id,$start_lkey,$repay_user_id,$user_inrepay_info['true_repay_money'],$need_interest_money,$user_inrepay_info['impose_money'],1);
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
					$root['show_err'] = '更新当前期数据失败';
					return $root;
				}
				//第一期之后的期数
				$where_after_start_lkey = array();
				$where_after_start_lkey['deal_id'] = $id;
				$where_after_start_lkey['user_id'] = $user_id;
				$where_after_start_lkey['l_key > '] = $start_lkey;
				$repay_status = $dealRepayDao->update($repay_data,$where_after_start_lkey);
				if($repay_status === false) {
					$root['show_err'] = '更新后续期数据失败';
					return $root;
				}
				$insert_inrepay = $dealInrepayDao->insert($inrepay_data);
				if($insert_inrepay === false) {
					$root['show_err'] = '保存提前还款数据失败';
					return $root;
				}
				//数据修改成功，扣除借款人资金
				//提前还款违约金
				if($inrepay_info['impose_money'] >0 ) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前还款违约金';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['impose_money'],2),$log_msg,6);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除提前还款违约金失败';
						return $root;
					}
				}
				//提前还款管理费
				if($inrepay_info['true_manage_money'] >0 ) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前还款管理费';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_manage_money'],2),$log_msg,10);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除提前还款管理费失败';
						return $root;
					}
				}
				//提前还款抵押物管理费
				if($inrepay_info['true_mortgage_fee'] >0 ) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前还款抵押物管理费';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_mortgage_fee'],2),$log_msg,27);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除提前还款抵押物管理费失败';
						return $root;
					}
				}
				//提前还款本息
				if($inrepay_info['true_repay_money'] >0 ) {
					$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($start_lkey + 1) . '期，提前还款本息';
					$edit_user_money = \Core::business('user_userinfo')->editUserMoney($user_id,-round($inrepay_info['true_repay_money'],2),$log_msg,6);
					if($edit_user_money === false){
						$root['show_err'] = '还款失败！扣除提前还款本息失败';
						return $root;
					}
				}
				$update_deal_status = \Core::dao('loan_loanbid')->update(array('deal_status'=>5,'pay_off_status'=>1,'payoff_time'=>$time),array('loan_id'=>$id));
				if($update_deal_status === false) {
					$root['show_err'] = '更新贷款状态出错！';
					return $root;
				}
				//TODO 借款者返佣金
				$true_manage_money_rebate = round($inrepay_info['true_manage_money'] * floatval(C('BORROWER_COMMISSION_RATIO')) / 100,2);
				if($true_manage_money_rebate != 0 ) {
					//是否有上级，有上级则给上级返佣
					$rebate_user = $userDao->findCol('pid',array('id'=>$user_id));
					if($rebate_user != 0) {
						$log_msg = '<a href="" target="_blank">'.$loanBaseDao->getName($id).'</a>第'.($v['l_key']+1).'期，返佣金';
						$edit_user_money = $userBusiness->editUserMoney($rebate_user,$true_manage_money_rebate,$log_msg, 23);
						if($edit_user_money === false) {
							$root['show_err'] = '借款者返佣出错！';
							return $root;
						}
					}
				}
				//TODO 用户获得额度
				$log_msg = "[<a href='' target='_blank'>" .$loanBaseDao->getName($id). "</a>],还清借款获得额度";
				$userBusiness->editUserQuota($user_id,trim(C('USER_REPAY_QUOTA')),$log_msg, 6);
				//TODO 判断借款人是否获得信用
				//判断获得信用是否超过上限
				$point_sum = \Core::dao('user_userPointLog')->getSumPoint(array('user_id'=>$user_id,'type'=>6));
				if($point_sum < intval(C('REPAY_SUCCESS_LIMIT'))) {
					//获取上一次还款时间
					$max_time =  \Core::dao('user_userPointLog')->getMaxTime(array('user_id'=>$user_id,'type'=>6));
					$day = ceil(($time - $max_time) / 24 / 3600);
					if($day >= intval(C('REPAY_SUCCESS_DAY'))) {
						$point = intval(C('REPAY_SUCCESS_POINT'));
						$log_msg = "[<a href='' target='_blank'>" . $loan_name . "</a>],还清借款";
						//增加借款用户信用
						$userBusiness->editUserPoint($user_id,$point,$log_msg,4);
					}
				}
				//TODO 发送还款成功短信、站内信
				$this->sendRepayMessage($id,$start_lkey,$user_id,$inrepay_info['true_repay_money'],$inrepay_info['true_manage_money'],$inrepay_info['impose_money'],$inrepay_info['true_manage_money'],1);
				//TODO 回滚债权
				$this->transReback($id);
				//TODO 更新债权转让状态
				$root['status'] = 1;
				$root['show_err'] = '还款成功';
				return $root;
			}
		}catch (\Exception $e){
			$loanBaseDao->getDb()->rollback();
			$root['show_err'] = '系统错误';
			return $root;
		}finally{
			if($root['status'] == 1) {
				$loanBaseDao->getDb()->commit();
			}else {
				$loanBaseDao->getDb()->rollback();
			}
		}
	}
	//网站资金代还
	public function siteRepay($id,$lkey,$user_id){
		$root = array();
		$root['status'] = 0;//0:出错;1:正确;
		$id = intval($id);
		if($id == 0 ) {
			$root['show_err'] = '操作失败';
			return $root;
		}
		$user_id = intval($user_id);
		if($user_id == 0 ) {
			$root['show_err'] = '操作失败';
			return $root;
		}
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
		if(!$repay_id) {
			$root['show_err'] = '数据异常';
			return $root;
		}
		$time = time();
		//判断是否逾期
		$userRepayImposeInfo = \Core::business('sys_dealrepay')->repayPlanImpose($id,$lkey);
		if(!$userRepayImposeInfo) {
			$root['show_err'] = '数据异常';
			return $root;
		}
		$need_repay_money = $userRepayImposeInfo['need_repay_money'];
		$status = ($userRepayImposeInfo['status']<2)?($userRepayImposeInfo['status']+1):$userRepayImposeInfo['status'];
		$impose_money = $userRepayImposeInfo['impose_money'];
		$manage_impose_money  = $userRepayImposeInfo['manage_impose_money'];
		//TODO 开启事务
		$loanbaseDao->getDb()->begin();
		//贷款名称
		$loan_name = $loanbaseDao->getName($id);
		try{
			//1.投资人回款
			foreach ($load_user_list as $v) {
				//获取所有投资用户该期的回款计划
				$user_load = $dealLoadRepayDao->getSomeOneLkeyPlan($id,$lkey,$v['user_id']);
				if(!$user_load ) {
					$root['show_err'] = '数据异常';
					return $root;
				}
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
						$log_impose_msg = '<a href="" target="_blank">债权标</a>' . $user_load['id'] . '第' . ($lkey + 1) . '期，逾期罚息';
					} else {
						$invest_user_id = $v['user_id'];
						$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($lkey + 1) . '期，回报本息';
						$log_impose_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($lkey + 1) . '期，逾期罚息';
					}
					//修投资人余额
					$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['repay_money'], $log_msg, 5);
					if($editMoneyStatus === false){
						$root['show_err'] = '回款失败，回报本息发放失败';
						return $root;
					}
					$message_data['repay_money'] = $user_load['repay_money'];
					if ($user_load['manage_money'] > 0) {
						$log_msg = '[<a href="" target="_blank">' . $loan_name . '</a>]第' . ($lkey + 1) . '期，投标管理费';
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_money'], $log_msg, 20);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，扣除投标管理费失败';
							return $root;
						}
					}
					if ($user_load['manage_interest_money'] > 0) {
						$log_msg = '[<a href="" target="_blank">' . $loan_name . '</a>]第' . ($lkey + 1) . '期，投标利息管理费';
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, -$user_load['manage_interest_money'], $log_msg, 20);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，扣除投标利息管理费失败';
							return $root;
						}
					}
					//逾期罚息
					$user_impose_money = $impose_money*($user_load['repay_money']/$v['money']);
					if ($user_impose_money != 0) {
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, number_format($impose_money*($user_load['repay_money']/$v['money']),2), $log_impose_msg, 21);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，逾期罚息发放失败';
							return $root;
						}
					}
					//投资者奖励
					if ($user_load['reward_money'] != 0) {
						$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($lkey + 1) . '期，奖励收益';
						$editMoneyStatus = $userBusiness->editUserMoney($invest_user_id, $user_load['reward_money'], $log_msg, 28);
						if($editMoneyStatus === false){
							$root['show_err'] = '回款失败，奖励收益发放失败';
							return $root;
						}
					}
					//TODO 普通会员邀请返利
					//判断该标是否参与分销返利
					if ($loanbaseDao->findCol('is_referral_award',array('id'=>$id)) != 0) {
						$deal_load_repay_id = $dealLoadRepayDao->findCol('id',array('deal_id'=>$id,'user_id'=>$user_id,'l_key'=>$lkey));
						$this->getReferrals($id,$deal_load_repay_id,$invest_user_id);
					}
					//投资者返佣金
					if ($user_load['manage_interest_money_rebate'] != 0) {
						//是否有上级，有上级则给上级返佣
						$rebate_user = $userDao->findCol('pid',array('id'=>$invest_user_id));
						if ($rebate_user != 0) {
							$log_msg = '<a href="" target="_blank">' . $loan_name . '</a>第' . ($lkey + 1) . '期，返佣金';
							$editMoneyStatus = $userBusiness->editUserMoney($rebate_user, $user_load['manage_interest_money_rebate'], $log_msg, 23);
							if($editMoneyStatus === false){
								$root['show_err'] = '回款失败，返佣金发放失败';
								return $root;
							}
						}
					}
					//发送站内信。邮件短信
					$this->sendRepayRebackMessage($id,$lkey,$invest_user_id,$user_load['repay_money'],$user_load['interest_money'],$user_impose_money,0,$time);
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
					$rebate_user = $userDao->findCol('pid',array('id'=>$user_id));
					if($rebate_user != 0) {
						$log_msg = '<a href="" target="_blank">'.$loan_name.'</a>第'.($v['l_key']+1).'期，返佣金';
						$editMoneyStatus = $userBusiness->editUserMoney($rebate_user,$repay_data['true_manage_money_rebate'],$log_msg, 23);
						if($editMoneyStatus === false) {
							$root['show_err'] = '代还失败，返佣失败';
							return $root;
						}
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
						$log_msg = "[<a href='' target='_blank'>" . $loan_name. "</a>],第" . ($lkey + 1) . "期,逾期还款";
					}else {
						//严重逾期扣分
						$point = C('YZ_IMPOSE_POINT')?C('YZ_IMPOSE_POINT'):0;
						$log_msg = "[<a href='' target='_blank'>" . $loan_name. "</a>],第" . ($lkey + 1) . "期,严重逾期";
					}
					if($point != 0) {
						$update_point_status = \Core::business('user_userinfo')->editUserPoint($user_id,$point,$log_msg,11);
						if($update_point_status === false) {
							$root['show_err'] = '代还失败，扣除借款者逾期积分失败';
							return $root;
						}
					}
				}
				//整理还款记录数据
				//网站代还统计
				$site_repay_sum = $dealLoadRepayDao->getHasSiteRepayTotal($id,$lkey);
				$dealRepayLogBusiness = \Core::business('loan_dealrepaylog');
				if($site_repay_sum) {
					//记录还款日志
					$repay_msg = '网站代还款，本息：' . $site_repay_sum['total_repay_money'];
					$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
					if($add_repay_log_status === false) {
						$root['show_err'] = '保存还款日志失败';
						return $root;
					}
					//罚息
					if($site_repay_sum['total_impose_money'] != 0) {
						$repay_msg = '网站代还款，逾期费用：' . $site_repay_sum['total_impose_money'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '保存还款日志失败';
							return $root;
						}
					}
					//借款管理费
					if ($site_repay_sum['total_manage_money'] > 0 && $get_manage == 0) {
						$repay_msg = '网站代还款，管理费：' .$site_repay_sum['total_manage_money'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '保存还款日志失败';
							return $root;
						}
					}
					//抵押物管理费
					if($site_repay_sum['total_mortgage_fee'] > 0 ) {
						$repay_msg = '网站代还款，抵押物管理费：' . $site_repay_sum['total_mortgage_fee'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '保存还款日志失败';
							return $root;
						}
					}
					//逾期管理费
					if($site_repay_sum['total_repay_manage_impose_money'] > 0) {
						$repay_msg = '会员还款，逾期管理费：' . $site_repay_sum['total_repay_manage_impose_money'];
						$add_repay_log_status = $dealRepayLogBusiness->addDealRepayLog($repay_id,$user_id,$repay_msg);
						if($add_repay_log_status === false) {
							$root['show_err'] = '保存还款日志失败';
							return $root;
						}
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
					$add_generation_status = $generationDao->insert($generation_repay);
					if($add_generation_status === false) {
						$root['show_err'] = '保存代款数据失败';
						return $root;
					}
					//记录网站代还日志
					$site_money_data['user_id'] = $user_id;
					$site_money_data['create_time'] = $time;
					if($repay_sum['total_repay_manage_money'] != 0 && $get_manage == 0) {
						$site_money_data['memo'] = "[<a href='' target='_blank'>" . $loan_name . "</a>],第" . ($lkey+1) . "期,借款管理费";
						$site_money_data['type'] = 10;
						$site_money_data['money'] = $repay_sum['total_repay_manage_money'];
						$add_site_money_status = $siteMoneyLogDao->insert($site_money_data);
						if($add_site_money_status === false) {
							$root['show_err'] = '保存代款数据失败';
							return $root;
						}
					}
					if($repay_sum['total_repay_manage_impose_money'] != 0 ) {
						$site_money_data['memo'] = "[<a href='' target='_blank'>" .$loan_name. "</a>],第" . ($lkey+1) . "期,逾期管理费";
						$site_money_data['type'] = 12;
						$site_money_data['money'] = $repay_sum['total_repay_manage_impose_money'];
						$add_site_money_status = $siteMoneyLogDao->insert($site_money_data);
						if($add_site_money_status === false) {
							$root['show_err'] = '保存代款数据失败';
							return $root;
						}
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
			return $root;
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			$root['show_err'] = '系统错误';
			return $root;
		}finally{
			if($root['status'] == 1) {
				$userDao->getDb()->commit();
			}else {
				$userDao->getDb()->rollback();
			}
		}
	}
	/*
	 * 还款返利(普通会员邀请返利)
	 * $loan_id 贷款id
	 * $deal_repay_id 回款计划id
	 * $user_id 回款用户id（有转标则为转标承接人id）
	 * */
	public function getReferrals($loan_id,$deal_repay_id,$user_id){
		if(!$deal_repay_id) return false;
		if(!$loan_id) return false;
		if(!$user_id) return false;
		$dealLoadRepayDao = \Core::dao('loan_dealloadrepay');
		$loanBaseDao = \Core::dao('loan_loanbase');
		$userDao = \Core::dao('user_user');
		$dealLoadDao = \Core::dao('loan_dealload');
		$referralsDao = \Core::dao('loan_referrals');
		$time = time();
		//返利用户信息
		$user_where = array('id'=>$user_id,'pid >'=>0,'referral_rate >'=>0,'user_type <'=>2);

		$user_info = $userDao->getUserInfo('user_name,referral_rate,pid,create_time,referral_time',$user_where)->row();
		if(!$user_info) return false;
		if (intval(C("INVITE_REFERRALS_DATE")) > 0) {
			//计算分销有效期
			$after_year = 0;
			if($user_info['referral_time'] > -1) {
				$after_year = strtotime(date('Y-m-d',$time).'-'.$user_info['referral_time'].'month');
			}else {
				$after_year = strtotime(date('Y-m-d',$time).'-'.C('INVITE_REFERRALS_DATE').'month');
			}
		}
		//用户注册时间不在返利时间内
		if($user_info['create_time'] >= $after_year) return false;
		//获取回款信息
		$deal_load_repay_info = $dealLoadRepayDao->getDataById($deal_repay_id,'repay_time,l_key,load_id,true_interest_money,true_self_money');
		if(!$deal_load_repay_info) return false;
		$deal_load_info = $dealLoadDao->getDealLoad('id,create_time',array('create_time > '=>$after_year,'id'=>$deal_load_repay_info['load_id']));
		if(!$deal_load_info) return false;
		$referrals_money = 0;
		if(C('INVITE_REFERRALS_TYPE') == 0) {
			$referrals_money = $deal_load_repay_info['true_interest_money'] * $user_info['referral_rate'] *0.01;
		}else {
			$referrals_money = $deal_load_repay_info['true_self_money'] * $user_info['referral_rate'] *0.01;
		}
		if($referrals_money == 0) return false;
		//整理返利数据
		$data = array();
		$data['deal_id'] = $loan_id;
		$data['load_id'] = $deal_load_repay_info['load_id'];
		$data['l_key'] = $deal_load_repay_info['l_key'];
		$data['money'] = round($referrals_money,2);
		$data['user_id'] = $user_id;
		$data['rel_user_id'] = $user_info['pid'];
		$data['referral_type'] = intval(C("INVITE_REFERRALS_TYPE")) == 0 ? 0 : 1;
		$data['referral_rate'] = $user_info['referral_rate'];
		$data['repay_time'] = $deal_load_repay_info['repay_time'];
		$data['create_time'] = $time;
		//TODO 对数据库进行修改，开启事务
		$referralsDao->getDb()->begin();
		try{
			$referrals_id = true;
			$referrals_id = $referralsDao->insert($data);
			unset($data);
			if($referrals_id === false) return false;
			if(intval(C('INVITE_REFERRALS_AUTO')) == 1) {
				//自动发放返利
				$userBusiness = \Core::business('user_userinfo');
				$msg = '[<a href="" target="_blank">' .$loanBaseDao->getName($loan_id). '</a>],第' . ($deal_load_repay_info['l_key'] + 1) . '期,还款获取邀请返利';
				//修改用户余额
				$edit_status = $userBusiness->editUserMoney($user_id,round($referrals_money,2),$msg,23);
				if($edit_status === false) {
					$referrals_id = $edit_status;
					return $referrals_id;
				}
				//修改返利表时间
				$update_status = $referralsDao->update(array('pay_time'=>time()),array('id'=>$referrals_id));
				if($update_status === false) {
					$referrals_id = $update_status;
					return $referrals_id;
				}
			}
		}catch(\Exception $e){
			$referralsDao->getDb()->rollback();
			$referrals_id = false;
		}finally{
			if($referrals_id === false) {
				$referralsDao->getDb()->rollback();
				return $referrals_id;
			}else {
				$referralsDao->getDb()->commit();
				return $referrals_id;
			}
		}
	}
	/*
	 * 手动还款返利(普通会员邀请返利)
	 * $id 返利表id
	 * */
	public function payReferrals($id){
		if(!$id) return false;
		$loanBaseDao = \Core::dao('loan_loanbase');
		$referralsDao = \Core::dao('loan_referrals');
		$time = time();
		if($loanBaseDao->findCol('is_referral_award',array('id'=>$id)) == 0) return false;
		//返利信息
		$referrals_info = $referralsDao->getDataById($id);
		if(!$referrals_info) return false;
		//TODO 对数据库进行修改，开启事务
		$referralsDao->getDb()->begin();
		try{
			$referrals_id = true;
			if($referrals_info['money'] != 0) {
				//自动发放返利
				$userBusiness = \Core::business('user_userinfo');
				$msg = '[<a href="" target="_blank">' .$loanBaseDao->getName($referrals_info['deal_id']). '</a>],第' . ($referrals_info['l_key'] + 1) . '期,还款获取邀请返利';
				//修改用户余额
				$edit_status = $userBusiness->editUserMoney($referrals_info['user_id'],round($referrals_info['money'],2),$msg,23);
				if($edit_status === false) {
					$referrals_id = $edit_status;
					return $referrals_id;
				}
				//修改返利表时间
				$update_status = $referralsDao->update(array('pay_time'=>time()),array('id'=>$id));
				if($update_status === false) {
					$referrals_id = $update_status;
					return $referrals_id;
				}
			}
		}catch(\Exception $e){
			$referralsDao->getDb()->rollback();
			$referrals_id = false;
		}finally{
			if($referrals_id === false) {
				$referralsDao->getDb()->rollback();
				return $referrals_id;
			}else {
				$referralsDao->getDb()->commit();
				return $referrals_id;
			}
		}
	}
	//债权回滚
	public function transReback($id){
		$dealLoadTransDao = \Core::dao('loan_dealloadtransfer');
		$trans_info = $dealLoadTransDao->getTransInfoByLoanId($id);
		if($trans_info) {
			foreach ($trans_info as $k => $transfer) {
				if($transfer['status'] != 0) {
					if ($transfer['next_dltid'] > 0) {
						$update_trans_status = $dealLoadTransDao->update(array('status'=>0,'callback_count'=>$transfer['callback_count']+1),array('id'=>$transfer['next_dltid'],'t_user_id'=>0,'callback_count'=>$transfer['callback_count']));
						if ($update_trans_status !== false) {
							$dealLoadTransDao->update(array('status2'=>0),array('id'=>$transfer['id']));
						}
					} else {
						$dealLoadTransDao->update(array('status'=>0,'callback_count'=>$transfer['callback_count']+1),array('id'=>$transfer['id'],'t_user_id'=>0,'callback_count'=>$transfer['callback_count']));

					}
				}
			}
			return true;
		}else {
			return false;
		}
	}
}