<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 审核业绩统计
 * Class task_dealauditstat
 */
class  task_dealauditstat extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('dealauditstat');
        $business->statistics();
    }
}