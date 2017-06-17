<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 芝麻信用数据反馈
 * Class task_ZhiMaFeedback
 */
class  task_ZhiMaFeedback extends Task {

    function execute(CliArgs $args){
        $business = \Core::business('ZhiMaCredit');
        $business->dealDataFeedback();
    }
}