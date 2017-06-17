<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 审核业绩统计
 * Class task_DealAuditStat
 */
class  task_DealAuditStat extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('DealAuditStat');
        $business->statistics();
    }
}