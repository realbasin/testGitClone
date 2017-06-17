<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * LLPay控制器
 */
class controller_LLPay extends controller_ApiBase {

    /**
     * 查看在连连支付已绑定的银行卡列表信息
     * @param 
     * @return array
     */
    public function do_bankcardbindlist()
    {
        
    }

    /**
     * 查询银行卡BIN信息（卡片所属和卡类型）
     * @param $card_no
     * @return array
     */
    public function do_showcard($card_no)
    {
        
    }

    /**
     * 银行卡解约
     * @param 
     * @return array
     */
    public function do_unbindcard()
    {
        
    }

    /**
     * WEB认证支付
     * @param 
     * @return array
     */
    public function do_authpay()
    {
        
    }

    /**
     * 支付结果查询
     * @param 
     * @return array
     */
    public function do_orderquery()
    {
        
    }

    /**
     * 同步/异步通知接口
     * @param 
     * @return array
     */
    public function do_verifyreturn()
    {
        
    }

    /**
     * WAP签约授权（跳转到连连支付页面上进行认证）用do_方式提交 TODO 未验证完整
     * @param 
     * @return array
     */
    public function do_signapply()
    {
        
    }

    /**
     * WEB银行卡签约（跳转到连连支付页面上进行认证）
     * @param 
     * @return array
     */
    public function do_authsign()
    {
        
    }
    
}