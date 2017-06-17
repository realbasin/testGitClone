<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 用户关闭不合格城市合作伙伴登录
 * Class task_autoclosepartner
 */
class  task_autoclosepartner extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('regionpartner');
        $business->autoClosePartner();
    }
}