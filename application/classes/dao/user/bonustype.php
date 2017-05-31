<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_bonustype extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'bonus_type_name'//优惠券类型名称
				,'use_type'//1是理财端，2.借款端
				,'is_limited'//是否限定使用一次
				,'start_time'//发放时间，如果为0，现在开始
				,'end_time'//发放的结束时间
				,'use_start_time'//优惠券开始使用时间
				,'use_end_time'//优惠券结束使用时间
				,'use_end_day'//优惠券激活后有效期，单位为天
				,'use_end_time_type'//优惠券结束使用时间类型，1.设定固定日期 2.激活后有效期
				,'send_type'//发放方式，1.注册发放，2.手动发放，3.用户领取，4.积分兑换
				,'num'//发放优惠券总数量
				,'amount'//优惠券优惠总额
				,'used_num'//使用优惠券数量
				,'userd_amount'//使用优惠券的投资总额
				,'is_effect'//是否可用
				,'create_time'//记录创建时间
				,'update_time'//记录更新时间
				,'is_delete'//标记删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'bonus_type';
	}

    /**
     * 获取某个优惠券类型
     * @param $id
     * @return array|mixed
     */
	public function getBonusTypeById($id) {
	    return $this->getDb()->select('*')->from($this->getTable())->where(array('id'=>$id))->execute()->row();
    }
}
