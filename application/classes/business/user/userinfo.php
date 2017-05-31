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

	/*
	 *修改用户的余额
	 * 余额为加密字段，
	 * @param $user_id 用户id
	 * @param $money 要变动的金额，带负号为减少余额，正数为增加余额
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserMoney($user_id,$money){
		$sql = "update _tablePrefix_user set money_encrypt = AES_ENCRYPT(ROUND(ifnull(AES_DECRYPT(money_encrypt,'".AES_DECRYPT_KEY."'),0) + ".floatval($money).",2),'".AES_DECRYPT_KEY."') where id =".$user_id;
		return \Core::db() -> execute($sql);
		//return $userDao->editUserMoney($where,$update);
	}
	/*
	 *修改用户lock_money
	 * @param $user_id 用户id
	 * @param $money 要变动的金额，带负号为减少余额，正数为增加余额
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserLockMoney($user_id,$money){
		$sql = 'update _tablePrefix_user set lock_money = lock_money + '.floatval($money).' where id ='.$user_id;
		return \Core::db() -> execute($sql);
		//return $userDao->editUserMoney($where,$update);
	}
	/*
	 *修改用户score
	 * @param $user_id 用户id
	 * @param $score 要变动的分数，带负号为减少，正数为增加
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserScore($user_id,$score){
		$sql = 'update _tablePrefix_user set score = score + '.floatval($score).' where id ='.$user_id;
		return \Core::db() -> execute($sql);
		//return $userDao->editUserMoney($where,$update);
	}
}