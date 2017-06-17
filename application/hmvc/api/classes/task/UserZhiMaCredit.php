<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 检查用户是否取消芝麻信用授权,若已取消授权，则将zm_openid更新为空，其他数据不变
 * Class task_UserZhiMaCredit
 */
class  task_UserZhiMaCredit extends Task {

    function execute(CliArgs $args){
        $business = \Core::business('ZhiMaCredit');
        $business->checkUserZhiMaCreditSubscribe();
    }
}