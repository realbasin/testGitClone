<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_userregplatform extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//平台名称
				,'home_url'//平台首页地址链接
				,'register_url'//平台注册页地址链接
				,'is_show'//0-不启用；1-启用
				,'config_param'//平台验证配置信息（{request_url,request_method,request_param,return_format,return_succ,return_fail}）
				,'create_time'//创建时间
				,'update_time'//修改时间
				,'is_delete'//0-正常；1-删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_register_platform';
	}
	//获取所有正常平台
	public function getPlatforms($fields='*'){
		return $this->getDb()->select($fields)->from($this->getTable())->where(array('is_delete'=>0))->execute()->key('id')->rows();
	}
}
