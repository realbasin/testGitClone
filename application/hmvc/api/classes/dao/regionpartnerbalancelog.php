<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_regionpartnerbalancelog extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'partner_id'//合伙人ID
				,'unpaid_amount'//待缴保证金额
				,'paid_amount'//已缴保证金额
				,'unpaid_admin_id'//催缴管理员ID
				,'paid_admin_id'//已缴管理员ID
				,'unpaid_date'//应缴日期
				,'paid_date'//已缴日期
				,'unpaid_login_ip'//催缴登录IP
				,'paid_login_ip'//已缴登录IP
				,'create_time'//建立时间
				,'paid_time'//已缴时间
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'region_partner_balance_log';
	}

    public function getAllByPartnerId($partner_id)
    {
        if ($partner_id < 1)
            return null;

        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where(['partner_id'=>$partner_id])
            ->orderBy('id','desc')
            ->execute()
            ->row();
        return $data;
    }

}
