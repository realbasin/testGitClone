<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_dealoplog extends Dao
{

    private $op_id_condition = "op_id=1";
    private $admin_condition = "adm.is_delete=0 AND adm.role_id IN (8,20,28,46)";
    private $pass_condition = "log LIKE '%初审通过；%'";
    private $fail_condition = "log LIKE '%初审失败；%'";

    public function getColumns()
    {
        return array(
            'id'//id
        , 'deal_id'//借款ID
        , 'user_id'//借款用户
        , 'op_id'//操作ID
        , 'op_name'//审核操作阶段
        , 'op_result'//操作结果
        , 'log'//日志内容
        , 'admin_id'//操作人员
        , 'create_time'//操作时间
        , 'ip'//操作ip
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_op_log';
    }

    // 查询条件（开始时间）
    private function start_time_condition($date_time)
    {
        return "create_time>=" . strtotime($date_time);
    }

    // 查询条件（结束时间）
    private function end_time_condition($date_time)
    {
        return "create_time<" . (strtotime($date_time) + 86400);
    }

    // 统计审核总笔数/审核成功总笔数
    public function getDealOpLogTotals($date_time)
    {
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(dol.deal_id) totals,dol.admin_id,
            (SELECT COUNT(deal_id) FROM _tablePrefix_deal_op_log WHERE " . $this->op_id_condition . " AND admin_id=dol.admin_id AND " . $this->pass_condition . " AND " . $start_time_condition . " AND " . $end_time_condition . " ) success_totals
			FROM _tablePrefix_deal_op_log dol INNER JOIN _tablePrefix_admin adm ON dol.admin_id=adm.id AND " . $this->admin_condition . " 
			WHERE dol." . $this->op_id_condition . " AND (dol." . $this->fail_condition . " OR dol." . $this->pass_condition . ") 
			AND dol." . $start_time_condition . " AND dol." . $end_time_condition . " 
			GROUP BY dol.admin_id ";

        return $this->getDb()->execute($sql);
    }

    // 统计首借审核总笔数
    public function getDealOpLogFirstTotals($date_time)
    {
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) first_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $this->op_id_condition . " AND (lg." . $this->fail_condition . " OR lg." . $this->pass_condition . ") AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " AND d.b_status = 0 GROUP BY lg.admin_id";

        return $this->getDb()->execute($sql);
    }

    // 统计首借审核成功笔数
    public function getDealOpLogFirstSuccessTotals($date_time)
    {
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) first_success_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $this->op_id_condition . " AND lg." . $this->pass_condition . " AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " AND d.b_status = 0 GROUP BY lg.admin_id";

        return $this->getDb()->execute($sql);
    }

    // 统计续借审核总笔数
    public function getDealOpLogRenewTotals($date_time)
    {
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) renew_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $this->op_id_condition . " AND (lg." . $this->fail_condition . " OR lg." . $this->pass_condition . ") AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " AND d.b_status = 1 GROUP BY lg.admin_id";

        return $this->getDb()->execute($sql);
    }

    // 统计续借审核成功笔数
    public function getDealOpLogRenewSuccessTotals($date_time)
    {
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) renew_success_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $this->op_id_condition . " AND lg." . $this->pass_condition . " AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " AND d.b_status = 1 GROUP BY lg.admin_id";

        return $this->getDb()->execute($sql);
    }

    //统计复审总笔数
    public function getDealOpLogTrueTotals($date_time)
    {
        $op_id_condition = "op_id=7";
        $pass_condition = "log LIKE '%复审通过；%'";
        $fail_condition = "log LIKE '%复审失败；%'";
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) true_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $op_id_condition . " AND (lg." . $pass_condition . " OR lg." . $fail_condition . ") AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " GROUP BY lg.admin_id";
        return $this->getDb()->execute($sql);
    }

    //统计复审成功笔数
    public function getDealOpLogTrueSuccessTotals($date_time)
    {
        $op_id_condition = "op_id=7";
        $pass_condition = "log LIKE '%复审通过；%'";
        $start_time_condition = $this->start_time_condition($date_time);
        $end_time_condition = $this->end_time_condition($date_time);

        $sql = "SELECT COUNT(lg.deal_id) true_success_totals,lg.admin_id 
			FROM _tablePrefix_deal_op_log lg INNER JOIN _tablePrefix_loan_base d ON lg.deal_id=d.id INNER JOIN _tablePrefix_admin adm ON lg.admin_id=adm.id AND " . $this->admin_condition . "
			WHERE lg." . $op_id_condition . " AND lg." . $pass_condition . " AND lg." . $start_time_condition . " AND lg." . $end_time_condition . " GROUP BY lg.admin_id";

        return $this->getDb()->execute($sql);
    }

}
