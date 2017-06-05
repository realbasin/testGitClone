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
	public function editUserMoney($user_id,$money,$log_msg,$type){
		//TODO 加锁
		$flag = false;
		$sql = "update _tablePrefix_user set money_encrypt = AES_ENCRYPT(ROUND(ifnull(AES_DECRYPT(money_encrypt,'".AES_DECRYPT_KEY."'),0) + ".floatval($money).",2),'".AES_DECRYPT_KEY."') where id =".$user_id;
		//开启事务
		\Core::db()->begin();
		try{
			$flag = \Core::db() -> execute($sql);
			\Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('money'=>$money));
			\Core::business('user_userlog')->addUserMoneyLog($user_id,$log_msg,$money,$type);
		}catch (\Exception $e){
			\Core::db()->rollback();
		}finally{
			//修改金额后记录日志
			if($flag === false){
				\Core::db()->rollback();
				return $flag;
			}else {
				\Core::db()->commit();
				return true;
			}
		}

	}
	/*
	 *修改用户lock_money
	 * @param $user_id 用户id
	 * @param $money 要变动的金额，带负号为减少余额，正数为增加余额
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserLockMoney($user_id,$money,$log_msg,$type){
		$flag = false;
		$sql = 'update _tablePrefix_user set lock_money = lock_money + '.floatval($money).' where id ='.$user_id;
		$flag = \Core::db() -> execute($sql);
		//修改后记录日志
		if($flag === false){
			return $flag;
		}else {
			\Core::business('user_userlog')->addUserLockMoneyLog($user_id,$log_msg,$money,$type);
			return true;
		}
	}
	/*
	 *修改用户score
	 * @param $user_id 用户id
	 * @param $score 要变动的分数，带负号为减少，正数为增加
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserScore($user_id,$score,$log_msg,$type){
		$flag = false;
		$sql = 'update _tablePrefix_user set score = score + '.floatval($score).' where id ='.$user_id;
		$flag = \Core::db() -> execute($sql);
		//修改后记录日志
		if($flag === false){
			return $flag;
		}else {
			 \Core::business('user_userlog')->addUserScoreLog($user_id,$log_msg,$score,2);
			 \Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('score'=>$score));
			return true;
		}
	}
}