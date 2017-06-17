<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * Deal控制器
 */
class controller_Deal extends controller_ApiBase {

    /**
     * 获取基本贷款配置
     * @param 
     * @return array
     */
    public function do_common()
    {
        
    }

    /**
     * 查看最新申请的贷款记录(最新一条数据)
     * @param $user_id
     * @return array
     */
    public function do_deallist($user_id)
    {
        
    }

    /**
     * 查看贷款申请草稿
     * @param 
     * @return array
     */
    public function do_tempdeal()
    {
        
    }

    /**
     * 获取用户信息及贷款类型【信用贷】
     * @param 
     * @return array
     */
    public function do_loantype()
    {
        
    }

    /**
     * 获取具体的贷款类型信息
     * @param $loanTypeId
     * @return array
     */
    public function do_loaninfo($loanTypeId)
    {
        
    }

    /**
     * 保存临时贷款信息【保存草稿】
     * @param 
     * @return array
     */
    public function do_savetempdeal()
    {
        
    }

    /**
     * 根据贷款类型更新贷款信息
     * @param 
     * @return array
     */
    public function do_savedeal()
    {
        
    }

    /**
     * 理财个人中心投资记录列表(兼容PC端、App端)
     * @param 
     * @return array
     */
    public function do_investlist()
    {
        
    }

    /**
     * 理财App个人中心债权转让列表
     * @param 
     * @return array
     */
    public function do_transferlist()
    {
        
    }

    /**
     * 根据贷款类型获取对应的贷款利率
     * @param 
     *
     * @return array
     */
    public function do_dealloantypeuserlevels()
    {
        
    }

}