<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 校园行长的统计
 * Class task_StatBankers
 */
class  task_StatBankers extends Task {

    function execute(CliArgs $args)
    {
        $date = $args->get('date');
        if (!strtotime($date))
            $date = '';

        $userBusiness = \Core::business('User');
        $userBusiness->statisticsBanker($date);
    }
}