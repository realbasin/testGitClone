<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 逾期贷款统计
 * Class task_dealrepaylateanalysis
 */
class  task_dealrepaylateanalysis extends Task {

    function execute(CliArgs $args)
    {
        $date = $args->get('date');
        if (!strtotime($date))
            $date = '';

        $business = \Core::business('dealrepaylateanalysis');
        $business->getDealRepayLateData($date);
    }
}