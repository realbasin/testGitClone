<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_wechat_menu extends controller_sysBase {

	public function before($method, $args) {
		\Language::read('wechat');
	}

	public function initMenu() {
		//从缓存中获取微信菜单
		$menu = \Core::cache() -> get('wechat_menu');
		//初始化微信SDK从微信获取menu
		if (!$menu) {
			$menuAPI = getWxLibrary('\Wechat\WechatMenu');
			//将获取到所有菜单的数据，包含：自定义菜单及个性化菜单
			$result = $menuAPI -> getMenu();
			// 处理结果
			if ($result === FALSE) {
				//如果errcode=0表示配置是完全错误的
				if ($menuAPI -> errCode == 0) {
					\Core::message(\Core::L('wechat_config_error'), null, 'tip', 0, 'message', false);
				}
				\Core::message($menuAPI -> errMsg, null, 'tip', 0, 'message');
			} else {
				// 接口成功的处理
				//将返回结果转换为array
				$json = json_decode($result, TRUE);
				//获取菜单
				$menu = array();
				if (\Core::arrayKeyExists('menu', $json)) {
					$menu = $json;
				}
				\Core::cache() -> set('wechat_menu', $menu);
			}
		}
	}

	public function do_index() {
		$this -> do_default();
	}

	public function do_default() {
		$this -> initMenu();
		$menu = \Core::cache() -> get('wechat_menu');
		if ($menu && \Core::arrayKeyExists('menu', $menu)) {
			\Core::view() -> set('menu', json_encode($menu['menu']));
		}
		\Core::view() -> load('wechat_menuDefault');
	}

	public function do_default_delete() {

	}

	public function do_default_save() {
		$group = \Core::post('group');
		if (!$group) {
			showJSON(0, \Core::L('parameter_error'));
		}
		//保存进缓存
		$menu = \Core::cache() -> get('wechat_menu');
		$menu['menu'] = $group;
		\Core::cache() -> set('wechat_menu', $menu);

		$menuAPI = getWxLibrary('\Wechat\WechatMenu');
		//删除菜单
		$result = $menuAPI -> deleteMenu();
		if ($result === FALSE) {
			if ($menuAPI -> errCode == 0) {
					\Core::message(\Core::L('wechat_config_error'), null, 'tip', 0, 'message', false);
				}
				\Core::message($menuAPI -> errMsg, null, 'tip', 0, 'message');
		}
		//创建菜单 先创建默认菜单 再创建个性菜单
		$menuAPI->createMenu($group);
	}

	public function do_conditional() {
		$this -> initMenu();
		$menu = \Core::cache() -> get('wechat_menu');
		if ($menu && \Core::arrayKeyExists('conditionalmenu', $menu)) {
			\Core::view() -> set('menu', json_encode($menu['conditionalmenu']));
		}
		\Core::view() -> load('wechat_menuConditional');
	}

	public function do_conditional_edit() {

	}

	public function do_conditional_add() {

	}

}
