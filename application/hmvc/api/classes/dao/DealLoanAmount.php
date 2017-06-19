<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealLoanAmount extends Dao
{

    public function __construct($groupName = '', $isNewInstance = false)
    {
        $groupName = 'stat';
        parent::__construct($groupName, $isNewInstance);
    }

    public function getColumns()
    {
        return array(
            'id'//id
        , 'date_time'//所统计数据的日期
        , 'apply_amount_total'//借款申请总额
        , 'apply_users'//申请人数
        , 'full_loan_amount'//满标放款金额
        , 'fail_loan_amount'//流标失败金额
        , 'pass_amount'//审核通过金额
        , 'pass_users'//审核通过人数
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_loan_amount';
    }

    //查询最新的记录
    public function getLastRow()
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->orderBy('id', 'desc')
            ->execute()
            ->row();
        return $data;
    }

}
