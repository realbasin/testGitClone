<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 优惠卷过期短信提醒
 * Class task_BonusMsgRemind
 */
class  task_BonusMsgRemind extends Task {

    function execute(CliArgs $args){
        $business = \Core::business('BonusMsgRemind');
        $business->sendMessageToUser();
    }
}