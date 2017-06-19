<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 自动还款
 * Class task_AutoRepay
 */
class  task_AutoRepay extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('DealRepay');
        $business->autoDealRepay();
    }
}