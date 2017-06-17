<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 贷款还款业务类
 */
class business_DealRepay extends Business
{

    /**
     * 用户自动还款
     */
    public function autoDealRepay()
    {
        $dealRepayDao = \Core::dao('DealRepay');

        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $deal_repays = $dealRepayDao->getNoRepayDealRepaysByDate($start_date, $end_date);

        $count = count($deal_repays);

        $config = getSiteConfig();
        $www_host = $config['www_host'];

        $url = 'http://' . $www_host . '/ajax-autorepay';

        $repay_succs = $repay_fails = array();

        $total = 0;

        $userDao = \Core::dao('User');

        for ($i = 0; $i < $count; $i++) {
            $data['user_id'] = $deal_repays[$i]['user_id'];
            $data['id'] = $deal_repays[$i]['deal_id'];
            $data['ids'] = $deal_repays[$i]['l_key'];
            $data['site_token'] = $config['site_token'];

            //TODO    加锁
            try {
                $result = curl_post($url, $data);
                $result = json_decode($result, true);

                $d = array(
                    'deal_id' => $deal_repays[$i]['deal_id'],
                    'user_id' => $deal_repays[$i]['user_id'],
                    'deal_repay_id' => $deal_repays[$i]['id'],
                    'money' => $deal_repays[$i]['repay_money']
                );
                if ($result['status'] == 1) {
                    $repay_succs[] = $d;
                    $total = +$deal_repays[$i]['true_repay_money'];
                    echo 'success: ' . print_r($d, true);
                } else {
                    // 优化还款提示短信 modify by maixh 2016-05-11 ----start----
                    if (strpos($result['info'], '余额不足') !== false) {
                        $hour = date("H");

                        $user_info = $userDao->getUserById($deal_repays[$i]['user_id']);
                        $mobile = $user_info['mobile_encrypt'];

                        // 只在该任务执行时间为9-10点之间、18-19点之间时触发发送短信
                        if (in_array($hour, array(9, 18))) {
                            $repay_money = $deal_repays[$i]['repay_money'] + $deal_repays[$i]['manage_money'] + $deal_repays[$i]['manage_impose_money'] + $deal_repays[$i]['impose_money'];
                            $deal_repay_data = $this->getDealRepaysByUserId($deal_repays[$i]['deal_id'], $deal_repays[$i]['repay_time']);
                            $need_repay_money_all = $deal_repay_data['need_repay_money_all'];

                            // "今日要还款啦，账单宝宝眼巴巴等您呐！本期账单%s元，往期欠款共%s元（截至今日），赶紧还款，解救账单宝宝哟~"
                            $content = $repay_money . "," . $need_repay_money_all;
                            if ($hour == 9) {
                                //TODO    发送短信
//                                $res = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_AUTO_REPAY_SMS_ONE');
                            } else {
                                //TODO    发送短信
                                // （在短信后台上的模版上）短信内容->来自账单的呼唤~本期账单%s元、加上往期未还共%s元，请记得账单日为今日呦~;
//                                $res = SMSService::sendSMSViaUcpaas($mobile, $content, 'TPL_DEAL_AUTO_REPAY_SMS_TWO');
                            }
                        }
                    }
                    // 优化还款提示短信 modify by maixh 2016-05-11 ----end----

                    $d['fail_reason'] = $result['info'];
                    $repay_fails[] = $d;
                    echo 'failed: ' . print_r($d, true);
                }
            } catch (\Exception $e) {
                $d['fail_reason'] = $e->getMessage();
                $repay_fails[] = $d;
                echo 'failed: ' . print_r($d, true);
            } finally {
                //TODO    解锁
            }

            //TODO    写日志

        }

        $str = '本次自动还款统计: 成功数 ' . count($repay_succs) . ',金额 ' . $total . '; 失败数: ' . count($repay_fails) . '.';
        //TODO    写日志

        echo $str;

        return true;
    }

    /**
     * 获取贷款还款列表，统计已还款总额、待还款总额
     * @param $deal_id
     * @param $repay_time_end 截至还款日期
     * @return array
     */
    public function getDealRepaysByUserId($deal_id, $repay_time_end)
    {
        $deal = \Core::dao('LoanBase')->getRowById($deal_id);

        $now_time = time();
        $today_time = strtotime(date('Y-m-d'));
        $deal_repay_list = \Core::dao('DealRepay')->getDealRepayList($deal_id);

        $has_repay_money_all = $need_repay_money_all = 0;
        for ($i = 0; $i < count($deal_repay_list); $i++) {
            $has_repay = $deal_repay_list[$i]['has_repay'];
            $repay_money = $deal_repay_list[$i]['repay_money'];
            $true_repay_money = $deal_repay_list[$i]['true_repay_money'];
            $manage_money = $deal_repay_list[$i]['manage_money'];
            $true_manage_money = $deal_repay_list[$i]['true_manage_money'];
            $manage_impose_money = $deal_repay_list[$i]['manage_impose_money'];
            $impose_money = $deal_repay_list[$i]['impose_money'];
            $mortgage_fee = $deal_repay_list[$i]['mortgage_fee'];

            if ($has_repay == 1) {
                $has_repay_money_all += $true_repay_money + $true_manage_money + $manage_impose_money + $impose_money + $mortgage_fee;
            } elseif ($has_repay == 0) {
                $repay_time = $deal_repay_list[$i]['repay_time'];

                // 截至到当期待还
                if ($repay_time <= $repay_time_end) {
                    //判断是否罚息
                    if ($now_time > ($repay_time + 24 * 3600 - 1) && $repay_money > 0) {
                        $day = ceil(($today_time - $repay_time) / 24 / 3600);
                        $conf = getXSConf('YZ_IMPSE_DAY');
                        if ($day >= $conf) {
                            $impose_fee = floatval(trim($deal['deal_loan_type']['impose_fee_day2']));
                            $manage_impose_fee = floatval(trim($deal['manage_impose_fee_day2']));
                        } else {
                            $impose_fee = floatval(trim($deal['deal_loan_type']['impose_fee_day1']));
                            $manage_impose_fee = floatval(trim($deal['deal_loan_type']['manage_impose_fee_day1']));
                        }

                        $impose_money = floatval($repay_money * $impose_fee * $day / 100);
                        $manage_impose_money = floatval($repay_money * $manage_impose_fee * $day / 100);
                    }

                    if ($deal_repay_list[$i]['is_site_repay']) {
                        $manage_money_real = $manage_money;
                    } else {
                        $manage_money_real = $manage_money - $true_manage_money;
                    }

                    $need_repay_money_all += $repay_money + $manage_money_real + $impose_money + $manage_impose_money + $mortgage_fee;
                }
            }
        }

        return array(
            'has_repay_money_all' => round($has_repay_money_all, 2),
            'need_repay_money_all' => round($need_repay_money_all, 2));
    }

}