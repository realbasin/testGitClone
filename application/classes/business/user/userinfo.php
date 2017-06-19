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
		$userDao = \Core::dao('user_user');
		//TODO 开启事务
		$userDao->getDb()->begin();
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
			$flag = $userDao->getDb() -> execute($sql);
			//修改金额后记录日志
			$user_log_id = \Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('money'=>$money));
			$user_money_log_id = \Core::business('user_userlog')->addUserMoneyLog($user_id,$log_msg,$money,$type);
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			return false;
		}finally{
			if($flag === false || $user_log_id === false || $user_money_log_id === false){
				$userDao->getDb()->rollback();
				return false;
			}else {
				$userDao->getDb()->commit();
				return true;
			}
			//TODO 交易托管
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
		$userDao = \Core::dao('user_user');
		//TODO 开启事务
		$userDao->getDb()->begin();
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
			$user_lock_money_log_id = \Core::business('user_userlog')->addUserLockMoneyLog($user_id,$log_msg,$money,$type);
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			return false;
		}finally{
			if($flag === false || $user_lock_money_log_id === false){
				$userDao->getDb()->rollback();
				return false;
			}else {
				$userDao->getDb()->commit();
				return true;
			}
			//TODO 交易托管
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
		$userDao = \Core::dao('user_user');
		$userDao->getDb()->begin();
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
			$user_score_log_id = \Core::business('user_userlog')->addUserScoreLog($user_id,$log_msg,$score,$type);
			$user_log_id = \Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('score'=>$score));
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			return false;
		}finally{
			if($flag === false || $user_score_log_id === false || $user_log_id === false){
				$userDao->getDb()->rollback();
				return false;
			}else {
				$userDao->getDb()->commit();
				return true;
			}
		}
	}

	/*
	 *修改用户point
	 * @param $user_id 用户id
	 * @param $point 要变动的分数，带负号为减少，正数为增加
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserPoint($user_id,$point,$log_msg,$type){
		//TODO 加锁
		$flag = false;
		//TODO 开启事务
		$userDao = \Core::dao('user_user');
		$userDao->getDb()->begin();
		try{
			$userPoint = $userDao->findCol('point',array('id'=>$user_id));
			$newpoint = $point + $userPoint;
			$data = array();
			$data['point'] = $newpoint;
			$where = array();
			$where['point'] = $userPoint;
			$where['id'] = $user_id;
			$flag = $userDao->update($data,$where);
			//修改后记录日志
			$user_point_log_id = \Core::business('user_userlog')->addUserPointLog($user_id,$log_msg,$point,$type);
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			return false;
		}finally{
			if($flag === false || $user_point_log_id === false){
				$userDao->getDb()->rollback();
				return false;
			}else {
				$userDao->getDb()->commit();
				return true;
			}
		}
	}

	/*
	 *修改用户额度
	 * @param $user_id 用户id
	 * @param $quota 要变动的额度，带负号为减少，正数为增加
	 * return 成功返回true 失败返回false
	 *  */
	public function editUserQuota($user_id,$quota,$log_msg,$type){
		//TODO 加锁
		$flag = false;
		//TODO 开启事务
		$userDao = \Core::dao('user_user');
		$userDao->getDb()->begin();
		try{
			$userQuota = $userDao->findCol('quota',array('id'=>$user_id));
			$newquota = $quota + $userQuota;
			$data = array();
			$data['quota'] = $newquota;
			$where = array();
			$where['quota'] = $userQuota;
			$where['id'] = $user_id;
			$flag = $userDao->update($data,$where);
			//修改后记录日志
			$user_log_id = \Core::business('user_userlog')->addUserLog($user_id,$log_msg,array('quota'=>$quota,'user_id'=>$user_id));
		}catch (\Exception $e){
			$userDao->getDb()->rollback();
			return false;
		}finally{
			if($flag === false || $user_log_id === false){
				$userDao->getDb()->rollback();
				return false;
			}else {
				$userDao->getDb()->commit();
				return true;
			}
		}
	}

	//借款三级分销
	public function distributionRebate($deal_id,$user_id,$depth=1){
		$result = array();
		$result['message'] = '';
		if (!$deal_id || !$user_id) {
			$result['message'] = '参数错误';
			return $result;
		}
		if($depth > C('DISTRIBUTION_DEPTH')) {
			$result['message'] = '分销深度超出配置';
			return $result;
		}
		//获取贷款信息
		$deal_info = \Core::dao('loan_loanbase')->getloanbase($deal_id,'id,create_time,borrow_amount,is_referral_award');
		if(!$deal_info) {
			$result['message'] = '贷款信息不存在';
			return $result;
		}
		//改标不参与返利
		if($deal_info['is_referral_award'] == 0) {
			$result['message'] = '不参与返利';
			return $result;
		}
		$user = \Core::dao('user_user')->getUser($user_id,'id,user_type,referral_time,pid');
		if(!$user) {
			$result['message'] = '用户信息不存在';
			return $result;
		}
		$user_info = $user[$user_id];
		//只有普通用户才能参与分销
		if($user_info['user_type'] != 0) {
			$result['message'] = '只有普通用户才能参与分销';
			return $result;
		}
		//计算分销有效期
		$after_year = 0;
		if($user_info['referral_time'] > -1) {
			$after_year = strtotime(date('Y-m-d',time()).'-'.$user_info['referral_time'].'month');
		}else {
			$after_year = strtotime(date('Y-m-d',time()).'-'.C('INVITE_REFERRALS_DATE').'month');
		}
		if(C('DISTRIBUTION_DEPTH_REBATE_DATE') > 0) {
			//创建时间小于分销有效期，不再返利
			if (intval($deal_info['create_time']) < $after_year) {
				$result['message'] = '贷款分销期限已过';
				return $result;
			}
		}
		//用户是否有上级
		if($user_info && $user_info['pid'] > 0) {
			//获取上级用户
			$p_user = \Core::dao('user_user')->getUser($user_info['pid'],'id,referral_time,create_time');
			if(!$p_user) {
				$result['message'] = '上级用户不存在';
				return $result;
			}
			$p_user_info = $p_user[$user_info['pid']];
			if($p_user_info) {
				//return $p_user_info;
				//分销返利
				//返利
				$rebate = C('DISTRIBUTION_DEPTH_REBATE_'.$depth);
				if(!$rebate) {
					$result['message'] = '返利比例不存在';
					return $result;
				}
				$rebate_money = $deal_info['borrow_amount'] * floatval($rebate) / 100;
				if(C('DISTRIBUTION_DEPTH_REBATE_DATE') > 0) {
					//注册时间小于分销有效期，不再返利
					if (intval($p_user_info['create_time']) < $after_year) {
						$result['message']  = '注册时间小于分销有效时间';
						return $result;
					}
				}
				//整合返利数据
				$data = array();
				$data['deal_id'] = $deal_info['id'];
				$data['load_id'] = 0;
				$data['l_key'] = 0;
				$data['money'] = $rebate_money;
				$data['user_id'] = $user_id;
				$data['rel_user_id'] = $p_user_info['id'];
				$data['referral_type'] = 1;
				$data['referral_rate'] = $rebate * 100;
				$data['repay_time'] = time();
				$data['create_time'] = time();
				$data['pay_time'] = time();
				//插入数据
				$referralsId = \Core::dao('loan_referrals')->insert($data);
				if($referralsId > 0 ) {
					$edit_money_result = $this->editUserMoney($p_user_info['id'],$rebate_money,$depth . '级借款分销返利',121);
					if(!$edit_money_result) {
						$result['message'] = '修改用户余额时出错';
						return $result;
					}
				}
				$this->distributionRebate($deal_id,$p_user_info['id'],$depth+1);
			}
		}
		$result['message'] = '无上级，停止返利';
		return $result;
	}
	//理财三级分销返利
	public function bidDistributionRebate($deal_id,$load_info,$user_id,$depth=1){
		if (!$deal_id || !$user_id || !$load_info) return;
		if($depth > C('DISTRIBUTION_DEPTH')) return;
		//获取贷款信息
		$deal_info = \Core::dao('loan_loanbase')->getloanbase($deal_id,'id,create_time,borrow_amount,is_referral_award');
		if(!$deal_info) return;
		//改标不参与返利
		if($deal_info['is_referral_award'] == 0) return;
		//获取投资用户
		$userDao = \Core::dao('user_user');
		$user = $userDao->getUser($user_id,'id,user_type,referral_time,pid');
		if(!$user) return;
		$user_info = $user[$user_id];
		//只有普通用户才能参与分销
		if($user_info['user_type'] != 0) return;
		//部分理财用户的推奖奖励时间变为3年 WWW-699
		$invest_user_ids = array(
			79,     //Vigar
			103,    //caihua1230
			13677,  //okfan
			64456,  //13549116954
			6434,   //tianping
			63302,  //tianxiafeng
			22319,  //kuenydeng
			63312,  //a13724473401
			64454,  //18922987480
		);
		//分销有效期
		$after_year = 0;
		if (in_array($user_info['pid'],$invest_user_ids)) {
			$after_year = strtotime(date('Y-m-d',time()).'-36'.'month');
		} elseif($user_info['referral_time']>-1){
			$after_year = strtotime(date('Y-m-d',time()).'-'.$user_info['referral_time'].'month');
		} else {
			$after_year = strtotime(date('Y-m-d',time()).'-'.C('INVITE_REFERRALS_DATE').'month');
		}
		if(C('DISTRIBUTION_DEPTH_REBATE_DATE') > 0) {
			//创建时间小于分销有效期，不再返利
			if (intval($deal_info['create_time']) < $after_year) return;
		}
		//找上级
		if($user_info && $user_info['pid'] > 0 ) {
			$rebate = 0;
			$rebate_money = 0;
			$p_user = $userDao->getUser($user_info['pid'],'id,referral_time,create_time');
			if(!$p_user) return;
			$p_user_info  = $p_user[$user_info['pid']];
			if($p_user_info) {
				$rebate = C('DISTRIBUTION_DEPTH_REBATE_'.$depth);
				if(!$rebate) return;
				$rebate_money = $load_info['money'] * floatval($rebate) / 100;
				//整合返利数据
				$data = array();
				$data['deal_id'] = $deal_info['id'];
				$data['load_id'] = $load_info['id'];;
				$data['l_key'] = 0;
				$data['money'] = $rebate_money;
				$data['user_id'] = $user_id;
				$data['rel_user_id'] = $p_user_info['id'];
				$data['referral_type'] = 1;
				$data['referral_rate'] = $rebate * 100;
				$data['repay_time'] = time();
				$data['create_time'] = time();
				$data['pay_time'] = time();
				//插入数据
				$referralsId = \Core::dao('loan_referrals')->insert($data);
				if($referralsId > 0 ) {
					$edit_money_result = $this->editUserMoney($p_user_info['id'],$rebate_money,$depth . '级理财分销返利',121);
					if(!$edit_money_result) return;
					//TODO 返利的短信通知
				}
				$this->bidDistributionRebate($deal_id,$load_info,$p_user_info['id'],$depth+1);
			}
		}
		return;
	}

	//获取芝麻信用信息
	public function zhimaCredit()
	{

	}

	//同盾决策
	public function tongdun_credit()
	{

	}
	
	//百融黑名单处理
	public function bairong_credit()
	{
		
	}
}