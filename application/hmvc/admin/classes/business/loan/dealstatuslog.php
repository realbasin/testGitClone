<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_dealstatuslog extends Business {
    public function business() {

    }

    /**
     * 存储借款状态信息
     * @param $user_id
     * @param $deal_id
     * @param $type 0、提交成功；1、同盾通过；2、同盾拒绝；3、认领；4、初审通过；5、回退初审通过；6、初审失败；
     *                7、复审通过；8、回退初审；9、满标放款；10、借款协议生效；11、流标；12、提现申请-已付款；
     */
    public function saveDealStatusMsg($user_id,$deal_id,$type)
    {
        $result = array('status' => 0, 'show_err' => '');
        $user_id = intval($user_id);
        $deal_id = intval($deal_id);
        $type = intval($type);
        if ($user_id == 0 || $deal_id == 0 /*|| $type < 0 || $type > 12*/) {
            $result['show_err'] = "错误操作";
            return $result;
        }
        $data = array(
            'deal_id' => $deal_id,
            'user_id' => $user_id,
            'type' => $type,
            'create_time' => time(),
        );
        $deal_status_log_id = \Core::dao('loan_dealstatuslog')->insert($data);
        return $deal_status_log_id;
    }
}