<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_bonusrule extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'bonus_type_id'//优惠类型id
				,'money'//优惠券面额
				,'limit_amount'//使用最少金额, 如果为空，等同现金券
				,'num'//每次派送数量
				,'use_deal_month'//适用普通标的月份，以逗号分隔的字符串
				,'use_deal_load'//是否适用债权标，1适用 0不适用
				,'is_effect'//是否启用
				,'create_time'//记录创建时间
				,'is_delete'//标记删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'bonus_rule';
	}

}
