<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_regionpartner extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'name'//姓名
        , 'user_name'//账户名
        , 'user_pwd'//会员密码
        , 'login_ip'//最后登录IP
        , 'login_time'//最后登录时间
        , 'create_time'//建立时间
        , 'is_effect'//是否启用
        , 'balance'//期初保证金额
        , 'guarantees_max'//最大保证金额
        , 'guarantees_min'//最小保证金额
        , 'guarantees_rate'//保证金费率
        , 'guarantees_amount'//保证金总额
        , 'service_A1_rate'//A1类服务费提成率
        , 'manage_A1_rate'//A1类管理费提成率
        , 'guarantees_A1_rate'//A1类逾期未还扣保证金费率
        , 'service_A2_rate'//A2类服务费提成率
        , 'manage_A2_rate'//A2类管理费提成率
        , 'guarantees_A2_rate'//A2类逾期未还扣保证金费率
        , 'service_A3_rate'//A3类类服务费提成率
        , 'manage_A3_rate'//A3类管理费提成率
        , 'guarantees_A3_rate'//A3类逾期未还扣保证金费率
        , 'service_B_rate'//B类类服务费提成率
        , 'manage_B_rate'//B类管理费提成率
        , 'guarantees_B_rate'//B类逾期未还扣保证金费率
        , 'expired_repay_openinfo_days'//逾期N天后借款信息公开
        , 'expired_repay_debit_days'//逾期N天数后冻结
        , 'is_close'//是否关闭(系统)
        , 'effect_date'//生效日期
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'region_partner';
    }

    public function getEffectAllRegionPartner()
    {
        $where = array('is_effect' => 1);
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where($where)
            ->execute()
            ->rows();
        return $data;
    }

    public function getRegionPartner($partner_id)
    {
        $where = array('id' => $partner_id);
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where($where)
            ->execute()
            ->row();
        return $data;
    }

}
