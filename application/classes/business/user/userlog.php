<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_user_userlog extends Business {
	public function business() {
		
	}
	/*
	 *记录用户日志
	 * @param $user_id 用户id
	 * @param $log_msg 日志信息
	 * return 成功返回true 失败返回false
	 *  */
	public function addUserLog($user_id,$log_msg,$data){
		$log_info['log_info'] = $log_msg;
		$log_info['log_time'] = time();
		//当前登录的用户id
		$adm_cookie = \Core::cookie('admin');
		$admin = unserialize(\Core::decrypt($adm_cookie));
		$adm_id = intval($admin['id']);
		$log_info['log_admin_id'] = $adm_id;
		$log_info['log_user_id'] = intval(isset($data['user_id'])?$data['user_id']:0);
		$log_info['money'] = floatval(isset($data['money'])?$data['money']:0);
		$log_info['score'] = intval(isset($data['score'])?$data['score']:0);
		$log_info['point'] = intval(isset($data['point'])?$data['point']:0);
		$log_info['quota'] = floatval(isset($data['quota'])?$data['quota']:0);
		$log_info['lock_money'] = floatval(isset($data['lock_money'])?$data['lock_money']:0);
		$log_info['user_id'] = $user_id;
		$insert_id = \Core::dao('user_userlog')->insert($log_info);
		return $insert_id;
	}
	/*
	 *记录用户金额日志
	 * @param $user_id 用户id
	 * @param $log_msg 日志信息
	 * return 成功返回true 失败返回false
	 *  */
	public function addUserMoneyLog($user_id,$log_msg,$money=0,$type=0){
		$userDao = \Core::dao('user_user');
		$user_mark = $userDao->getUserMarkById($user_id);
		$user_name = $userDao->getUserNameById($user_id);
		$money_log_info = array();
		$money_log_info['memo'] = $log_msg;
		$money_log_info['money'] = floatval($money);
		$money_log_info['account_money'] =  $userDao->getUserMoney($user_id);
		$money_log_info['user_id'] = $user_id;
		$money_log_info['user_name'] = $user_name;
		$money_log_info['user_mark'] = $user_mark;
		$money_log_info['create_time'] = time();
		//$money_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
		//$money_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
		//$money_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
		$money_log_info['type'] = $type;
		$insert_id = \Core::dao('user_usermoneylog')->insert($money_log_info);
		return $insert_id;
	}
	/*
	 *记录用户冻结资金日志
	 * @param $user_id 用户id
	 * @param $log_msg 日志信息
	 * return 成功返回true 失败返回false
	 *  */
	public function addUserLockMoneyLog($user_id,$log_msg,$money=0,$type=0){
		$userDao = \Core::dao('user_user');
		$money_log_info['memo'] = $log_msg;
		$money_log_info['lock_money'] = floatval($money);
		$money_log_info['account_lock_money'] = $userDao->getUserLockMoneyById($user_id);
		$money_log_info['user_id'] = $user_id;
		$money_log_info['create_time'] = time();
		//$money_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
		//$money_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
		//$money_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
		$money_log_info['type'] = $type;
		$insert_id = \Core::dao('user_userlockmoneylog')->insert($money_log_info);
		return $insert_id;
	}
	/*
	 *记录用户冻结资金日志
	 * @param $user_id 用户id
	 * @param $log_msg 日志信息
	 * return 成功返回true 失败返回false
	 *  */
	public function addUserScoreLog($user_id,$log_msg,$score=0,$type=0){
		$userDao = \Core::dao('user_user');
		$score_log_info['memo'] = $log_msg;
		$score_log_info['score'] = floatval($score);
		$score_log_info['account_score'] = $userDao->getUserScoreById($user_id);
		$score_log_info['user_id'] = $user_id;
		$score_log_info['create_time'] = time();
		//$score_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
		//$score_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
		//$score_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
		$score_log_info['type'] = $type;
		$insert_id = \Core::dao('user_userscorelog')->insert($score_log_info);
		return $insert_id;
	}
}