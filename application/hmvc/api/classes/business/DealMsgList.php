<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 贷款消息列表业务类
 */
class business_DealMsgList extends Business
{

    // 短信内容模版(账单日7天前)
    static $sevenDaysAgoSMSContent = "这是从小树发来的讯号，您本期账单%s元，账单日%s月%s日。加上往期未还共%s元（截至今日）。账单宝宝星星眼等您来噢！";
    // 短信内容模版(账单日3天前)
    static $threeDaysAgoSMSContent = "还记得树疙瘩里的账单吗？您本期账单%s元，账单日%s月%s日。加上往期未还共%s元（截至今日）。若已清还则无须理会。";
    // 短信内容模版(当天)
    static $todaySMSContent = "小树来讯提示：今日是账单日啦，账单宝宝眼巴巴等您呐！本期账单%s元，其中已还%s元，剩余未还为%s元~";

    public function sendMessageToUser()
    {
        //@TODO 短信发送方案   发送到mq队列，非实时
        $data = $this->getShouldRepayDealUserList();

        $today_time = strtotime(date('Y-m-d'));
        foreach ($data as $item) {
            // 检查是否已还款
            $deal_repay = \Core::dao('DealRepay')->getDealRepayById($item['id']);
            if ($deal_repay['has_repay'] == 1) {
                continue;
            }

            $dealRepayBusiness = \Core::business('DealRepay');
            $deal_repay_money = $dealRepayBusiness->getDealRepaysByUserId($item['deal_id'], $item['repay_time']);
            $has_repay_money_all = $deal_repay_money['has_repay_money_all'];
            $need_repay_money_all = $deal_repay_money['need_repay_money_all'];
            $days = ceil(($item['repay_time'] - $today_time) / (3600 * 24));

            $month = date('n', strtotime($item['repay_date']));
            $day = date('j', strtotime($item['repay_date']));

            $result = false;
            if ($days == 0) {
                $param['content'] = sprintf(self::$todaySMSContent, $item['repay_time'], $has_repay_money_all, $need_repay_money_all);

                $content = $item['repay_money'] . "," . $has_repay_money_all . "," . $need_repay_money_all;
                //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_TODAY_SMS');

            } elseif ($days == 3) {
                $param['content'] = sprintf(self::$threeDaysAgoSMSContent, $item['repay_time'], $month, $day, $need_repay_money_all);

                $content = $item['repay_money'] . "," . $month . "," . $day . "," . $need_repay_money_all;
                //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_THREE_SMS');
            } elseif ($days == 7) {
                $param['content'] = sprintf(self::$sevenDaysAgoSMSContent, $item['repay_time'], $month, $day, $need_repay_money_all);

                $content = $item['repay_money'] . "," . $month . "," . $day . "," . $need_repay_money_all;
                //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_SEVEN_SMS');
            }

            $param['mobile'] = $item['mobile'];
            $param['user_id'] = $item['user_id'];

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

    // 获取待催款用户数据
    private function getShouldRepayDealUserList()
    {
        $list = \Core::dao('DealRepay')->getShouldRepayDealUserList();

        $result = [];
        foreach ($list as $item) {
            $user = \Core::dao('User')->getUserById($item['user_id']);
            $mobile = $user['mobile_encrypt'];
            if (strlen($mobile) != 11 || !is_numeric($mobile)) {
                continue;
            }

            $data = [
                'id' => $item['id'],
                'deal_id' => $item['deal_id'],
                'mobile' => $mobile,
                'repay_money' => $item['repay_money'] + $item['manage_money'] + $item['manage_impose_money'] + $item['impose_money'],
                'repay_date' => $item['repay_date'],
                'repay_time' => $item['repay_time']
            ];
            $result[] = $data;
        }

        return $result;
    }

    private function saveDealMsgList($param)
    {
        $app_env = \Core::config()->getEnvironment();
        if ($app_env != 'production') {
            $content_prefix = "【小树时代测试】";
        } else {
            $content_prefix = "【小树时代】";
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
            'title' => "催款短信通知",
            'is_youhui' => 0,
            'youhui_id' => 0
        ];

        \Core::dao('DealMsgList')->insert($data);
    }

}