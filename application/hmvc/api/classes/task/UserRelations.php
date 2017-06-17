<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 刷新用户关系表
 * Class task_UserRelations
 */
class  task_UserRelations extends Task {

    function execute(CliArgs $args)
    {
        $type = $args->get('type');
        $date = $args->get('date');

        $userRelationsBusiness = \Core::business('UserRelations');
        // 更新全部用户，命令行执行：/usr/local/php/bin/php artisan userrelations --type=all
        if ($type == 'all') {
            $rs = $userRelationsBusiness->flushTable();
        }
        // 更新某一天(北京时间)注册的新用户，命令执行：/usr/local/php/bin/php artisan userrelations --date=2017-04-24
        elseif (strtotime($date)) {
            $register_starttime = strtotime($date);
            $register_endtime = $register_starttime + 24*3600 -1;
            $rs = $userRelationsBusiness->flushTableByTimes($register_starttime, $register_endtime);
        }
        // 更新最近两天（北京时间）注册的新用户，命令行执行：/usr/local/php/bin/php artisan userrelations
        else {
            $register_starttime = time() - 24 * 3600 * 2;
            $register_endtime = time();
            $rs = $userRelationsBusiness->flushTableByTimes($register_starttime, $register_endtime);
        }
    }
}