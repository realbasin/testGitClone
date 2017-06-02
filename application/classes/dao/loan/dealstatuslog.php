<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealstatuslog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//deal_id
				,'user_id'//user_id
				,'type'//0、提交成功；1、同盾通过；2、同盾拒绝；3、认领；4、初审通过；5、回退初审通过；6、初审失败；7、复审通过；8、回退初审；9、满标放款；10、借款协议生效；11、流标；12、提现申请-已付款；
				,'create_time'//create_time
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_status_log';
	}
	
}
