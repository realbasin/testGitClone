<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userautobid extends Dao {

	public function getColumns() {
		return array(
				'user_id'//user_id
				,'min_amount'//每次最小投标金额
				,'max_amount'//每次最大投标金额
				,'fixed_amount'//每次投标金额
				,'min_rate'//最小利息
				,'max_rate'//最大利息
				,'min_period'//最低借款期限
				,'max_period'//最高借款期限
				,'min_level'//最低信用等级
				,'max_level'//最高信用等级
				,'retain_amount'//账户保留金额
				,'is_effect'//是否开启  0关闭 1开启
				,'last_bid_time'//最后一次投标时间
				,'deal_cates'//deal_cates
				,'is_autotrans'//是否开启自动债权承接
				,'is_use_bonus'//是否可使用理财优惠券：0-否；1-是
				);
	}

	public function getPrimaryKey() {
		return 'user_id';
	}

	public function getTable() {
		return 'user_autobid';
	}
	

}
