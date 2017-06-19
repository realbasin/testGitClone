<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 发送催款短信task
 * Class task_DealMsgList
 */
class  task_DealMsgList extends Task
{

    function execute(CliArgs $args)
    {
        $business = \Core::business('DealMsgList');
        $business->sendMessageToUser();
    }
}