<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 网站垫付task
 * Class task_DoSiteRepay
 */
class  task_DoSiteRepay extends Task {

    function execute(CliArgs $args)
    {
        $business = \Core::business('DoSiteRepay');
        $business->getToDoSiteRepayListData();
    }
}