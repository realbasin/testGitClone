<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 用户业务类
 */
class business_User extends Business
{

    /**
     * 统计校园行长数据，然后写入数据库
     */
    public function statisticsBanker($day = '')
    {
        $bankerList = \Core::dao('User')->getBankerList();

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

            $loanBaseDao = \Core::dao('LoanBase');
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

                $bankerStatisticsDao = \Core::dao('BankerStatistics');
                $bs2 = $bankerStatisticsDao->getBsByUserIdAndDate($p_id, $day);
                if ($bs2) {
                    $bankerStatisticsDao->update($data, ['id' => $bs2['id']]);
                } else {
                    $bankerStatisticsDao->insert($data);
                }

                ++$cnt;
            }
        }
        return $day . "success: " . $cnt;
    }

    public function usersTodayFirstRepayAmount($ids, $start_time, $end_time)
    {
        $userIdList = \Core::dao('UserSta')->getUserIdList($ids);

        $startDate = date('Y-m-d', $start_time);
        $endDate = date('Y-m-d', $end_time);
        return \Core::dao('DealRepay')->usersTodayRepayAmount($userIdList, $startDate, $endDate);
    }

    public function usersTodayMoreRepayAmount($ids, $start_time, $end_time)
    {
        $userIdList = \Core::dao('UserSta')->getUserIdList2($ids);
        $startDate = date('Y-m-d', $start_time);
        $endDate = date('Y-m-d', $end_time);
        return \Core::dao('DealRepay')->usersTodayRepayAmount($userIdList, $startDate, $endDate);
    }

    /**
     * 获取用户下线，可以自定义下线级别
     * @param int $user_id
     * @param int $range 下线级别，默认5级
     * @return array 返回该用户的所有下线
     */
    public function getUserBelongRelations($user_id)
    {
        $user_relations = \Core::dao('UserRelations')->getUserRelationsByUserId($user_id);
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
        return \Core::dao('UserRelations')->usersNew($ids, $start, $end);
    }

    public function getUsersHasPid()
    {
        $key = md5(__CLASS__ . __FUNCTION__);

        $count = \Core::dao('User')->getUsersHasPidCount();
        $size = 3000;
        $range = ceil($count / $size);

        $users = array();

        for ($i = 0; $i < $range; $i++) {
            $key2 = $key . $i;
            if ($tmp = \Core::cache()->get($key2)) {
                for ($ii = 0; $ii < count($tmp); $ii++) {
                    $users[] = $tmp[$ii];
                }
            } else {
                // 防止只获取部分数据
                $users = [];
                break;
            }
        }

        if (count($users) > 0)
            return $users;

        $users2 = [];
        for ($i = 0; $i < $range; $i++) {
            $start = $i * $size;
            $users = \Core::dao('User')->getUsersHasPid($start, $size);

            $key2 = $key . $i;
            \Core::cache()->set($key2, $users, 3600);
            $users2 = array_merge($users2, $users);
        }

        return $users2;
    }

    /**
     * 获取指定用户的上线user_id
     * @param int $user_id
     * @param array $users 以用户user_id为键名，直属上线user_id为值的数组
     * @param string $pids
     * @param int $rank_level 递归时用于标记当前层级的数组,-1表示获取所有上线
     * @return string
     */
    public function getUserPids($user_id, $users, $pids = '', $rank_level = -1)
    {
        if (!is_int($rank_level) || $rank_level < -1) {
            return '';
        }

        //获取所有上线用户id
        if ($rank_level == -1) {
            if (isset($users[$user_id])) {
                $pids .= ',' . $users[$user_id];
                $pids = $this->getUserPids($users[$user_id], $users, $pids, -1);
            }
        } elseif ($rank_level > 0) {
            if (isset($users[$user_id])) {
                $pids .= ',' . $users[$user_id];
                $pids = $this->getUserPids($users[$user_id], $users, $pids, $rank_level - 1);
            }
        }

        if (!empty($pids)) {
            $pids = ltrim($pids, ',');
        }

        return $pids;
    }

    /**
     * 获取指定用户的所有下线user_id 及 根据USER_CHILDREN_RANGE限制下线层级数量的user_id
     * @param int $user_id
     * @param int $users 以上线用户(即：pid)为键名，直属下线user_id为子数组的数组
     * @param array $ranks 递归时用于标记当前层级的数组
     * @param array $children
     * @return array 返回格式：array('all_children'=>'1,2,3,4,5', 'rank_children'=>array(1=>'1,2,3', 2=>'4', 3=>'5'))
     */
    public function getUserChildren($user_id, $users, $ranks = array(), $children = array())
    {
        if (empty($children)) {
            $children = array('all_children' => '', 'rank_children' => '');

            for ($i = 1; $i <= 3; $i++) {
                $children['rank_children'][$i] = '';
            }
        }

        if (isset($users[$user_id])) {
            $ids = implode(',', $users[$user_id]);

            $children['all_children'] .= ',' . $ids;

            if (!isset($ranks[$user_id])) {
                $ranks[$user_id] = 1;
            }

            $rank = intval($ranks[$user_id]);
            if ($rank <= 3) {
                $children['rank_children'][$rank] .= ',' . $ids;
            }

            foreach ($users[$user_id] as $v) {
                $ranks[$v] = $ranks[$user_id] + 1;
                $children = $this->getUserChildren($v, $users, $ranks, $children);
            }
        }

        if (!empty($children['all_children'])) {
            $children['all_children'] = ltrim($children['all_children'], ',');

            foreach ($children['rank_children'] as $k => $v) {
                if (!empty($v)) {
                    $children['rank_children'][$k] = ltrim($v, ',');
                }
            }
        }

        return $children;
    }

}