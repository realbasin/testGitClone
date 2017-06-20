<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userlevel extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//等级名称
				,'point'//所需经验
				,'services_fee'//服务费率
				,'enddate'//贷款时间
				,'repaytime'//借款期限和借款利率【一行一配置】
				,'is_delete'//是否已删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_level';
	}

}
