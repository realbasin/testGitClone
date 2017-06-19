<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 芝麻信用业务类
 */
class business_ZhiMaCredit extends Business
{

    /**
     * 【定时任务】检查用户是否取消芝麻信用授权,若已取消授权，则将zm_openid更新为空，其他数据不变
     */
    public function checkUserZhiMaCreditSubscribe()
    {
        $userDao = \Core::dao('User');
        $user_list = $userDao->getUserZmOpenIdList();

        if ($user_list != null && !empty($user_list)) {
            foreach ($user_list as $key => $zm_user) {
                $open_id = $zm_user['zm_openid'];
                $credit = \Core::library("ZhimaCredit/Loader");
                $creditScore = $credit->scoreGet($open_id);

                //可查询到用户的芝麻信用评分
                if (!empty($creditScore['success']) || $creditScore['success'] == 'true') {
                    continue;
                } else {
                    //查询到用户鉴权失败-ZMCREDIT.authentication_fail，即用户已取消授权，则将zm_openid更新为空
                    $userDao->update(['zm_openid' => ''], ['id' => $zm_user['id']]);
                }
            }

            return '已检查完毕';
        }

        return '无已授权芝麻信用的用户';
    }

    /**
     * 业务流水凭证
     * @return string
     */
    public function setTransactionId()
    {
        list($usec, $sec) = explode(" ", microtime());

        $msec = round($usec * 1000);

        $millisecond = str_pad($msec, 3, '0', STR_PAD_RIGHT);

        $transaction_id = date("YmdHis") . $millisecond;

        return $transaction_id;
    }

    /**
     * 反馈数据给芝麻信用【凌晨1：15执行定时任务】
     */
    public function dealDataFeedback()
    {
        $date = date('Y-m-d', strtotime('-1day', time()));
        $start_time = strtotime($date);
        $end_time = $start_time + 3600 * 24;
        $records = 0;
        $borrow_records = array();

        $pageSize = 10;  // 默认显示10条数据

        $loanBaseDao = \Core::dao('LoanBase');
        $userDao = \Core::dao('User');
        $borrow_list_total = $loanBaseDao->getBorrowDealCount($start_time, $end_time);
        if ($borrow_list_total > 0) {
            $records += $borrow_list_total;
            $allPage = ceil($borrow_list_total / $pageSize);

            for ($page = 1; $page <= $allPage; $page++) {
                $borrow_list = $loanBaseDao->getBorrowDealData($start_time, $end_time, ($page - 1) * $pageSize, $pageSize);
                foreach ($borrow_list as $key => $borrow) {
                    $borrow_data = array();
                    $borrow_data['user_credentials_type'] = "0";
                    $user_info = $userDao->getUserEncryptInfoById($borrow->user_id);
                    if (empty($user_info) || $user_info['real_name'] == null || $user_info['idno'] == null) {
                        continue;
                    }
                    $borrow_data['user_name'] = $user_info['real_name'];
                    $borrow_data['user_credentials_no'] = $user_info['idno'];
                    $borrow_data['order_no'] = $borrow['id'];//借款ID
                    $borrow_data['pay_month'] = '';//还款月份
                    $borrow_data['biz_type'] = '1';
                    //审核失败、流标
                    if ($borrow['is_delete'] == 3 || $borrow['deal_status'] == 3) {
                        //审批否决
                        $borrow_data['order_status'] = '02';
                        $borrow_data['gmt_ovd_date'] = date('Y-m-d H:i:s', $borrow['create_time']);
                    } else {
                        //审批通过
                        $borrow_data['order_status'] = '01';
                        if ($borrow->success_time > 0) {
                            //满标时间（待放款）
                            $borrow_data['gmt_ovd_date'] = date('Y-m-d H:i:s', $borrow['second_audit_time']);
                        } elseif ($borrow->second_audit_time > 0) {
                            //复审通过时间
                            $borrow_data['gmt_ovd_date'] = date('Y-m-d H:i:s', $borrow['second_audit_time']);
                        } else {
                            //初审通过时间
                            $borrow_data['gmt_ovd_date'] = date('Y-m-d H:i:s', $borrow['first_audit_time']);
                        }
                    }
                    //借款金额
                    $borrow_data['create_amt'] = $borrow['borrow_amount']; //$borrow->apply_borrow_amount>0 ? $borrow->apply_borrow_amount : $borrow->borrow_amount;
                    $borrow_data['overdue_days'] = '';
                    $borrow_data['overdue_amt'] = '';
                    $borrow_data['gmt_pay'] = '';
                    $borrow_data['memo'] = '';
                    $borrow_records[] = $borrow_data;
                }
            }
        }

        //获取昨天的借款用户还款情况数据总数
        $dealRepayDao = \Core::dao('DealRepay');
        $deal_repay_total = $dealRepayDao->getDealRepayCount($date);

        if ($deal_repay_total > 0) {
            $records += $deal_repay_total;
            $allPage = ceil($deal_repay_total / $pageSize);

            for ($page = 1; $page <= $allPage; $page++) {

                $deal_repay_list = $dealRepayDao->getDealRepayData($date, ($page - 1) * $pageSize, $pageSize);
                foreach ($deal_repay_list as $key => $deal_repay) {
                    $deal_repay_data = array();
                    $deal_repay_data['user_credentials_type'] = "0";
                    $user_info = $userDao->getUserEncryptInfoById($deal_repay['user_id']);
                    if (empty($user_info) || $user_info['real_name'] == null || $user_info['idno'] == null) {
                        continue;
                    }
                    $deal_repay_data['user_credentials_no'] = $user_info['idno'];
                    $deal_repay_data['user_name'] = $user_info['real_name'];
                    $deal_repay_data['order_no'] = $deal_repay['deal_id'];//借款ID
                    $deal_repay_data['biz_type'] = '1';
                    $deal_repay_data['pay_month'] = $deal_repay['l_key'];//还款月份（还款期数）

                    $deal_repay_data['gmt_ovd_date'] = date('Y-m-d H:i:s', $deal_repay['repay_time']);
                    $deal_repay_data['order_status'] = '04';

                    //借款金额
                    $deal_info = $loanBaseDao->getRowById($deal_repay['deal_id']);
                    $deal_repay_data['create_amt'] = $deal_info['borrow_amount'];

                    $noRepayMoney = $dealRepayDao->getNoRepayTotalMoney($deal_repay['user_id'], $deal_repay['deal_id'], $deal_repay['l_key']);
                    ////逾期天数
                    if ($noRepayMoney['mix_repay_date'] != null) {
                        $deal_repay_data['overdue_days'] = (string)ceil((strtotime($date) - strtotime($noRepayMoney->mix_repay_date)) / 86400);
                    } else {
                        $deal_repay_data['overdue_days'] = '';
                    }
                    //逾期待还本息
                    $deal_repay_data['overdue_amt'] = (string)floatval($noRepayMoney['total_repay_money']);
                    //还清
                    if ($deal_info['deal_status'] == 5) {
                        $deal_repay_data['gmt_pay'] = date('Y-m-d H:i:s', $deal_repay['true_repay_time']);
                    } else {
                        $deal_repay_data['gmt_pay'] = '';
                    }
                    $deal_repay_data['memo'] = '';
                    $borrow_records[] = $deal_repay_data;
                }
            }
        }

        $dir = STORAGE_PATH . 'app/zmxy/';
        if (!is_dir(STORAGE_PATH . 'app/zmxy/')) {
            mk_dir($dir);
        }
        $file_records = $dir . $date . '_feedback.json';
        file_put_contents($file_records, json_encode(array('records' => $borrow_records), JSON_UNESCAPED_UNICODE));

        return $this->zhiMaDataBatchFeedback($records, $file_records);
    }

    /**
     * 数据反馈
     * @param $records
     * @param $file_records
     * @return mixed
     */
    public function zhiMaDataBatchFeedback($records, $file_records)
    {
        $credit = \Core::library("ZhimaCredit/Loader");

        $response = $credit->ZhimaDataBatchFeedback($records, $file_records);

        return json_decode($response, true);
    }

}