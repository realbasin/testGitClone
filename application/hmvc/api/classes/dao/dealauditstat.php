<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_dealauditstat extends Dao
{

    public function __construct($groupName = 'stat', $isNewInstance = false)
    {
        parent::__construct($groupName, $isNewInstance);
    }

    public function getColumns()
    {
        return array(
            'id'//id
        , 'date_time'//所统计数据的日期
        , 'admin_id'//审核人员ID
        , 'totals'//总审核笔数
        , 'success_totals'//审核成功总笔数
        , 'first_totals'//首借审核总笔数
        , 'first_success_totals'//首借审核成功笔数
        , 'renew_totals'//续借审核总笔数
        , 'renew_success_totals'//续借审核成功笔数
        , 'true_totals'//复审总笔数
        , 'true_success_totals'//复审成功笔数
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_audit_stat';
    }

    // 查看审核业绩表最新的一条记录
    public function getTheLatestDealAuditStat()
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->orderBy('id', 'desc')
            ->execute()
            ->row();
        return $data;
    }

    /**
     * 查询业绩统计表的id（xssd_stat.deal_audit_stat表）
     * @param $admin_id
     * @param $date_time
     * @return mixed
     */
    public function getDealAuditStatId($admin_id, $date_time)
    {
        $where = array(
            'admin_id' => $admin_id,
            'date_time' => $date_time,
        );
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('id')
            ->where($where)
            ->execute()
            ->value('id');
        return $data;
    }

}
