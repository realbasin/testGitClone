<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_stat_dealaudit extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'date_time'//所统计数据的日期
				,'admin_id'//审核人员ID
				,'totals'//总审核笔数
				,'success_totals'//审核成功总笔数
				,'first_totals'//首借审核总笔数
				,'first_success_totals'//首借审核成功笔数
				,'renew_totals'//续借审核总笔数
				,'renew_success_totals'//续借审核成功笔数
				,'true_totals'//复审总笔数
				,'true_success_totals'//复审成功笔数
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_audit_stat';
	}

}
