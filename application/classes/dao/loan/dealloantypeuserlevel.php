<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealloantypeuserlevel extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'loan_type_id'//loan_type_id
				,'user_level_id'//user_level_id
				,'name'//等级名称
				,'point'//所需经验
				,'services_fee'//服务费率
				,'enddate'//贷款时间
				,'repaytime'//借款期限和借款利率【一行一配置】
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_loan_type_user_level';
	}

}
