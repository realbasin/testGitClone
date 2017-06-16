<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 校园行长的统计
 * Class task_statBankers
 */
class  task_statBankers extends Task {

    function execute(CliArgs $args)
    {
        $date = $args->get('date');
        if (!strtotime($date))
            $date = '';

        $userBusiness = \Core::business('user');
        $userBusiness->statisticsBanker($date);
    }
}