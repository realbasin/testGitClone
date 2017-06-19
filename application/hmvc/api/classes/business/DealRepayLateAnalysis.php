<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 逾期贷款统计业务类
 */
class business_DealRepayLateAnalysis extends Business
{

    public function getDealRepayLateData()
    {
        $yesterday = date('Y-m-d', time() - 86400);

        $analysisDailyDetailDao = \Core::dao('DealRepayLateAnalysisDailyDetail');
        $yesterdayDataExists = $analysisDailyDetailDao->dataExists($yesterday);

        // 已备份过逾期未还款的数据，再次进行统计，并保存结果进数据表
        if ($yesterdayDataExists) {
            $this->saveDealRepayLateAnalysisDetail();
        } else {
            // 备份逾期数据
            $dealRepayDao = \Core::dao('DealRepay');
            $list = $dealRepayDao->getDealRepayLateData($yesterday);
            foreach ($list as $item) {
                $analysisDailyDetailDao->insert($item);
            }

            // 统计逾期数据，保存进数据表
            $this->saveDealRepayLateAnalysisDetail();
        }
    }

    public function saveDealRepayLateAnalysisDetail()
    {
        $yesterday = date('Y-m-d', time() - 86400);

        $analysisDetailDao = \Core::dao('DealRepayLateAnalysisDetail');
        $yesterdayDataExists = $analysisDetailDao->dataExists($yesterday);
        if ($yesterdayDataExists) {
            $this->saveDealRepayLateAnalysis();
        } else {
            // 备份逾期数据
            $analysisDailyDetailDao = \Core::dao('DealRepayLateAnalysisDailyDetail');
            $list = $analysisDailyDetailDao->getDealRepayLateAnalysisDailyDetailData($yesterday);
            foreach ($list as $item) {
                $dealRepayDao = \Core::dao('DealRepay');
                $overMoney = $dealRepayDao->getDealRepayNoPaySumSelfMoney($item['deal_id']);
                $item['over_money'] = $overMoney;

                $analysisDetailDao->insert($item);
                $analysisDailyDetailDao->update(['level' => $item['level']], ['date_time' => $yesterday, 'deal_id' => $item['deal_id']]);
            }

            // 统计逾期数据，保存进数据表
            $this->saveDealRepayLateAnalysis();
        }
    }

    public function saveDealRepayLateAnalysis()
    {
        $yesterday = date('Y-m-d', time() - 86400);

        $analysisDao = \Core::dao('DealRepayLateAnalysis');
        $yesterdayDataExists = $analysisDao->dataExists($yesterday);

        if ($yesterdayDataExists) {
            return null;
        }

        $create_time = time();

        // 先保存合计的统计数据
        $analysisDetailDao = \Core::dao('DealRepayLateAnalysisDetail');
        $data = $analysisDetailDao->getDealRepayLateAnalysisDetailData($yesterday, true);

        $analysis = $data[0];
        foreach ($analysis as &$value) {
            if (empty($value) && !is_numeric($value)) {
                $value = 0;
            }
        }
        $analysis['create_time'] = $create_time;
        $analysis['date_time'] = $yesterday;
        $analysisDao->insert($analysis);


        // 之后再保存各个等级对应的统计数据
        $dataList = $analysisDetailDao->getDealRepayLateAnalysisDetailData($yesterday);
        foreach ($dataList as $item) {
            $item['create_time'] = $create_time;
            $item['level'] = 'M' . $item['level'];
            foreach ($item as &$value) {
                if (empty($value) && !is_numeric($value)) {
                    $value = 0;
                }
            }
            $analysisDao->insert($item);
        }
    }

}