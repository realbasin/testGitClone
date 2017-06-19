<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_collect_bidCollect extends Dao {

	public function getColumns() {
		return array(
				'collec_id'//自动ID
				,'loan_id'//对应loan_id
				,'collec_admin_id'//分配给的催收员id
				,'collec_create_time'//催收单创建时间
				,'collec_end_time'//催收结束时间，M值变化则自动转相应级别催收人 如果是M0还款则直接结束，直到下次还款前3天再转入M0催收组
				,'collec_repay_times'//催收期间自动还款笔数
				,'collec_repay_amount'//催收期间还款总金额
				,'collec_result'//催收结果 0失败 1成功
				);
	}

	public function getPrimaryKey() {
		return 'collec_id';
	}

	public function getTable() {
		return 'bid_collect';
	}

}
