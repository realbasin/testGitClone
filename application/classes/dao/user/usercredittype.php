<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_usercredittype extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'type_name'//类型名称
				,'type'//审核类型
				,'icon'//图标
				,'brief'//简介
				,'brief'//简介
				,'description'//认证说明
				,'role'//认证条件
				,'file_tip'//上传框说明
				,'file_count'//file_count
				,'expire'//过期时间
				,'status'//0系统，1管理员新加
				,'is_effect'//0无效，1有效
				,'sort'//排序
				,'point'//信用积分
				,'must'//是否必须
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_credit_type';
	}
	
	public function getCreditType() {
		$credit_type = \Core::cache()->get('credit_type');
		if(!$credit_type) {
			$temp_credit_type = $this->getDb()->select('*')->from($this->getTable())->where(array('is_effect' => 1))->orderBy('sort', 'asc')->execute()->rows();
			$credit_type = array();
			foreach ($temp_credit_type as $k => $v) {
				$credit_type['list'][$v['type']] = $v;
				$credit_type['type'][] = $v['type'];
			}
			if($credit_type){
				\Core::cache()->set('credit_type',$credit_type);
			}
		}
		return $credit_type;
	}

}
