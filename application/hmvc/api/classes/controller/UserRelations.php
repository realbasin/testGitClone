<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * UserRelations控制器
 */
class controller_UserRelations extends controller_ApiBase {

    /**
     * 获取用户推广收益金额（目前仅限获取1、2级好友投资返利收益）
     * @param $user_id
     * @return float
     */
    public function do_referralsmoney()
    {
        
    }

    /**
     * 新版理财端：推广收益页
     * @param $user_id
     * @return array
     */
    public function do_relationsinfo()
    {
        
    }

    /**
     * 新版理财端：*级好友贡献页
     * @param $user_id
     * @param $rank
     * @param int $page
     * @param int $pagesize
     * @return array
     */
    public function do_relationsranklist()
    {
        
    }

    /** create by liuzw 20160906
     * 刷新用户好友关系表xssd_user_relation的好友关系数据
     * @param int flush_type  1刷新全部好友关系, 2刷新某个时间段内注册的用户好友关系, 3刷新特定user_ids的用户好友关系
     * @return array
     */
    public function do_flushuserrelationstable()
    {
        
    }

}