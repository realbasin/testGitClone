<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/**
 * 用户关系业务类
 */
class business_UserRelations extends Business
{

    /**
     * 刷新全部用户的层级关系表数据
     * @return string
     */
    public function flushTable()
    {
        // 先清空表
        $userRelationsDao = \Core::dao('UserRelations');
        $userRelationsDao->truncate();

        // 禁用缓存
        $GLOBALS['del'] = 1;

        $userBusiness = \Core::business('User');
        $users = $userBusiness->getUsersHasPid();
        if (empty($users)) {
            return '没有需要整理的用户关系';
        }

        $users_parents = $users_children = array();
        foreach ($users as $v) {
            $users_parents[$v['id']] = $v['pid'];
            $users_children[$v['pid']][] = $v['id'];
        }

        // 所有有上线的用户
        foreach ($users_parents as $k => $v) {
            $all_parents = $userBusiness->getUserPids($k, $users_parents);

            $data = ['user_id' => $k, 'all_children' => '', 'rank_children' => '', 'all_parents' => $all_parents, 'update_time' => time()];
            $userRelationsDao->insert($data);
        }

        // 所有有下线的用户
        foreach ($users_children as $k => $v) {
            $children = $userBusiness->getUserChildren($k, $users_children);
            $all_children = $children['all_children'];

            // 将下线层级数据转换成数组
            if (!empty($children['rank_children'])) {
                foreach ($children['rank_children'] as $k2 => $v2) {
                    if (!empty($v2)) {
                        $children['rank_children'][$k2] = explode(',', $v2);
                    } else {
                        unset($children['rank_children'][$k2]);
                    }
                }
            }
            // 将下线层级数据转换成json格式
            $rank_children = !empty($children['rank_children']) ? json_encode($children['rank_children']) : '';

            if (isset($users_parents[$k])) {
                $data = ['all_children' => $all_children, 'rank_children' => $rank_children, 'update_time' => time()];
                $userRelationsDao->update($data,['user_id'=>$k]);
            } else {
                $data = ['user_id' => $k, 'all_children' => $all_children, 'rank_children' => $rank_children, 'all_parents' => '', 'update_time' => time()];
                $userRelationsDao->insert($data);
            }
        }
        return 'done';
    }

    /**
     * 刷新某个时间段内注册的用户的层级关系表数据（含其上线，上上线）
     * @param int $starttime 起始时间
     * @param int $endtime 结束时间
     * @return string
     */
    public function flushTableByTimes($starttime, $endtime)
    {
        if ($starttime < 0 || $endtime < 0 || $starttime > $endtime) {
            return '起始或结束时间不正确';
        }

        //获取需要更新的用户
        $user_ids = \Core::dao('User')->getUserIdsByTimes($starttime, $endtime, -1);
        if (count($user_ids) == 0) {
            return '没有需要整理的用户关系1';
        }

        return $this->flushTableByUserIds($user_ids);
    }

    /**
     * 刷新特定user_ids用户的层级关系表数据
     * @param array $user_ids 需要刷新的用户id数组（格式如：[1,2,3]）
     * @parram int $flush_rank 刷新层级（0刷新其上线，上上线, 1只刷新自己-不刷新其上线，上上线）
     * @return string
     */
    public function flushTableByUserIds($user_ids, $flush_rank = 0)
    {
        // 禁用缓存
        $GLOBALS['del'] = 1;

        // 过滤user_ids
        foreach ($user_ids as $k => $v) {
            if (empty($v)) {
                unset($user_ids[$k]);
            } elseif ($v < 0) {
                return '部分用户id有错误,处理中断';
            }
        }

        //获取所有含有pid的用户
        $userBusiness = \Core::business('user');
        $users = $userBusiness->getUsersHasPid();
        if (empty($users)) {
            return '没有需要整理的用户关系2';
        }

        //以数组形式整理保存好友关系信息
        $users_parents = $users_children = array();
        foreach ($users as $v) {
            $users_parents[$v['id']] = $v['pid'];  //以用户user_id为键名，直属上线user_id为值
            $users_children[$v['pid']][] = $v['id']; //以上线用户(即：pid)为键名，直属下线user_id为子数组
        }

        $user_ids_str = implode(',', $user_ids);

        // 0刷新其上线，上上线, 1只刷新自己-不刷新其上线，上上线
        if($flush_rank == 0) {
            foreach ($user_ids as $k => $v) {
                $user_ids_str = $user_ids_str . ',' . $userBusiness->getUserPids($v, $users_parents, '', 2);
            }
        }
        $need_update_user_ids = explode(',', $user_ids_str);

        $need_update_user_ids = array_unique($need_update_user_ids);

        foreach ($need_update_user_ids as $k => $v) {
            if (empty($v)) {
                unset($need_update_user_ids[$k]);
            }
        }

        //需要更新上线字段、下线字段(all_parents,all_children,rank_children)的用户ids
        $need_update_parent_user_ids = $need_update_children_user_ids = $need_update_user_ids;
        //\App\Services\LogService::mlog('$need_update_parent_user_ids=' . json_encode($need_update_parent_user_ids));
        //\App\Services\LogService::mlog('$need_update_children_user_ids=' . json_encode($need_update_children_user_ids));

        $userRelationsDao = \Core::dao('UserRelations');
        foreach ($need_update_parent_user_ids as $k => $v) {
            $all_parents = $userBusiness->getUserPids($v, $users_parents, '', -1);

            if (!empty($userRelationsDao->getUserRelationsByUserId($v))) {
                $data = ['all_children' => '', 'rank_children' => '', 'all_parents' => $all_parents, 'update_time' => time()];
                $userRelationsDao->update($data,['user_id' => $k]);
            } else {
                $data = ['user_id' => $k, 'all_children' => '', 'rank_children' => '', 'all_parents' => $all_parents, 'update_time' => time()];
                $userRelationsDao->insert($data);
            }
        }

        foreach ($need_update_children_user_ids as $k => $v) {
            $children = $userBusiness->getUserChildren($v, $users_children);
            $all_children = $children['all_children'];

            // 将下线层级数据转换成数组
            if (!empty($children['rank_children'])) {
                foreach ($children['rank_children'] as $k2 => $v2) {
                    if (!empty($v2)) {
                        $children['rank_children'][$k2] = explode(',', $v2);
                    } else {
                        unset($children['rank_children'][$k2]);
                    }
                }
            }
            // 将下线层级数据转换成json格式
            $rank_children = !empty($children['rank_children']) ? json_encode($children['rank_children']) : '';

            if (!empty($userRelationsDao->etUserRelationsByUserId($v))) {
                $data = ['all_children' => $all_children, 'rank_children' => $rank_children, 'update_time' => time()];
                $userRelationsDao->update($data,['user_id' => $k]);
            } else {
                $data = ['user_id' => $k, 'all_children' => $all_children, 'rank_children' => $rank_children, 'all_parents' => '', 'update_time' => time()];
                $userRelationsDao->insert($data);
            }
        }

        return 'done';
    }

}