<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 自动还款
 * Class task_autorepay
 */
class  task_autorepay extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('dealrepay');
        $business->autoDealRepay();
    }
}