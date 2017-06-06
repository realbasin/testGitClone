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
		$userDb = \Core::db();
		//$loanBidDao = \Core::dao('loan_loanbid');
		//$loanBaseDao = \Core::dao('loan_loanbase');
		$userDao = \Core::dao('user_user');
		//TODO 开启事务
		$userDb->begin();
		try{
			//当前用户余额
			$userMoney = $userDao->getUserMoney($user_id);
			$sql = "update _tablePrefix_user ";
			if($type == 3) {
				//放款sql
				$sql .= ",_tablePrefix_loan_bid,_tablePrefix_loan_base ";
			}
			$sql .= "set money_encrypt = AES_ENCRYPT
(ROUND(ifnull(AES_DECRYPT(money_encrypt,'".AES_DECRYPT_KEY."'),0) + ".floatval
				($money).",2),'".AES_DECRYPT_KEY."') where _tablePrefix_user.id =".$user_id." 
			and AES_DECRYPT(money_encrypt,'".AES_DECRYPT_KEY."')='".
				$userMoney."'";
			if($type == 3) {
				//放款sql
				$sql .= " and _tablePrefix_loan_bid.is_has_loans = 0 
					and _tablePrefix_loan_base.user_id = _tablePrefix_user.id 
					and _tablePrefix_loan_bid.loan_id = _tablePrefix_loan_base.id";
			}
			$flag = $userDb -> execute($sql);
			//修改金额后记录日志
			\Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('money'=>$money));
			\Core::business('user_userlog')->addUserMoneyLog($user_id,$log_msg,$money,$type);
		}catch (\Exception $e){
			$userDb->rollback();
			return false;
		}finally{
			if($flag === false){
				$userDb->rollback();
				return $flag;
			}else {
				$userDb->commit();
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
		//TODO 加锁
		$flag = false;
		$userDb = \Core::db();
		$userDao = \Core::dao('user_user');
		//TODO 开启事务
		$userDb->begin();
		try{
			$userLockMoney = $userDao->getUserLockMoneyById($user_id);
			$newmoney = $money + $userLockMoney;
			$data = array();
			$data['lock_money'] = $newmoney;
			$where = array();
			$where['lock_money'] = $userLockMoney;
			$where['id'] = $user_id;
			$flag = $userDao->update($data,$where);
			//修改后记录日志
			\Core::business('user_userlog')->addUserLockMoneyLog($user_id,$log_msg,$money,$type);
		}catch (\Exception $e){
			$userDb->rollback();
			return false;
		}finally{
			if($flag === false){
				$userDb->rollback();
				return $flag;
			}else {
				$userDb->commit();
				return true;
			}
		}
	}
	/*
	 *修改用户score
	 * @param $user_id 用户id
	 * @param $score 要变动的分数，带负号为减少，正数为增加
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserScore($user_id,$score,$log_msg,$type){
		//TODO 加锁
		$flag = false;
		//TODO 开启事务
		$userDb = \Core::db();
		$userDao = \Core::dao('user_user');
		$userDb->begin();
		try{
			$userScore = $userDao->getUserScoreById($user_id);
			$newscore = $score + $userScore;
			$data = array();
			$data['score'] = $newscore;
			$where = array();
			$where['score'] = $userScore;
			$where['id'] = $user_id;
			$flag = $userDao->update($data,$where);
			//修改后记录日志
			\Core::business('user_userlog')->addUserScoreLog($user_id,$log_msg,$score,$type);
			\Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('score'=>$score));
		}catch (\Exception $e){
			$userDb->rollback();
			return false;
		}finally{
			if($flag === false){
				$userDb->rollback();
				return $flag;
			}else {
				$userDb->commit();
				return true;
			}
		}
	}
}