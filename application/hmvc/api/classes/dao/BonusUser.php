<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_BonusUser extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'bonus_sn'//优惠券号
        , 'bonus_type_id'//优惠券类型id
        , 'bonus_rule_id'//优惠券id
        , 'user_id'//领取优惠券的用户id，如果为0，未有人领取
        , 'user_name'//领取优惠券的用户名
        , 'drawed_time'//领取优惠券时间
        , 'used_time'//优惠券使用时间
        , 'module'//使用优惠券的模型（deal,deal_load,deal_transfor)
        , 'module_pk_Id'//模型表的id
        , 'create_time'//记录创建时间
        , 'issue_type'//领取方式：0-派发；1-手动发放
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'bonus_user';
    }

    /**
     * 获取用户红包列表
     * @param $seven_day
     * @param $fifteen_day
     * @return array
     */
    public function getBonusList($seven_day, $fifteen_day)
    {
        $sql = "SELECT bu.user_id FROM _tablePrefix_bonus_user bu INNER JOIN _tablePrefix_bonus_rule br ON bu.bonus_rule_id=br.id INNER JOIN _tablePrefix_bonus_type bt ON bu.bonus_type_id=bt.id 
         WHERE bu.used_time=0  AND ((bt.use_end_time_type=1 AND FROM_UNIXTIME(bt.use_end_time,'%Y-%m-%d') IN ('" . $seven_day . "','" . $fifteen_day . "')) OR (bt.use_end_time_type=2 AND FROM_UNIXTIME((bu.drawed_time+bt.use_end_day*86400),'%Y-%m-%d') IN ('" . $seven_day . "','" . $fifteen_day . "'))) AND bt.use_type=1
         GROUP BY bu.user_id";
        $data = $this->getDb()->execute($sql)->rows();
        return $data;
    }

}
