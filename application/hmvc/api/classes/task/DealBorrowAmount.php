<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 借款金额统计
 * Class task_DealBorrowAmount
 */
class  task_DealBorrowAmount extends Task
{

    function execute(CliArgs $args)
    {
        $business = \Core::business('DealLoanAmount');
        $business->autoCountAmount();
    }
}