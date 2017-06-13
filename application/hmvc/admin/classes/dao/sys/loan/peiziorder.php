<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_loan_peiziorder extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'peizi_name'//peizi_name
				,'user_id'//用户ID
				,'invest_user_id'//出资人
				,'deal_id'//关联理财标ID
				,'order_sn'//订单编号
				,'peizi_conf_id'//配资配置表
				,'type'//配资类型;0:天;1周；2月
				,'rate_type'//type=0时，有效; 0:按借款金额收取利率；1按每天的实际交易金额,收取利率
				,'rate'//利率
				,'rate_money'//每日或每月利息费用【出资人收取】
				,'site_money'//服务费 = 借款金额×管理费率【按天或月收取】【平台收取的】
				,'site_rate'//服务费率【每日/月收取】[平台收取的]
				,'manage_money'//平台收取配资人的，成交服务费或是一次性业务审核费用(相当于p2p的满标服务费)
				,'score'//借款者获得积分
				,'cost_money'//本金
				,'borrow_money'//借款金额
				,'trade_money'//计息金额,用于计算rate_money,site_money的基数,按借款金额计算=fanwe_peiziorder.borrow_money；如果按实际使用资金计息方式则为=1、当天新增+	===》利息，佣金 2、当天卖出+	===》利息，佣金 3、当天持有+（不含：当天新增，卖出部分）===》利息
				,'trade_date'//计息金额统计日期
				,'stock_money'//当前帐户股票市值(帐户余额+股票市值）
				,'stock_date'//当前帐户股票市值 记录时间
				,'re_cost_money'//返还保证金, 有可能出现负数; 出现负数时,则说明：配资者的本金不够还亏损
				,'lever'//配资倍率
				,'warning_coefficient'//警戒系数
				,'warning_line'//亏损警戒线
				,'open_coefficient'//平仓系数
				,'open_line'//亏损平仓线
				,'time_limit_num'//资金使用期限
				,'begin_date'//开始交易时间(启息时间)
				,'end_date'//预计操作结束时间
				,'create_time'//订单创建时间
				,'status'//status:0:正在申请;1:支付成功;2:验证通过;3:验证失败;4:筹款成功;5:筹款失败;6:开户成功;7:开户失败;8:平仓结束;9:已撤消
				,'memo'//订单备注
				,'op_memo'//审核未通过的原因
				,'first_rate_money'//首次收取的利息费用(或预存款)
				,'contract_id'//借款合同
				,'stock_sn'//分配的，股票帐户
				,'stock_pwd_encrypt'//分配的，股票密码
				,'last_fee_date'//最后(近)一次扣费日期（日，月利率)
				,'next_fee_date'//下次扣费日期
				,'payoff_rate'//盈利比如：0.7则，实际盈利的70%归操盘者；30%归平台
				,'invest_payoff_rate'//出资者与平台的分成比
				,'user_payoff_fee'//用户获得盈利
				,'invest_payoff_fee'//出资人分成=股票盈利*(1-payoff_rate)*invest_payoff_rate
				,'site_payoff_fee'//平台获得的盈利金额
				,'other_fee'//平仓时的，收取的其它费用
				,'other_memo'//收费的其它费用备注
				,'is_holiday_fee'//0;type=0时有效;1周末节假日免费
				,'admin_id'//分配的专职管理员
				,'is_today'//交易开始时间(0:下一交易日;1:今天)
				,'is_arrearage'//1:自动继费失败
				,'invest_commission_rate'//投资者获得交易佣金差比率;如果填0,则所有的佣金差，都有平台获得;填:0.4则；40%投资者;60%平台
				,'total_site_commission_money'//平台累计，收到：佣金
				,'total_invest_commission_money'//投资者累计，收到：佣金
				,'total_site_money'//已收服务费总和
				,'total_rate_money'//已收利息总和
				,'update_time'//更新时间
				,'bad_msg'//流标原因
				,'p_invest_user_id'//投资者推荐人
				,'invite_invest_money_rate'//投资推荐人p_user_id获得的: 投资人投资金的n%作为返利
				,'invite_invest_interest_rate'//投资推荐人p_invest_user_id获得的:投资人利息收益的n%作为返利
				,'invite_invest_commission_rate'//投资推荐人p_invest_user_id获得的:投资人佣金收益的 n%作为返利
				,'invite_invest_money'//投资返利【投资推荐人p_invest_user_id获得的: 投资金额返利 = borrow_money * invite_invest_money_rate】
				,'p_user_id'//配资者推荐人
				,'invite_borrow_money_rate'//借款推荐人p_user_id获得的: 借款人借款的n%作为返利
				,'invite_borrow_interest_rate'//借款推荐人p_user_id获得的: 平台服务费收益的n%作为返利
				,'invite_borrow_commission_rate'//借款推荐人p_user_id获得的: 平台佣金收益的 n%作为返利
				,'invite_borrow_money'//借款返利【借款推荐人p_user_id获得的: 借款金额返利 = borrow_money * invite_borrow_money_rate】
				,'sort'//前台显示排序
				,'invest_begin_time'//投资开始时间
				,'invest_end_time'//投资结束时间
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'peizi_order';
	}

}
