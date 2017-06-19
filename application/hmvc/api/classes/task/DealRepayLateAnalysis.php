<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 逾期贷款统计
 * Class task_DealRepayLateAnalysis
 */
class  task_DealRepayLateAnalysis extends Task {

    function execute(CliArgs $args)
    {
        $date = $args->get('date');
        if (!strtotime($date))
            $date = '';

        $business = \Core::business('DealRepayLateAnalysis');
        $business->getDealRepayLateData($date);
    }
}