<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_vipsetting extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'vip_id'//VIP等级
				,'probability'//收益奖励几率
				,'load_mfee'//借款管理费(每月)
				,'interest'//投资利息费
				,'charges'//提现手续费(每笔)
				,'coefficient'//积分折现系数
				,'multiple'//积分获取倍数
				,'bgift'//生日礼品
				,'btype'//生日礼品类别 1积分 2现金红包
				,'holiday_score'//节日积分
				,'is_delete'//删除标识
				,'is_effect'//有效性标识
				,'sort'//VIP配置排序
				,'original_price'//vip 原价（原先购买价格）
				,'site_pirce'//vip 现价 (现有购买价格)
				,'rate'//增加的收益率
				,'integral'//收益积分值
				,'red_envelope'//多种类型红包金额
				,'gift'//多种礼品ID
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'vip_setting';
	}

}
