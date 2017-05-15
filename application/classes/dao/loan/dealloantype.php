<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealloantype extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//借款用途
				,'brief'//简介
				,'pid'//父类ID
				,'is_delete'//是否删除
				,'is_effect'//是否有效
				,'is_display'//是否显示 0否 1是
				,'is_autobid'//是否启用自动投标 0否 1是
				,'is_use_ecv'//是否可使用借款红包 0否 1是
				,'is_use_bonus'//是否可使用理财优惠券 0否 1是
				,'is_referral_award'//是否纳入推荐奖励 0否 1是
				,'is_pay_off_limit'//是否开启未还清限制：0-关闭；1-开启
				,'sort'//sort
				,'uname'//uname
				,'icon'//分类icon
				,'applyto'//适用人群
				,'condition'//申请条件
				,'credits'//必要申请资料
				,'usetypes'//借款用途ID
				,'collaterals'//抵押物:1-住房；2-汽车
				,'types'//类型：0-学生贷; 1-信用贷; 2-抵押贷
				,'is_quota'//额度限制  0否 1是
				,'content'//类型简介
				,'is_extend_effect'//是否启用扩展配置
				,'is_user_level_effect'//是否启用扩展信用等级配置
				,'identity_auth'//身份认证
				,'education_auth'//教育认证
				,'relation_info'//联系信息
				,'work_info'//工作信息`
				,'tongdun_limit_score'//同盾自动拒单分数设定
				,'tongdun_limit_minage'//同盾自动拒单最小年龄设定
				,'tongdun_limit_maxage'//同盾自动拒单最大年龄设定
				,'tongdun_limit_city'//同盾自动拒单高风险城市
				,'tongdun_limit_province'//同盾自动拒单高风险省份
				,'xuex_chk_status'//学信网限制:学信网状态未勾选的将被自动拒绝(0,未验证,1,正确,2,错误)
				,'tongdun_three_month_idno_relevance'//3个月内身份证关联多个申请信息
				,'tongdun_seven_day_apply_num'//7天内申请人在多个平台申请借款
				,'tongdun_one_month_apply_num'//1个月内申请人在多个平台申请借款
				,'tongdun_three_month_apply_num'//3个月内申请人在多个平台申请借款
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_loan_type';
	}
	
	public function getDealLoanTypes($field='*',$where=array()){
		return $this->getDb()->select($field)->from($this->getTable())->where($where)->execute()->rows();
	}

}
