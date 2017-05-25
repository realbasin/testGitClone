<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 通用调用控制器
 * 无需管理员权限
 */
class  controller_common extends controller_sysBase {
	public function do_index() {

	}

	//用户查询自动补全
	public function do_autoGetUsers() {
		$userName = \Core::getPost('q');
		$userBusiness = \Core::business('user_userinfo');
		$users = $userBusiness -> getUsersByName($userName);
		echo @json_encode($users);
	}

}
