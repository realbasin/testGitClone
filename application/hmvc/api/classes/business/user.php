<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/**
 * 用户业务类
 */
class business_user extends Business
{

    /**
     * 统计校园行长数据，然后写入数据库
     */
    public function statisticsBanker($day = '')
    {
        $bankerList = \Core::dao('user')->getBankerList();

        $first_day = '2015-10-31';
        if ($day != '' && strtotime($day) < strtotime($first_day))
            $day = $first_day;

        if ($day == $first_day) {
            $start_time = strtotime('1970-1-1');
            $end_time = strtotime("+1 day", strtotime($first_day));
        } else if ($day != '') {
            $start_time = strtotime($day);
            $end_time = strtotime("+1 day", strtotime($day));
        } else {
            $day = date('Y-m-d', time() - 3600 * 24);
            $start_time = strtotime($day);
            $end_time = strtotime(date('Y-m-d'));
        }

        if ($start_time > time())
            return "it's tommorrow!!!";

        $cnt = 0;
        foreach ($bankerList as $v) {
            $row = array();
            $p_id = $v;

            $nextUsers = $this->getUserBelongRelations($p_id);
            if (empty($nextUsers) || empty($nextUsers['all']))
                continue;

            $nextUsers = $nextUsers['all'];

            $row['user_new'] = self::usersNew($nextUsers, $start_time, $end_time);

            $loanBaseDao = \Core::dao('loanbase');
            $row['user_borrow'] = $loanBaseDao->usersDealCount($nextUsers, $start_time, $end_time);

            $row['borrow_amount'] = $loanBaseDao->usersBorrowAmount($nextUsers, $start_time, $end_time);

            $row['borrow_first'] = $loanBaseDao->usersFirstDealAmount($nextUsers, $start_time, $end_time);

            $row['borrow_more'] = $loanBaseDao->usersMoreDealAmount($nextUsers, $start_time, $end_time);

            $row['repay_first'] = $this->usersTodayFirstRepayAmount($nextUsers, $start_time, $end_time);

            $row['repay_more'] = $this->usersTodayMoreRepayAmount($nextUsers, $start_time, $end_time);

            $row['repay_amount'] = $row['repay_first'] + $row['repay_more'];

            $doAction = false;
            foreach ($row as $value) {
                if ($value > 0) {
                    $doAction = true;
                    break;
                }
            }

            // 没有任何记录的不写到数据库里面
            if ($doAction) {
                $data = [
                    'user_id' => $p_id,
                    'sta_date' => $day,
                    'repay_amount' => $row['repay_amount'],
                    'repay_more' => $row['repay_more'],
                    'repay_fisrt' => $row['repay_first'],
                    'borrow_more' => $row['borrow_more'],
                    'borrow_first' => $row['borrow_first'],
                    'borrow_amount' => $row['borrow_amount'],
                    'user_borrow' => $row['user_borrow'],
                    'user_new' => $row['user_new']
                ];

                $bankerStatisticsDao = \Core::dao('bankerstatistics');
                $bs2 = $bankerStatisticsDao->getBsByUserIdAndDate($p_id,$day);
                if ($bs2){
                    $bankerStatisticsDao->update($data,['id' => $bs2['id']]);
                }else{
                    $bankerStatisticsDao->insert($data);
                }

                ++$cnt;
            }
        }
        return $day . "success: " . $cnt;
    }

    public function usersTodayFirstRepayAmount($ids, $start_time, $end_time)
    {
        $userIdList = \Core::dao('usersta')->getUserIdList($ids);

        $startDate = date('Y-m-d', $start_time);
        $endDate = date('Y-m-d', $end_time);
        return \Core::dao('dealrepay')->usersTodayRepayAmount($userIdList, $startDate, $endDate);
    }

    public function usersTodayMoreRepayAmount($ids, $start_time, $end_time)
    {
        $userIdList = \Core::dao('usersta')->getUserIdList2($ids);
        $startDate = date('Y-m-d', $start_time);
        $endDate = date('Y-m-d', $end_time);
        return \Core::dao('dealrepay')->usersTodayRepayAmount($userIdList, $startDate, $endDate);
    }

    /**
     * 获取用户下线，可以自定义下线级别
     * @param int $user_id
     * @param int $range 下线级别，默认5级
     * @return array 返回该用户的所有下线
     */
    public function getUserBelongRelations($user_id)
    {
        $user_relations = \Core::dao('userrelations')->getUserRelationsByUserId($user_id);
        if (!empty($user_relations['rank_children'])) {
            $user_relations['rank_children'] = json_decode($user_relations['rank_children'], true);
            $usersHasPid['rank'] = $user_relations['rank_children'];
            $usersHasPid['all'] = array();
            foreach ($usersHasPid['rank'] as $v) {
                $usersHasPid['all'] = array_merge($usersHasPid['all'], $v);
            }
        } else {
            $usersHasPid['rank'] = array();
            $usersHasPid['all'] = array();
        }

        return $usersHasPid;
    }

    public function usersNew($ids, $start, $end)
    {
        return \Core::dao('userrelations')->usersNew($ids, $start, $end);
    }

}