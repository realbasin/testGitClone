<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
//业务员
class dao_user_agent extends Dao {

	public function getColumns() {
		return array(
				'agent_id'//自动递增ID
				,'agent_name'//业务员用户名
				,'agent_pwd'//登录密码
				,'is_effect'//是否启用
				,'is_delete'//是否删除
				,'last_login_time'//上次登录时间
				,'last_login_ip'//上次登录IP
				,'real_name'//真实姓名
				,'mobile'//手机号码
				,'admin_id'//归属管理员
				);
	}

	public function getPrimaryKey() {
		return 'agent_id';
	}

	public function getTable() {
		return 'agent';
	}
	
	//获取全部业务员列表
	public function getAgentList(){
		return $this->getDb()->select("agent_id,agent_name,real_name,mobile,admin_id")->from($this->getTable())->execute()->key('agent_id')->rows();
	}

}
