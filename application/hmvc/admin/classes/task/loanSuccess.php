<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  task_loanSuccess extends Task {
	/*针对$args参数要做说明，以便以后进行配置
	 * 注意：task代码内不能写任何die,exit
	 * 放款成功后自动执行的任务
	 *@option deal_id int 借款ID
	 */
	public function execute(CliArgs $args) {
		$user_id = $args->get('user_id');
		echo $user_id;
		if($user_id && is_numeric($user_id)){
			$admin_id = \Core::dao('user_user')->getUser($user_id,'id,admin_id,platform_code');
			if($admin_id) {
				$loanBid = \Core::dao('loan_loanbid')->getOneLoanById($user_id,'load_money');
				$loanBase = \Core::dao('loan_loanbase')->getloanbase($user_id,'id,name,repay_time_type,repay_time');
				$admin_deal_info = array_merge($loanBase,$loanBid);
				$adminstatus = \Core::business('sys_admin_admin')->adminReferrals($admin_id[$user_id],$admin_deal_info);
				if($adminstatus['status'] == 1) {
					$result['message'] = $adminstatus['message'];
					$result['status'] = 1;
				}
			}else {
				$result['message'] = '无管理员';
				$result['status'] = 1;
			}
			return $result;
			//$loanBaseDao->update(array('is_effect' => 1, 'loan_status' => 1), array('id' => $deal_id));
			//发送短信发送邮件

		    //是否存在优投用户，存在发送推送

			//发借款成功邮件

			//发借款成功站内信

			//发送借款协议范本
		}
	}
}