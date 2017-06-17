<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 自动发送催款短信
 * Class task_dealmsglist
 */
class  task_dealmsglist extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('dealmsglist');
        $business->sendMessageToUser();
    }
}