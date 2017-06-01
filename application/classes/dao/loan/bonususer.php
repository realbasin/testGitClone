<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_bonususer extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'bonus_sn'//优惠券号
				,'bonus_type_id'//优惠券类型id
				,'bonus_rule_id'//优惠券id
				,'user_id'//领取优惠券的用户id，如果为0，未有人领取
				,'user_name'//领取优惠券的用户名
				,'drawed_time'//领取优惠券时间
				,'used_time'//优惠券使用时间
				,'module'//使用优惠券的模型（deal,deal_load,deal_transfor)
				,'module_pk_Id'//模型表的id
				,'create_time'//记录创建时间
				,'issue_type'//领取方式：0-派发；1-手动发放
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'bonus_user';
	}
	//根据用户id获取优惠券id
	public function getBonusRuleIdByUserId($userid){
		return $this->getDb()->from($this->getTable())->where(array('user_id'))->execute()->value('bonus_rule_id');
	}
}
