<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * UserBank控制器
 */

class controller_UserBank extends controller_ApiBase
{

    /**
     * 后台删除一张银行卡（前端删除银行卡请查看 SecurityController.do_Carddel）
     * @param
     * @return array
     */
    public function do_userbankdel()
    {

    }


    /**
     * 后台启用一张银行卡
     * @param
     * @return array
     */
    public function do_userbankenable()
    {

    }


    /**
     * 取得银行卡信息
     * @param
     * @return array
     */
    public function do_getuserbank()
    {

    }

    /**
     * 取得用户已经绑定并生效的所有银行卡
     *
     * @param
     * @return array
     */
    public function do_getuserbanklist()
    {

    }

    /**
     * 不用鉴权，直接通过
     * @param
     * @return array
     */
    public function do_saveuserbank()
    {

    }

}