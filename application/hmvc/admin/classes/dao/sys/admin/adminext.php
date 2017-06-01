<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_admin_adminext extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'adm_name'//管理员用户名
				,'adm_password'//管理员密码
				,'is_effect'//有效性控制
				,'is_delete'//删除标识
				,'role_id'//角色ID(权限控制用)
				,'login_time'//最后登录时间
				,'login_ip'//最后登录IP
				,'is_department'//用户类型   0：管理员，1：部门
				,'pid'//所属部门编号
				,'work_id'//员工编号
				,'referrals_rate'//提成系数
				,'referrals_count'//部门成员人数
				,'referrals_money'//提成金额
				,'real_name'//real_name
				,'mobile'//mobile
				,'openid'//用户的标识，对当前公众号唯一
				,'salesman_id'//关联业务员ID
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'admin';
	}
	//根据id获取管理员
	public function getAdminById($id,$field){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('id'=>$id))->execute()->key('id')->row();
	}
}
