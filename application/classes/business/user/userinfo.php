<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_user_userinfo extends Business {
	public function business() {
		
	}
	
	//通过用户名称模糊查找用户
	public function getUsersByName($userName){
		if($userName!=null && $userName!=''){
			$userDao=\Core::dao('user_user');
			return $userDao->getUsersByName($userName);
		}
		return array();
	}
}