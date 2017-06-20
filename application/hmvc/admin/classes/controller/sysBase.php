<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * 后台基类
 * 后台功能均要继承此类
 */
class  controller_sysBase extends Controller {
	protected $admininfo;
	protected $permission;

	public function __construct() {
		\Language::read('common,layout,flexigrid');
		$this -> admininfo = $this -> systemLogin();
		if ($this -> admininfo['super'] != 1) {
			if (!$this -> checkPermission()) {
				//显示权限错误信息
				\Core::message(\Language::get('permission_error'), null, 'error', 0, 'message');
				exit();
			}
		}
	}

	protected final function systemLogin() {
		$cookie_admin = \Core::cookie('admin');
		$admin = unserialize(\Core::decrypt($cookie_admin));
		if (empty($admin['id']) || empty($admin['name'])) {
			echo "<script>top.location.href='" . \Core::getUrl('login', '', \Core::config() -> getAdminModule()) . "'</script>";
			exit ;
		} else {
			//加60分钟过期时间
			\Core::setCookie('admin', $cookie_admin, 3600);
		}
		if ($admin['super'] == 1) {
			$admin['gname'] = \Core::L('super');
		}
		return $admin;
	}

	protected final function hasPermission($permission) {
		if ($this -> admininfo['super'] == 1) {
			return true;
		}
		if (in_array($permission, $this -> permission)) {
			return true;
		}
		return false;
	}

	protected final function checkPermission() {
		if ($this -> admininfo['super'] == 1) {
			return true;
		}
		if (empty($this -> permission)) {
			$auth = \Core::dao('sys_admin_auth') -> getBygid($this -> admininfo['gid']) -> getPermission();
			$this -> admininfo['gname'] = $auth -> getGname();
			$permission = \Core::decrypt($auth -> getPermission());
			$this -> permission = $permission = explode('|', $permission);
		}
		$route = \Base::getConfig() -> getRoute();
		$controller = $route -> getControllerShort();
		$method = $route -> getMethodShort();

		$nocheck = array("login", "index", "dashboard", "common");
		if (in_array($controller, $nocheck))
			return true;
		if (in_array("{$controller}&{$method}", $permission)) {
			return true;
		} else {
			//通过相关后缀方法
			foreach ($permission as $v) {
				if (!empty($v) && strpos("{$controller}&{$method}_", $v) !== false) {
					return true;
					break;
				}
			}
		}
		return false;
	}

	protected final function log($lang = '', $type = '', $admin_name = '', $admin_id = 0) {
		if (!C('sys_log') || !is_string($lang))
			return;
		if ($admin_name == '') {
			$admin = $this -> admininfo;
			$admin_name = $admin['name'];
			$admin_id = $admin['id'];
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
	
	protected final function createTaps($links = array(), $actived = '') {
		$linkstr = '';
		foreach ($links as $k => $v) {
			if (!$this -> checkPermission($v['ctl'] . '&' . $v['act']))
				continue;
			$href = ($v['act'] == $actived ? null : 'href="' . \Core::getUrl($v['ctl'], $v['act'], \Core::config() -> getAdminModule()) . '"');
			$class = ($v['act'] == $actived ? "class=\"current\"" : null);
			$lang = (\Core::arrayKeyExists('text', $v) ? $v['text'] : \Core::L($v['lang']));
			$linkstr .= sprintf('<li><a %s %s><span>%s</span></a></li>', $href, $class, $lang);
		}
		return array("pagetabs" => "<ul class=\"tab-base dz-row\">{$linkstr}</ul>");
	}

}
