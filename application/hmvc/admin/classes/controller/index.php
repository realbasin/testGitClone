<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_index extends controller_sysBase {

	public function before($method, $args) {

	}

	public function do_index() {
		\Core::view() -> load('index', array('admininfo' => $this -> admininfo));
	}

	//获取主导航
	public function do_get_nav() {
		$route = \Base::getConfig() -> getRoute();
		$controller = $route -> getControllerShort();
		$method = $route -> getMethodShort();
		if ($this -> admininfo['super'] != 1 && empty($this -> permission)) {
			$permission = \Core::dao('sys_admin_auth') -> getBygid($this -> admininfo['gid']) -> getPermission();
			$permission = \Core::decrypt($permission, '', $this -> admininfo['name']);
			$this -> permission = $permission = explode('|', $permission);
		}
		$lang = \Language::getLangContent();
		$array =
		require_once (BASE_PATH . DIRECTORY_SEPARATOR . 'menu.php');
		//获取自定义部分
		$admin = $this -> admininfo;
		$link = unserialize($admin['link']);
		if ($link) {
			array_unshift($link, array('link' => 'dashboard,index', 'text' => $lang['dashboard']));
		} else {
			$link = $array[0]['list'];
		}
		$array[0]['list'] = $link;
		$array = $this -> parseMenu($array);
		$str = '';
		$this -> parseMenuString($array, $str);
		echo $str;
	}

	private function parseMenuString($menu = array(), &$str) {
		foreach ($menu as $k => $v) {
			$str .= '<div class="list-group">';
			$str .= '<h1 title="' . $v['text'] . '"><img src="' . RS_PATH . 'admin/images/nav/' . $v['img'] . '.png" /></h1>';
			$str .= '<div class="list-wrap">';
			$str .= '<h2>' . $v['subtext'] . '<i></i></h2>';
			$str .= '<ul>';
			foreach ($v['list'] as $xk => $xv) {
				$tmp = explode(',', $xv['link']);
				if (count($tmp) == 1) {
					$tmp[1] = '';
				}
				$str .= '<li>';
				if ($tmp) {
					$str .= '<a navid="' . $tmp[0] . '_' . $tmp[1] . '" href="' . \Core::getUrl($tmp[0], $tmp[1], \Core::config() -> getAdminModule()) . '" target="mainframe">';
				} else {
					$str .= '<a  target="mainframe">';
				}
				$str .= '<span>' . $xv['text'] . '</span>';
				$str .= '</a>';
				if (array_key_exists('sub', $xv)) {
					$submenu = $xv['sub'];
					if ($submenu) {
						$this -> parseMenuSub($submenu, $str);
					}
				}
				$str .= '</li>';
			}
			$str .= '</ul>';
			$str .= '</div>';
			$str .= '</div>';
		}
	}

	private function parseMenuSub($menu = array(), &$str) {
		$str .= '<ul>';
		foreach ($menu as $k => $v) {
			$tmp = explode(',', $v['link']);
			if (count($tmp) == 1) {
				$tmp[1] = '';
			}
			$str .= '<li>';
			if ($tmp) {
				$str .= '<a navid="' . $tmp[0] . '_' . $tmp[1] . '" href="' . \Core::getUrl($tmp[0], $tmp[1], \Core::config() -> getAdminModule()) . '" target="mainframe">';
			} else {
				$str .= '<a  target="mainframe">';
			}
			$str .= '<span>' . $v['text'] . '</span>';
			$str .= '</a>';
			if (array_key_exists('sub', $v)) {
				$submenu = $v['sub'];
				if ($submenu) {
					$this -> parseMenuSub($menu, $str);
				}
			}
			$str .= '</li>';
		}
		$str .= '</ul>';
	}

	private function parseMenu($menu = array()) {
		if ($this -> admininfo['super'] == 1)
			return $menu;
		foreach ($menu as $k => $v) {
			foreach ($v['list'] as $xk => $xv) {
				$tmp = explode(',', $xv['link']);
				if (count($tmp) == 1) {
					$tmp[1] = '';
				}
				$nocheck = array('index', 'dashboard', 'login', 'common');
				if (in_array($tmp[0], $nocheck))
					continue;
				if (!in_array($tmp[0] . '&' . $tmp[1], $this -> permission)) {
					unset($menu[$k]['list'][$xk]);
				}
			}
			if (empty($menu[$k]['list'])) {
				unset($menu[$k]);
			}
		}
		return $menu;
	}

}
