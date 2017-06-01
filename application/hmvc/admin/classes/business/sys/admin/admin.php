<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_sys_admin_admin extends Business {
	public function business() {
		
	}
	//管理员提成
	public function adminreferrals($admin_id,$loanBase,$loanBid){
		$result = array();
		$result['status'] = 0;
		$result['is_post_yott'] = false; //记录是否有Yott用户投标
		if ($admin_id["platform_code"] == "yott") {
			$result['is_post_yott'] = true;
		}
		//获取部门成员
		$mymanager = \Core::dao('sys_admin_adminext')->getAdminById($admin_id,'id,referrals_rate,pid');
		if($mymanager  && floatval($mymanager['referrals_rate']) != 0) {
			$money = 0;
			$url = '';
			if ($loanBase['repay_time_type'] == 0) {
				//天 投资金额 × 投资的天数 / 365天
				$money = $loanBid['load_money'] * $loanBase['repay_time'] / 365;
			} else {
				//月标：投资金额 × 投资的月数 / 12个月
				$money = $loanBid['money'] * $loanBase['repay_time'] / 12;
			}
			$memo = "[<a href='" . $url . "' target='_blank'>" . $loanBase['name'] . "</a>],满标放款";
			$m_data = array();
			$m_data['deal_id'] = $loanBase['id'];
			$m_data['user_id'] = $admin_id['id'];
			$m_data['money'] = $money * floatval($mymanager['referrals_rate']) * 0.01;
			$m_data['rel_admin_id'] = 0;
			$m_data['admin_id'] = $mymanager['id'];
			$m_data['rel_admin_id'] = $mymanager['pid'];
			$m_data['create_time'] = getGmtime()+C('time_zone')*3600;
			$m_data['loan_money'] = $loanBid['money'];
			$m_data['memo'] = $memo;
			//插入数据
			$insertmdata = \Core::dao('sys_adminreferrals')->insert($m_data);
			//更新管理员提成金额
			if($insertmdata !== false){
				$adminReferralsMoney = \Core::dao('sys_adminreferrals')->getSumMoneyByAdminId($admin_id);
				$updateAdminMoney = \Core::dao('sys_admin_adminext')->update(array('referrals_money'=>$adminReferralsMoney),array('id'=>$admin_id));
				if($updateAdminMoney === false) {
					$result['status'] = 1;
					$result['message'] = "放款失败，更新管理员提成金额失败";
				}
			}
			//部门
			if($mymanager['pid'] > 0) {
				//获取提成比
				$mydepartment = \Core::dao('sys_admin_adminext')->getAdminById($mymanager['pid'],'id,referrals_rate,pid');
				$d_data['deal_id'] = $m_data['deal_id'];
				$d_data['user_id'] = $m_data['user_id'];
				$d_data['money'] = $m_data['money'] * floatval($mydepartment['referrals_rate']) * 0.01;
				$d_data['rel_admin_id'] = $admin_id['admin_id'];
				$d_data['admin_id'] = $mymanager['pid'];
				$d_data['create_time'] =  $m_data['create_time'];
				$d_data['loan_money'] = $m_data['loan_money'];
				$d_data['memo'] = $m_data['memo'];
				//插入数据
				$insertmdata = \Core::dao('sys_adminreferrals')->insert($d_data);
				//更新管理员提成金额
				if($insertmdata !== false){
					$adminReferralsMoney = \Core::dao('sys_adminreferrals')->getSumMoneyByAdminId($admin_id);
					$updateAdminMoney = \Core::dao('sys_admin_adminext')->update(array('referrals_money'=>$adminReferralsMoney),array('id'=>$mymanager['pid']));
					if($updateAdminMoney === false) {
						$result['status'] = 1;
						$result['message'] = "放款失败，更新管理员提成金额失败";
					}
				}
			}
		}
		return $result;
	}
}