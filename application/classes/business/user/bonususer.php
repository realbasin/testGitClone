<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_user_bonususer extends Business {
    /**
     * 通过各种条件查询用户优惠券使用情况（用户优惠券列表详情）
     * @param $param
     * @param int $page
     * @param int $pagesize
     * @return array
     */
	public function getBonusUserLogListByCondition($param, $page=1, $pagesize=15) {
	    $condition = " WHERE bt.use_type=".intval($param['use_type']);

        if (isset($param['bonus_type_id']) && intval($param['bonus_type_id']) > 0) {
            $condition .= " AND bu.bonus_type_id=".$param['bonus_type_id'];
        }
        if (isset($param['bonus_type_name']) && !empty($param['bonus_type_name'])) {
            $bonusTypeWhere['bonus_type_name like'] = '%'.$param['bonus_type_name'].'%';
            //获取有效的优惠券类型ID集合
            $bonusTypeIds = \Core::dao('user_bonustype')->findCol('id', $bonusTypeWhere, true);

            //不存在有效的优惠券类型ID集合，返回空数据
            if (empty($bonusTypeIds)) {
                return array();
            }
            $condition .= " AND bu.bonus_type_id IN (".implode(',', $bonusTypeIds).")";
        }
        //领取时间（开始）
        if (isset($param['start_time']) && !empty($param['start_time'])) {
            $condition .= " AND bu.drawed_time >= ". strtotime($param['start_time']);
        }
        //领取时间（结束）
        if (isset($param['end_time']) && !empty($param['end_time'])) {
            $condition .= " AND bu.drawed_time <= ". strtotime($param['end_time']);
        }
        //优惠券号
        if (isset($param['bonus_sn']) && !empty($param['bonus_sn'])) {
            $condition .= " AND bu.bonus_sn = ".$param['bonus_sn'];
        }
        //用户ID
        if (isset($param['user_id']) && intval($param['user_id']) > 0) {
            $condition .= " AND bu.user_id = ".$param['user_id'];
        }
        //用户名【通过查询到用户ID，以避免用户修改用户名，导致查询时遗漏数据】
        if (isset($param['user_name']) && !empty($param['user_name'])) {
            $user_id = \Core::dao('user_user')->findCol('id', array('user_name'=>$param['user_name']));
            if (empty($user_id)) {
                return array();
            }
            $condition .= " AND bu.user_id = ".$user_id;
        }
        //手机号码【查询用户ID】
        if (isset($param['mobile']) && !empty($param['mobile'])) {
            $user_id = \Core::dao('user_user')->findCol('id', array('AES_DECRYPT(mobile_encrypt,"'.AES_DECRYPT_KEY.'")'=>$param['mobile']));
            if (empty($user_id)) {
                return array();
            }
            $condition .= " AND bu.user_id = ".$user_id;
        }
        //使用时间（开始）
        if (isset($param['used_time_start']) && !empty($param['used_time_start'])) {
            $condition .= " AND bu.used_time >= ".$param['used_time_start'];
        }
        //使用时间（结束）
        if (isset($param['used_time_end']) && !empty($param['used_time_end'])) {
            $condition .= " AND bu.used_time <= ".$param['used_time_end'];
        }
        //使用情况：0-全部；1-未使用；2-已使用；3-已过期
        if (isset($param['use_status']) && !empty($param['use_status'])) {
            switch (intval($param['use_status'])) {
                case 1:
                    $condition .= " AND bu.used_time=0 AND bt.use_start_time<=" . time();
                    $condition .= " AND ((bt.use_end_time_type=1 AND bt.use_end_time>" . time() . ") OR (bt.use_end_time_type=2 AND bu.drawed_time+bt.use_end_day*86400>" . time() . "))";
                    break;
                case 2:
                    $condition .= " AND bu.used_time>0";
                    break;
                case 3:
                    $condition .= " AND bu.used_time=0";
                    $condition .= " AND ((bt.use_end_time_type=1 AND bt.use_end_time<=" . time() . ") OR (bt.use_end_time_type=2 AND bu.drawed_time+bt.use_end_day*86400<=" . time() . "))";
                    break;
                default:
            }
        }
        //规则启用：0-全部；1-启用；2-禁用
        if (isset($param['rule_effect']) && !empty($param['rule_effect'])) {
            switch (intval($param['rule_effect'])) {
                case 1:
                    $condition .= " AND br.is_effect=1";
                    break;
                case 2:
                    $condition .= " AND br.is_effect=0";
                    break;
                default:
            }
        }
        //规则删除：0-全部；1-未删除；2-已删除
        if (isset($param['rule_delete']) && !empty($param['rule_delete'])) {
            switch (intval($param['rule_effect'])) {
                case 1:
                    $condition .= " AND br.is_delete=0";
                    break;
                case 2:
                    $condition .= " AND br.is_delete=1";
                    break;
                default:
            }
        }
        //领取方式：-1-全部；0-系统派发；1-手动发放
        if (isset($param['issue_type']) && intval($param['issue_type']) > 0) {
            if ($param['issue_type'] == 1) {
                $condition .= " AND bu.issue_type=1";
            } else{
                $condition .= " AND bu.issue_type=0";
            }
        }
        //待投标页面可使用的优惠券列表【attention】
        if (isset($param['get_type']) && intval($param['get_type']) == 1) {
            if ($param['deal_id'] > 0 && $param['deal_month'] > 0) { //购买普通标
                $condition .= " AND FIND_IN_SET('" . $param['deal_month'] . "',br.use_deal_month)";
            } elseif ($param['deal_load_id'] > 0 && $param['deal_load_month'] > 0) { //购买债权标
                $condition .= " AND br.use_deal_load=1 AND FIND_IN_SET('" . $param['deal_load_month'] . "',br.use_deal_month)";
            }
        }

        $sql = "SELECT 
                    bu.*,
                    br.money,
                    br.limit_amount,
                    bt.use_type,
                    bt.bonus_type_name,
                    bt.use_start_time,
                    CASE WHEN bt.use_end_time_type=1 THEN bt.use_end_time ELSE (bt.use_end_day*86400 + bu.drawed_time) END AS used_end_time,
                    br.use_deal_month,
                    br.use_deal_load 
                FROM _tablePrefix_bonus_user bu INNER JOIN _tablePrefix_bonus_rule br ON bu.bonus_rule_id=br.id 
                INNER JOIN _tablePrefix_bonus_type bt ON bu.bonus_type_id=bt.id " . $condition;
        $sql .= " ORDER BY bu.id DESC";

        return \Core::business('common')->getPageList($page, $pagesize, $sql);
	}
}