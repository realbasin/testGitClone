<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userlog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'log_info'//日志内容
				,'log_time'//发生时间
				,'log_admin_id'//操作管理员的ID
				,'log_user_id'//操作的前台会员ID
				,'money'//相关的钱
				,'score'//相关的积分
				,'point'//相关的经验
				,'quota'//相关的额度
				,'lock_money'//相关的冻结资金
				,'user_id'//会员ID
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_log';
	}

}
