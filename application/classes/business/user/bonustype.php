<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_user_bonustype extends Business {
	public function business() {
		
	}

    /**
     * 发放方式
     * @param int $send_type
     * @return array|mixed
     */
	public function getSendType($send_type=0) {
        $sendTypeArr = array(
            1 => '注册发放',
            2 => '手动发放',
            3 => '用户领取',
            4 => '积分兑换',
        );
        return ($send_type>0) ? \Core::arrayGet($sendTypeArr, $send_type, array()) : $sendTypeArr;
    }
}