<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealRepayLateAnalysis extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'date_time'//日期
        , 'level'//逾期等级
        , 'sum_repay_manage_money'//逾期本息、管理费
        , 'sum_repay_money'//逾期本息
        , 'sum_self_money'//逾期本金
        , 'sum_count_deal'//逾期笔数
        , 'sum_over_times'//逾期期数
        , 'sum_over_money'//剩余未还本金
        , 'create_time'//统计时间
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_repay_late_analysis';
    }

    /**
     * 根据日期判断数据是否存在
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

}
