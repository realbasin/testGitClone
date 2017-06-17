<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 网站垫付(自动执行)
 * Class task_dositerepay
 */
class  task_dositerepay extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('dositerepay');
        $business->getToDoSiteRepayListData();
    }
}