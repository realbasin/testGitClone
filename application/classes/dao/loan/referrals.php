<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_referrals extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'// 被邀请人ID，即返利生成的用户ID
				,'rel_user_id'//邀请人ID（即需要返利的会员ID）
				,'money'//返利的现金
				,'create_time'//返利生成的时间
				,'repay_time'//返利时间
				,'pay_time'//返利发放的时间
				,'deal_id'//关联的借款id
				,'load_id'//关联的投标id
				,'l_key'//关联的投标第几期还款
				,'score'//返利的积分
				,'point'//返利的信用
				,'referral_type'//返利方式 0利息 1本金
				,'referral_rate'//返利抽成比
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'referrals';
	}

}
