<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_login extends Controller {

	public function before($method, $args) {
		\Language::read('common,login');
	}

	public function do_index() {
		if (chksubmit()) {
			$name = \Core::post('txtUserName', null, TRUE);
			$pwd = \Core::post('txtPassword', null, TRUE);
			$code = \Core::post('txtVerify', null, TRUE);
			if (!$name || !$pwd) {
				\Core::message(\Core::L("input_user_pwd"), '', 'tip', 3, 'message');
			}
			if (strtoupper($code) != strtoupper(\Core::session('captcha_code'))) {
				\Core::message(\Core::L("error_captcha"), '', 'tip', 3, 'message');
			}

			$admin = \Core::dao('sys_admin_admin');
			$row = $admin -> find(array('admin_name' => $name, 'admin_password' => md5($pwd)));

			if ($row) {
				//更新最后登录时间和登录次数
				$admin->updateLastLogin($row['admin_id']);
				//写入COOKIE
				$admin = array();
				$admin['id'] = $row['admin_id'];
				$admin['name'] = $row['admin_name'];
				$admin['avatar'] = $row['admin_avatar'];
				$admin['super'] = $row['admin_is_super'];
				$admin['gid'] = $row['admin_gid'];
				$admin['link'] = $row['admin_quick_link'];
				$admin['gname'] = '';
				$adminstr = \Core::encrypt(serialize($admin));
				\Core::setCookie('admin', $adminstr);
				//记录日志
				$this->adminLog(\Core::L('admin_login'), $admin['name'], $admin['id'], 'login');
				//跳转到主页面
				\Core::redirect(\Core::getUrl('index', '', \Core::config()->getAdminModule()));
			}
			\Core::message(\Core::L("error_user_pwd"), '', 'tip', 3, 'message');
		}
		\Core::view() -> load('login');
	}

	public function do_logout() {
		\Core::setCookie('admin', null);
		\Core::message(\Core::L("logout_tip"), \Core::getUrl('login', '', \Core::config()->getAdminModule()), 'tip', 3, 'message');
	}

	function adminLog($lang = '', $admin_name = '', $admin_id = 0, $type = '') {
		if (!C('sys_log') || !is_string($lang))
			return;
		if ($admin_name == '') {
			return;
		}

		$route = \Base::getConfig() -> getRoute();
		$controller = $route -> getControllerShort();
		$method = $route -> getMethodShort();

		$data = array();
		$data['content'] = $lang;
		$data['admin_name'] = $admin_name;
		$data['operatetime'] = time();
		$data['operatetype'] = $type;
		$data['admin_id'] = $admin_id;
		$data['ip'] = \Core::clientIp();
		$data['link'] = $controller . '&' . $method;
		return \Core::dao('sys_admin_log') -> insert($data);
	}

}
