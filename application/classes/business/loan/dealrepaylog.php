<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_dealrepaylog extends Business {
	public function business() {
		
	}
	//记录dealrepaylog日志信息
	public function addDealRepayLog($repay_id,$user_id,$log_msg,$admin_id=0){
		$repay_log = array();
		$repay_log['repay_id'] = $repay_id;
		$repay_log['log'] = $log_msg;
		$repay_log['user_id'] = intval($user_id);
		$repay_log['adm_id'] = $admin_id;
		$repay_log['create_time'] = time();
		return \Core::dao('loan_dealrepaylog')->insert($repay_log);
	}
}