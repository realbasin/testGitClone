<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_loanbid extends Dao {

	public function getColumns() {
		return array(
				'loan_id'//对应loan表的id
				,'min_loan_money'//最低投标额度
				,'max_loan_money'//最高投标额度
				,'start_time'//开始招标时间
				,'end_time'//结束时间对应endtime
				,'deal_status'//1进行中，2满标，3流标，4还款中，5已还清
				,'pay_off_status'//投资人回款状态
				,'xssd_loan_bidcol'//xssd_loan_bidcol
				,'uloadtype'//投标类型 0按金额 1按分数
				,'portion'//分成多少份
				,'max_portion'//最多买多少份
				,'use_ecv'//是否可以使用红包
				,'is_autobid'//是否允许自动投标
				,'is_advance'//是否预告
				,'is_hidden'//是否隐藏，不显示在理财端
				,'is_recommend'//是否推荐
				,'fund_type'//资金源类型
				,'customers_id'//客服ID
				,'buy_count'//投标人数
				,'load_money'//已投标金额
				,'repay_start_time'//开始还款时间
				,'last_repay_time'//最后一次还款时间
				,'next_repay_time'//下次还款时间
				,'payoff_time'//还清时间
				,'repay_money'//总计已还款金额
				,'is_has_loans'//是否已放款给借款人
				,'loan_time'//放款时间
				,'bad_time'//流标时间
				,'is_send_bad_msg'//是否已发送流标通知
				,'bad_msg'//流标通知内容
				,'is_has_received'//流标是否已经返还
				,'is_send_success_msg'//是否已发送满标成功通知
				,'success_time'//满标成功通知时间
				,'is_send_half_msg'//是否已发送筹标过半信息
				,'send_half_msg_time'//发送筹标过半通知时间
				,'send_three_msg_time'//发送3天内需还款的通知时间
				,'risk_rank'//风险等级
				,'risk_security'//风险描述
				,'collec_times'//总计催收次数
				,'collec_last_time'//最后一次催收时间
				,'collec_allocation'//是否已做了催收分配
				);
	}

	public function getPrimaryKey() {
		return 'loan_id';
	}

	public function getTable() {
		return 'loan_bid';
	}
	//根据id条件获取字段数据
	public function getLoan($loanIds,$fields) {
		return $this->getDb()->select($fields) -> from($this -> getTable()) ->where(array('loan_id'=>$loanIds)) -> execute() -> key('loan_id') -> rows();
	}
	//根据流标返还状态获取贷款id
	public function getIds($where) {
		return $this->getDb()->from($this->getTable())->where($where)->execute()->values('loan_id');
	}
	//根据条件，更新数据
	public function updateData($where,$data){
		return $this->getDb()->where($where)->update($this->getTable(),$data)->execute();
	}
}
