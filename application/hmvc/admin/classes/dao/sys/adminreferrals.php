<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_adminreferrals extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'memo'//操作备注
				,'deal_id'//关联贷款
				,'user_id'//投资账户
				,'rel_admin_id'//关联管理员ID
				,'admin_id'//管理员ID
				,'money'//提成金额
				,'create_time'//发生时间
				,'create_date'//发生时间 YMD
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'admin_referrals';
	}
 	//根据管理员id获取总的提成金额
	public function getSumMoneyByAdminId($admin_id){
		return $this->getDb()->select('sum(money) as adminmoney')->from($this->getTable())->where(array('admin_id'=>$admin_id))->execute()->value('adminmoney');
	}
}
