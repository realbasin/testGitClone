<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_collect_bidCollectLog extends Dao {

	public function getColumns() {
		return array(
				'log_id'//自动增长
				,'collec_id'//催收ID
				,'loan_id'//借款单ID
				,'collec_feedback'//用户反馈
				,'collec_info'//自定义内容
				,'collec_time'//催收记录创建时间
				);
	}

	public function getPrimaryKey() {
		return 'log_id';
	}

	public function getTable() {
		return 'bid_collect_log';
	}

}
