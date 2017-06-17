<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/**
 * 逾期贷款统计业务类
 */
class business_DealRepayLateAnalysis extends Business
{

    public function getDealRepayLateData()
    {
        $now_date = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime($now_date) - 86400);

        $analysisDailyDetailDao = \Core::dao('DealRepayLateAnalysisDailyDetail');
        $yesterdayDataExists = $analysisDailyDetailDao->dataExists($yesterday);

        // 已备份过逾期未还款的数据，再次进行统计，并保存结果进数据表
        if ($yesterdayDataExists) {
            $this->saveDealRepayLateAnalysisDetail();
        } else {
            // 备份逾期数据
            $dealRepayDao = \Core::dao('DealRepay');
            $list = $dealRepayDao->getYesterdayDealRepayLateData($now_date);

            foreach ($list as $item) {
                $analysisDailyDetailDao->insert($item);
            }

            // 统计逾期数据，保存进数据表
            $this->saveDealRepayLateAnalysisDetail();
        }
    }

    public function saveDealRepayLateAnalysisDetail()
    {
        $now_date = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime($now_date) - 86400);

        $analysisDetailDao = \Core::dao('DealRepayLateAnalysisDetail');
        $yesterdayDataExists = $analysisDetailDao->dataExists($yesterday);
        if ($yesterdayDataExists) {
            $this->saveDealRepayLateAnalysis();
        } else {
            // 备份逾期数据
            $analysisDailyDetailDao = \Core::dao('DealRepayLateAnalysisDailyDetail');
            $list = $analysisDailyDetailDao->getDealRepayLateAnalysisDailyDetailData($now_date);

            foreach ($list as $item) {
                $dealRepayDao = \Core::dao('DealRepay');
                $overMoney = $dealRepayDao->getDealRepayNoPaySumSelfMoney($item['deal_id']);
                $item['over_money'] = $overMoney;

                $analysisDetailDao->insert($item);

                $yesterday = date('Y-m-d', strtotime($now_date) - 86400);
                $analysisDailyDetailDao->update(['level' => $item['level']], ['date_time' => $yesterday, 'deal_id' => $item['deal_id']]);
            }

            // 统计逾期数据，保存进数据表
            $this->saveDealRepayLateAnalysis();
        }
    }

    public function saveDealRepayLateAnalysis()
    {
        $now_date = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime($now_date) - 86400);

        $analysisDao = \Core::dao('DealRepayLateAnalysis');
        $yesterdayDataExists = $analysisDao->dataExists($yesterday);

        if ($yesterdayDataExists) {
            return null;
        }

        $create_time = time();

        // 先保存合计的统计数据
        $analysisDetailDao = \Core::dao('DealRepayLateAnalysisDetail');
        $data = $analysisDetailDao->getDealRepayLateAnalysisDetailData($now_date, true);

        $data[0]['create_time'] = $create_time;
        $analysisDao->insert($data[0]);


        // 之后再保存各个等级对应的统计数据
        $dataList = $analysisDetailDao->getDealRepayLateAnalysisDetailData($now_date);

        foreach ($dataList as $item) {
            $item['create_time'] = $create_time;
            $item['level'] = 'M' . $item['level'];
            $analysisDao->insert($item);
        }
    }

}