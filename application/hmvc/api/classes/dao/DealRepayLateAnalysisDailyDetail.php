<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealRepayLateAnalysisDailyDetail extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'date_time'//所统计数据的日期
        , 'deal_repay_id'//还款详情ID
        , 'level'//逾期等级
        , 'deal_id'//借款ID
        , 'user_id'//借款人
        , 'repay_money'//还款金额
        , 'manage_money'//管理费
        , 'impose_money'//罚息
        , 'repay_time'//还的是第几期的
        , 'true_repay_time'//还款时间
        , 'status'//0提前,1准时还款，2逾期还款 3严重逾期  前台在这基础上+1
        , 'l_key'//还款顺序 0 开始
        , 'has_repay'//0未还,1已还 2部分还款
        , 'manage_impose_money'//逾期管理费
        , 'is_site_bad'//是否坏账  0不是，1坏账 管理员看到的
        , 'repay_date'//预期还款日期,日期格式方便统计
        , 'true_repay_date'//实际还款日期,日期格式方便统计
        , 'true_repay_money'//实还金额
        , 'true_self_money'//实际还款本金
        , 'interest_money'//待还利息   repay_money - self_money
        , 'true_interest_money'//实际还利息
        , 'true_manage_money'//实际管理费
        , 'self_money'//需还本金
        , 'loantype'//还款方式
        , 'manage_money_rebate'//预计收到的：管理费返佣,满标放款时生成
        , 'true_manage_money_rebate'//实际收到的：管理费返佣,每期还款时生成
        , 'get_manage'//是否已收取管理费
        , 'mortgage_fee'//抵押物管理费
        , 'true_mortgage_fee'//抵押物管理费
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_repay_late_analysis_daily_detail';
    }

    /**
     * 判断数据是否存在
     * @param $date
     * @return bool
     */
    public function dataExists($date)
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where(['date_time' => $date])
            ->execute()
            ->row();
        return $data ? true : false;
    }

    public function getDealRepayLateAnalysisDailyDetailData($now_date)
    {
        $yesterday = $now_date;
        $fields = "date_time, COUNT(deal_id) over_times,
          CEIL((" . $yesterday . "-(repay_time+3600*24))/(3600*24)/29) level,
          SUM(repay_money)+SUM(manage_money) repay_manage_money,
          SUM(repay_money) repay_money,
          SUM(manage_money) manage_money,
          SUM(self_money) self_money,
          deal_id";
        $data = $this->getDb()
            ->from($this->getTable())
            ->select($fields)
            ->where(['date_time' => $yesterday, 'has_repay' => 0])
            ->groupBy('deal_id')
            ->orderBy('level')
            ->execute()
            ->rows();
        return $data;
    }

}
