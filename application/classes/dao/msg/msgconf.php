<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_msg_msgconf extends Dao {

	public function getColumns() {
		return array(
				'user_id'//user_id
				,'mail_asked'//有人对我的借款列表提问（邮件）
				,'sms_asked'//有人对我的借款列表提问（邮件）
				,'mail_bid'//有人向我的借款列表投标（邮件）
				,'sms_bid'//有人向我的借款列表投标（短信）
				,'mail_myfail'//我的借款列表流标（邮件）
				,'sms_myfail'//我的借款列表流标（短信）
				,'mail_half'//我的借款列表完成度超过50%
				,'sms_half'//我的借款列表完成度超过50%
				,'mail_bidsuccess'//我的投标成功
				,'sms_bidsuccess'//我的投标成功
				,'mail_fail'//我的投标流标
				,'sms_fail'//我的投标流标
				,'mail_bidrepaid'//我收到一笔还款
				,'sms_bidrepaid'//我收到一笔还款
				,'mail_answer'//借入者回答了我对借款列表的提问
				,'sms_answer'//借入者回答了我对借款列表的提问
				,'mail_transferfail'//债权转让失败提醒
				,'sms_transferfail'//债权转让失败提醒
				,'mail_transfer'//债权转让成功提醒
				,'sms_transfer'//债权转让成功提醒
				,'mail_redenvelope'//红包奖励提醒
				,'sms_redenvelope'//红包奖励提醒
				,'mail_rate'//收益率奖励提醒
				,'sms_rate'//收益率奖励提醒
				,'mail_integral'//积分奖励提醒
				,'sms_integral'//积分奖励提醒
				,'mail_gift'//礼品奖励提醒
				,'sms_gift'//礼品奖励提醒
				);
	}

	public function getPrimaryKey() {
		return 'user_id';
	}

	public function getTable() {
		return 'msg_conf';
	}

}
