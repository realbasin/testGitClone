<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 优惠卷过期发送短信提醒
 */
class business_BonusMsgRemind extends Business
{

    static $SMSContent = "尊敬的小树时代会员，您的投资红包即将到期，请尽快使用喔！登陆App即可查看详情";

    public function sendMessageToUser()
    {
        $file_data = $this->getBonusList();

        $param = array();
        foreach ($file_data as $vv) {
            $param['mobile'] = $vv['mobile'];
            $param['user_id'] = $vv['user_id'];
            $param['content'] = self::$SMSContent;

            $content = '11';
//            $result = SMSService::sendSMSViaUcpaas($vv['mobile'], $content, 'REWARD_REMIND');
            $result = true;
            //@TODO    发送短信

            // 处理发送短信后所返回的结果
            if ($result === true) {
                $param['status'] = 1;
                $param['msg'] = '';
            } else {
                $param['status'] = 0;
                $param['msg'] = $result;
            }

            $this->saveDealMsgList($param);
        }

    }

    public function getBonusList()
    {
        $seven_day = date('Y-m-d', strtotime('+7day', time()));
        $fifteen_day = date('Y-m-d', strtotime('+15day', time()));

        $list = \Core::dao('BonusUser')->getBonusList($seven_day, $fifteen_day);

        $count = count($list);
        $list_arr = array();
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $user_id = $list[$i]['user_id'];
                $user_info = \Core::dao('User')->getUserById($user_id);
                $mobile = $user_info['mobile_encrypt'];

                if (strlen($mobile) != 11 || !is_numeric($mobile)) {
                    continue;
                }

                $list_arr[$i]['user_id'] = $user_id;
                $list_arr[$i]['mobile'] = $mobile;
            }
        }
        return $list_arr;
    }

    public function saveDealMsgList($param)
    {
        $app_env = \Core::config()->getEnvironment();
        if ($app_env != 'production') {
            $content_prefix = "【小树时代理财测试】";
        } else {
            $content_prefix = "【小树时代理财】";
        }

        $now_time = time();
        $data = [
            'dest' => $param['mobile'],
            'send_type' => 0,
            'content' => $content_prefix . $param['content'],
            'user_id' => $param['user_id'],
            'is_success' => $param['status'],
            'result' => $param['msg'],
            'send_time' => $now_time,
            'create_time' => $now_time,
            'is_send' => 1,
            'is_html' => 0,
            'title' => "用户优惠券到期短信提醒",
            'is_youhui' => 0,
            'youhui_id' => 0
        ];

        \Core::dao('DealMsgList')->insert($data);
    }

}