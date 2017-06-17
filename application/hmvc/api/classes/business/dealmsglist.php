<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 贷款消息列表业务类
 */
class business_dealmsglist extends Business
{

    // 短信内容模版(账单日7天前)
    static $sevenDaysAgoSMSContent = "这是从小树发来的讯号，您本期账单%s元，账单日%s月%s日。加上往期未还共%s元（截至今日）。账单宝宝星星眼等您来噢！";
    // 短信内容模版(账单日3天前)
    static $threeDaysAgoSMSContent = "还记得树疙瘩里的账单吗？您本期账单%s元，账单日%s月%s日。加上往期未还共%s元（截至今日）。若已清还则无须理会。";
    // 短信内容模版(当天)
    static $todaySMSContent = "小树来讯提示：今日是账单日啦，账单宝宝眼巴巴等您呐！本期账单%s元，其中已还%s元，剩余未还为%s元~";

    public function sendMessageToUser()
    {
        //@TODO 短信发送方案   根据要发送的短信条数生成不同个数的临时文件，采取分时间段发送的方式，降低服务器压力
        $send_should_repay_user_file = STORAGE_PATH . '/app/send_to_repay_user';
        if (!file_exists($send_should_repay_user_file)) {
            mkdir($send_should_repay_user_file);
        }

        // 已整理出来需要发送消息的用户数据存放文件
        $toSendMessageUserFile = $send_should_repay_user_file . '/user_list_' . date('Y-m-d') . '.txt';

        try {
            $file_data = trim(file_get_contents($toSendMessageUserFile));
        } catch (\Exception $e) {

        }

        // 整理并保存要发送消息的用户数据
        if (empty($file_data)) {
            $file_list_str = $this->getShouldRepayDealUserList();

            file_put_contents($toSendMessageUserFile, $file_list_str);
        } // 进行对用户数据发送消息
        else {
            $data = explode("\n", $file_data);

            $today_time = strtotime(date('Y-m-d')) - 3600 * 8;

            $param = array();
            foreach ($data as $vv) {
                list($id, $deal_id, $user_id, $mobile, $repay_money, $repay_date, $repay_time) = explode("\t", $vv);

                $param['mobile'] = $mobile;
                $param['user_id'] = $user_id;

                $year = date('Y', strtotime($repay_date));
                $month = date('n', strtotime($repay_date));
                $day = date('j', strtotime($repay_date));

                // 检查是否已还款
                $deal_repay = \Core::dao('dealrepay')->getDealRepayById($id);
                if ($deal_repay['has_repay'] == 1) {
                    continue;
                }

                $dealRepayBusiness = \Core::business('dealrepay');
                $deal_repay_money = $dealRepayBusiness->getDealRepaysByUserId($deal_id, $repay_time);
                $has_repay_money_all = $deal_repay_money['has_repay_money_all'];
                $need_repay_money_all = $deal_repay_money['need_repay_money_all'];
                $days = ceil(($repay_time - $today_time) / (3600 * 24));

                $result = false;
                if ($days == 0) {
                    $param['content'] = sprintf(self::$todaySMSContent, $repay_money, $has_repay_money_all, $need_repay_money_all);

                    $content = $repay_money . "," . $has_repay_money_all . "," . $need_repay_money_all;
                    //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_TODAY_SMS');

                } elseif ($days == 3) {
                    $param['content'] = sprintf(self::$threeDaysAgoSMSContent, $repay_money, $month, $day, $need_repay_money_all);

                    $content = $repay_money . "," . $month . "," . $day . "," . $need_repay_money_all;
                    //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_THREE_SMS');
                } elseif ($days == 7) {
                    $param['content'] = sprintf(self::$sevenDaysAgoSMSContent, $repay_money, $month, $day, $need_repay_money_all);

                    $content = $repay_money . "," . $month . "," . $day . "," . $need_repay_money_all;
                    //@TODO    发送短信
//                    $result = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_REPAY_SEVEN_SMS');
                }

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

            // 发送完成后删除文件
            unlink($toSendMessageUserFile);
        }
    }

    // 获取待催款用户数据，整理成字符串形式，待保存到文本
    private function getShouldRepayDealUserList()
    {
        $list = \Core::dao('dealrepay')->getShouldRepayDealUserList();
        $count = count($list);

        $list_str = '';
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $id = $list[$i]['id'];
                $deal_id = $list[$i]['deal_id'];
                $user_id = $list[$i]['user_id'];
                $user_info = \Core::dao('user')->getUserById($user_id);
                $mobile = $user_info['mobile_encrypt'];
                if (strlen($mobile) != 11 || !is_numeric($mobile)) {
                    continue;
                }
                $repay_money = $list[$i]['repay_money'] + $list[$i]['manage_money'] + $list[$i]['manage_impose_money'] + $list[$i]['impose_money'];
                $repay_date = $list[$i]['repay_date'];
                $repay_time = $list[$i]['repay_time'];

                $list_str .= $id . "\t" . $deal_id . "\t" . $user_id . "\t" . $mobile . "\t" . $repay_money . "\t" . $repay_date . "\t" . $repay_time . "\n";
            }
        }

        return $list_str;
    }

    private function saveDealMsgList($param){
        $app_env = \Core::config()->getEnvironment();
        if ($app_env != 'production') {
            $content_prefix = "【小树时代测试】";
        }  else {
            $content_prefix = "【小树时代】";
        }

        $now_time = time();
        $data = [
            'dest' => $param['mobile'],
            'send_type' => 0,
            'content' => $content_prefix.$param['content'],
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

        \Core::dao('dealmsglist')->insert($data);
    }

}