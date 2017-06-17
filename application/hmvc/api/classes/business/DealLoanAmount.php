<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 统计借款情况业务类
 */
class business_DealLoanAmount extends Business
{

    //定时统计借款情况
    public function autoCountAmount()
    {
        //执行日期
        $date_time = date('Y-m-d', strtotime('-1day', time()));

        //开始统计时间
        $start_date = "2017-02-01";

        $theLatest = \Core::dao('DealLoanAmount')->getLastRow();

        if ($theLatest != null && $theLatest['id'] > 0) {
            //已保存最新数据与自动统计时的时间一致时，更新该数据
            if (strtotime($theLatest['date_time']) == strtotime($date_time)) {
                return $this->saveBorrowAmountTotalDataByDate($date_time, $theLatest['id']);
            } elseif (strtotime($theLatest['date_time']) > strtotime($date_time)) {
                return 'no data';
            }

            //处理从初始时间到前一天的数据统计
            $new_date = $theLatest['date_time'];
            do {
                $new_date = date("Y-m-d", strtotime($new_date) + 86400);

                $this->saveBorrowAmountTotalDataByDate($new_date);

            } while (strtotime(date("Y-m-d", strtotime($new_date) + 86400)) <= strtotime($date_time));
        } else {

            $this->saveBorrowAmountTotalDataByDate($start_date);

            $this->autoCountAmount();
        }

        return 'done';
    }

    /**
     * 将查出来的统计数据保存到数据表
     * @param $date
     * @param int $latestId
     * @return string
     */
    public function saveBorrowAmountTotalDataByDate($date, $latestId = 0)
    {
        $start_time = strtotime($date);
        $end_time = $start_time + 3600 * 24;

        $data = [
            'date_time' => $date,
        ];

        $loanBaseDao = \Core::dao('LoanBase');

        //借款申请总额
        $applyData = $loanBaseDao->getApplyAmountAndApplyUsersByDate($start_time, $end_time);
        $data['apply_amount_total'] = $applyData['applyAmount'];

        //申请人数
        $data['apply_users'] = $applyData['applyUsers'];

        //满标放款金额
        $fullLoanAmount = $loanBaseDao->getFullLoanAmountByDate($start_time, $end_time);
        $data['full_loan_amount'] = $fullLoanAmount;

        //流标失败金额
        $failLoanAmount = $loanBaseDao->getFailLoanAmountByDate($start_time, $end_time);
        $data['fail_loan_amount'] = $failLoanAmount;

        //审核通过金额
        $applyPassedData = $loanBaseDao->getPassedLoanAmountAndPassedApplyUsersByDate($start_time, $end_time);
        $data['pass_amount'] = $applyPassedData['pass_amount'];

        //审核通过人数
        $data['pass_users'] = $applyPassedData['pass_users'];

        $dealLoanAmountDao = \Core::dao('DealLoanAmount');
        if ($latestId) {
            $res = $dealLoanAmountDao->update($data, ['id' => $latestId]);
        } else {
            $res = $dealLoanAmountDao->insert($data);
        }

        if ($res) {
            return 'success';
        } else {
            return 'fail';
        }
    }

}