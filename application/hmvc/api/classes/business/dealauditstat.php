<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 贷款审核
 */
class business_dealaudit extends Business
{

    /**
     * 审核统计
     * @return null
     */
    public function statistics()
    {
        // 执行日期
        $date_time = date('Y-m-d', strtotime('-1day' ,time()));

        $start_date = "2016-01-07";

        $dealAuditStatDao = \Core::dao('dealauditstat');
        $theLatest = $dealAuditStatDao->getTheLatestDealAuditStat();
        if ($theLatest != null && $theLatest['id'] > 0 && strtotime($theLatest['date_time']) > strtotime($date_time)) {
            return null;
        } elseif ($theLatest != null && $theLatest['id'] > 0 && strtotime($theLatest['date_time']) == strtotime($date_time)) {
            $this->saveDealAuditStatData($date_time);
        } elseif ($theLatest != null && $theLatest['id'] > 0) {
            $new_date = $theLatest['date_time'];
            do {

                $new_date = date("Y-m-d", strtotime($new_date) + 86400);

                $this->saveDealAuditStatData($new_date);

            } while (strtotime(date("Y-m-d", strtotime($new_date) + 86400)) <= strtotime($date_time));
        } else {

            $this->saveDealAuditStatData($start_date);

            $this->statistics();
        }
    }

    /**
     * 把统计好的数据写入数据库中保存
     * @param $date_time
     * @return bool
     */
    private function saveDealAuditStatData($date_time)
    {
        $op_log_list = $this->getArrangeDealOpLogStat($date_time);

        $dealAuditStatDao = \Core::dao('dealauditstat');
        $result = array();
        foreach ($op_log_list as $k => $item) {
            $item['date_time'] = $date_time;

            $id = $dealAuditStatDao->getDealAuditStatId($item['admin_id'], $date_time);
            if($id){
                $res = $dealAuditStatDao->update($item,['id' => $id]);
            }else{
                $res = $dealAuditStatDao->insert($item);
            }

            $result[] = $res;
        }

        return !empty($op_log_list) && count($op_log_list) == count($result) ? true : false;
    }

    // 整理统计审核的数据
    public static function getArrangeDealOpLogStat($date_time)
    {
        // 审核总笔数/审核成功总笔数
        $dealOpLogDao = Core::dao('dealoplog');
        $total_totals = $dealOpLogDao->getDealOpLogTotals($date_time);

        // 首借审核总笔数
        $first_totals = $dealOpLogDao->getDealOpLogFirstTotals($date_time);

        // 首借审核成功笔数
        $first_success_totals = $dealOpLogDao->getDealOpLogFirstSuccessTotals($date_time);

        // 续借审核总笔数
        $renew_totals = $dealOpLogDao->getDealOpLogRenewTotals($date_time);

        // 续借审核成功笔数
        $renew_success_totals = $dealOpLogDao->getDealOpLogRenewSuccessTotals($date_time);

        //by xssd xbw 20160923 审核业绩统计与借款人数的数据有出入 WWW-321

        //复审总笔数
        $true_totals = $dealOpLogDao->getDealOpLogTrueTotals($date_time);

        //复审成功笔数
        $true_success_totals = $dealOpLogDao->getDealOpLogTrueSuccessTotals($date_time);
        //by xssd xbw 20160923 审核业绩统计与借款人数的数据有出入 WWW-321 End

        $op_log_list = array();

        if ($total_totals != null) {
            for ($i=0; $i<count($total_totals); $i++) {
                $admin_id = $total_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['admin_id'] = $total_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['totals'] = $total_totals[$i]['totals'];
                $op_log_list[$admin_id]['success_totals'] = $total_totals[$i]['success_totals'];
                $op_log_list[$admin_id]['first_totals'] = 0;
                $op_log_list[$admin_id]['first_success_totals'] = 0;
                $op_log_list[$admin_id]['renew_totals'] = 0;
                $op_log_list[$admin_id]['renew_success_totals'] = 0;
                $op_log_list[$admin_id]['true_totals'] = 0;
                $op_log_list[$admin_id]['true_success_totals'] = 0;
            }
        }

        if ($first_totals != null) {
            for ($i=0; $i<count($first_totals); $i++) {
                $admin_id = $first_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['first_totals'] = $first_totals[$i]['first_totals'];
            }
        }

        if ($first_success_totals != null) {
            for ($i=0; $i<count($first_success_totals); $i++) {
                $admin_id = $first_success_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['first_success_totals'] = $first_success_totals[$i]['first_success_totals'];
            }
        }

        if ($renew_totals != null) {
            for ($i=0; $i<count($renew_totals); $i++) {
                $admin_id = $renew_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['renew_totals'] = $renew_totals[$i]['renew_totals'];
            }
        }

        if ($renew_success_totals != null) {
            for ($i=0; $i<count($renew_success_totals); $i++) {
                $admin_id = $renew_success_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['renew_success_totals'] = $renew_success_totals[$i]['renew_success_totals'];
            }
        }

        //by xssd xbw 20160923 审核业绩统计与借款人数的数据有出入 WWW-321

        if ($true_totals != null) {
            for ($i=0; $i<count($true_totals); $i++) {
                $admin_id = $true_totals[$i]['admin_id'];
                if (empty($op_log_list[$admin_id]['admin_id'])){
                    $op_log_list[$admin_id]['admin_id'] = $admin_id;
                }
                $op_log_list[$admin_id]['true_totals'] = $true_totals[$i]['true_totals'];
            }
        }

        if ($true_success_totals != null) {
            for ($i=0; $i<count($true_success_totals); $i++) {
                $admin_id = $true_success_totals[$i]['admin_id'];
                $op_log_list[$admin_id]['true_success_totals'] = $true_success_totals[$i]['true_success_totals'];
            }
        }
        //by xssd xbw 20160923 审核业绩统计与借款人数的数据有出入 WWW-321 End


        return $op_log_list;
    }

}