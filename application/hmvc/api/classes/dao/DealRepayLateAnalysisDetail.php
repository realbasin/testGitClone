<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealRepayLateAnalysisDetail extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'date_time'//所统计数据的日期
        , 'level'//逾期等级
        , 'deal_id'//借款ID
        , 'repay_manage_money'//逾期本息、管理费
        , 'repay_money'//逾期本息
        , 'self_money'//逾期本金
        , 'manage_money'//逾期管理费
        , 'over_money'//剩余未还本金
        , 'over_times'//逾期期数
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_repay_late_analysis_detail';
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

    public function getDealRepayLateAnalysisDetailData($now_date, $all = false)
    {
        $yesterday = $now_date;

        if ($all) {
            $field = "'合计' level";
        } else {
            $field = "level";
        }

        $fields = "date_time," . $field . ",SUM(repay_manage_money) sum_repay_manage_money,SUM(repay_money) sum_repay_money,SUM(self_money) sum_self_money,SUM(over_money) sum_over_money,COUNT(deal_id) sum_count_deal,SUM(over_times) sum_over_times";
        $query = $this->getDb()
            ->from($this->getTable())
            ->select($fields)
            ->where(['date_time' => $yesterday]);
        if (!$all) {
            $query->groupBy('level');
        }
        $query->orderBy('level', 'desc');
        return $query->execute()->rows();
    }

}
