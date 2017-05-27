<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userregplatverified extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//贷款ID
				,'user_id'//会员（标识ID）
				,'mobile'//会员手机号
				,'platform_id'//注册平台（标识ID）
				,'is_register'//0-未注册；1-已注册；2-未查询到结果
				,'is_success'//0-失败；1-成功
				,'verify_res'//验证失败返回内容
				,'create_time'//首次验证时间
				,'update_time'//最新验证时间
				,'is_delete'//0-正常；1-删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_register_platform_verified';
	}
	//根据贷款id和用户id获取验证过的平台
	public function getVerified($loan_id,$user_id,$fields="*") {
		$where = array();
		$where['deal_id'] = $loan_id;
		$where['user_id'] = $user_id;
		$where['is_delete'] = 0;
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->key('platform_id')->rows();
	}

}
