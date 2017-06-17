<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealRepay extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
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
        return 'deal_repay';
    }

    public function usersTodayRepayAmount($ids, $start_date, $end_date)
    {
        if (!is_array($ids) || count($ids) == 0)
            return 0;

        $where['user_id'] = $ids;
        if ($start_date != '') {
            $where['true_repay_date >='] = $start_date;
        }
        if ($end_date != '') {
            $where['true_repay_date <'] = $end_date;
        }
        $where['has_repay'] = 1;


        $data = $this->getDb()
            ->from($this->getTable())
            ->select('sum(true_self_money) sum_amount')
            ->where($where)
            ->execute()
            ->value('sum_amount');
        return $data;
    }

    public function getDealRepayNoPaySumSelfMoney($deal_id)
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('sum(self_money) over_money')
            ->where(['deal_id' => $deal_id, 'has_repay' => 0])
            ->execute()
            ->value('over_money');
        return $data;
    }

    public function getYesterdayDealRepayLateData($now_date)
    {

        $sql = "SELECT '" . date('Y-m-d', $now_date) . "' as date_time,a.id deal_repay_id,a.*
        FROM _tablePrefix_deal_repay a INNER JOIN _tablePrefix_deal b ON a.deal_id = b.id
        WHERE (a.has_repay=0 AND (a.repay_time+3600*24-1)<" . $now_date . ") OR (a.has_repay=1 AND a.true_repay_date='" . date('Y-m-d', $now_date) . "' AND (a.repay_time+3600*24)<" . $now_date . ")
        GROUP BY a.deal_id,a.l_key";

        return Core::db()->execute($sql);
    }

    public function getNoRepayDealRepaysByDate($start_date, $end_date)
    {
        if (!strtotime($start_date) || !strtotime($end_date)) {
            return false;
        }

        $where = ['has_repay' => 0];
        if ($start_date == $end_date) // 指定日期
        {
            $where['repay_date'] = $start_date;
        } else {
            $where['repay_date>='] = $start_date;
            $where['repay_date<'] = $end_date;
        }

        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where($where)
            ->execute()
            ->rows();
        return $data;
    }

    public function getDealRepayList($deal_id)
    {
        $data = $this->getDb()
            ->select('distinct(dr.l_key),dr.*,dr.l_key+1 as l_key_index,dlr.is_site_repay')
            ->from($this->getTable(), 'dr')
            ->join(['deal_load_repay' => 'dlr'], 'dlr.deal_id=dr.deal_id AND dlr.l_key=dr.l_key', 'left')
            ->where(['dr.deal_id' => $deal_id])
            ->orderBy('dr.l_key', 'asc')
            ->execute()
            ->rows();
        return $data;
    }

    public function getShouldRepayDealUserList()
    {
        $today = date('Y-m-d');
        $third_day = date('Y-m-d', strtotime('+3day', time()));
        $seven_day = date('Y-m-d', strtotime('+7day', time()));

        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*,l_key + 1 AS l_key_index')
            ->where(['repay_date' => [$today, $third_day, $seven_day], 'has_repay' => 0])
            ->orderBy('repay_time', 'asc')
            ->execute()
            ->rows();
        return $data;
    }

    public function getDealRepayById($id)
    {
        $where = array('id' => $id);
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where($where)
            ->execute()
            ->row();
        return $data;
    }

    public function getAutoSiteList()
    {
        $yesterday = date('Y-m-d', strtotime('-1day', time()));
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where(['has_repay' => 0, 'repay_date' => $yesterday])
            ->limit(0, 3)
            ->execute()
            ->rows();
        return $data;
    }

    /**
     * 获取借款用户还款情况数据总数
     * @param $date
     * @return mixed
     */
    public function getDealRepayCount($date)
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('COUNT(*) AS num')
            ->where(['repay_date' => $date])
            ->execute()
            ->value('num');
        return $data;
    }

    /**
     * 获取借款用户还款情况
     * @param $date string 时间
     * @param $startLimit int
     * @param $endLimit int
     * @return array
     */
    public function getDealRepayData($date, $startLimit, $endLimit)
    {
        return $this->getDb()
            ->select('*')
            ->from($this->getTable())
            ->where(['repay_date' => $date])
            ->limit($startLimit, $endLimit)
            ->execute()
            ->rows();
    }

    /**
     * 获取用户截止本期未还本息总和
     * @param $user_id
     * @param $deal_id
     * @param $l_key
     * @return array
     */
    public function getNoRepayTotalMoney($user_id, $deal_id, $l_key)
    {
        $where = ['user_id' => $user_id, 'deal_id' => $deal_id, 'has_repay' => 0, 'l_key<=' => $l_key];
        return $this->getDb()
            ->select('SUM(repay_money) as total_repay_money,MIN(repay_date) as mix_repay_date')
            ->from($this->getTable())
            ->where($where)
            ->execute()
            ->row();
    }

}
