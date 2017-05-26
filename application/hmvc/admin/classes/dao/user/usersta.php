<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_usersta extends Dao {

	public function getColumns() {
		return array(
				'user_id'//user_id
				,'dp_count'//留言数
				,'borrow_amount'//总的借款数
				,'repay_amount'//已还本息
				,'need_repay_amount'//待还本息
				,'need_manage_amount'//待还管理费
				,'avg_rate'//平均借款利率
				,'avg_borrow_amount'//平均每笔借款金额
				,'deal_count'//总借入笔数
				,'success_deal_count'//成功借款
				,'repay_deal_count'//还清笔数
				,'tq_repay_deal_count'//提前还清
				,'zc_repay_deal_count'//正常还清
				,'wh_repay_deal_count'//未还清
				,'yuqi_count'//逾期次数
				,'yz_yuqi_count'//严重逾期次数
				,'yuqi_amount'//逾期本息
				,'yuqi_impose'//逾期费用
				,'load_earnings'//已赚利息
				,'load_tq_impose'//提前还款违约金
				,'load_yq_impose'//逾期还款违约金
				,'load_avg_rate'//借出加权平均收益率
				,'load_count'//总借出笔数
				,'load_money'//总的借出金额
				,'load_repay_money'//已回收本息
				,'load_wait_repay_money'//待回收本息
				,'reback_load_count'//收回的借出笔数
				,'wait_reback_load_count'//未收回的借出笔数
				,'load_wait_earnings'//待回收利息
				,'bad_count'//坏账数量
				,'rebate_money'//返利金额
				);
	}

	public function getPrimaryKey() {
		return 'user_id';
	}

	public function getTable() {
		return 'user_sta';
	}
	//getdataByid
	public function getByUserId($user_id,$fields='*') {
		return $this->getDb()->select($fields)->from($this->getTable())->where(array('user_id'=>$user_id))->execute()->row();
	}
}
