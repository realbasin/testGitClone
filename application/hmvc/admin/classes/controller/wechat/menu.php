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
				\Core::cache() -> set('wechat_menu', $json);
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
			\Core::view() -> set('wechat_menu', json_encode($menu['menu']));
		}
		\Core::view() -> load('wechat_menuDefault');
	}

	public function do_default_delete() {
		$menuAPI = getWxLibrary('\Wechat\WechatMenu');
		$result = $menuAPI -> deleteMenu();
		// 处理创建结果
		if ($result === FALSE) {
			if ($menuAPI -> errCode == 0) {
				\Core::message(\Core::L('wechat_config_error'), null, 'tip', 0, 'message', false);
			}
			\Core::message($menuAPI -> errMsg, null, 'tip', 0, 'message');
		} else {
			\Core::cache() -> delete('wechat_menu');
			$this -> log(\Core::L('delete,wechat_menu'), 'delete');
			\Core::message(\Core::L('delete,wechat_menu,success'), adminUrl('wechat_menu', 'default'), 'suc', 3, 'message');
		}
	}

	public function do_default_save() {
		$group = \Core::postRawBody();
		if (!$group) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$group = json_decode($group, true);
		//保存进缓存
		$menu = \Core::cache() -> get('wechat_menu');
		$menu['menu'] = array('button' => $group['group']['button']);
		\Core::cache() -> set('wechat_menu', $menu);
		$menuAPI = getWxLibrary('\Wechat\WechatMenu');
		//创建自定义菜单
		$result = $menuAPI -> createMenu($menu['menu']);
		if ($result === FALSE) {
			// 接口失败的处理
			if ($menuAPI -> errCode == 0) {
				showJSON(0, \Core::L('wechat_config_error'));
			}
			showJSON(10, $menuAPI -> errMsg);
		} else {
			$this -> log(\Core::L('update,wechat_menu'), 'update');
			showJSON(200, \Core::L('create,wechat_menu_default,sucess'));
		}
	}

	//列表
	public function do_conditional() {
		$this -> initMenu();
		$menu = \Core::cache() -> get('wechat_menu');

		if ($menu && \Core::arrayKeyExists('conditionalmenu', $menu)) {
			\Core::view() -> set('datalist', $menu['conditionalmenu']);
		} else {
			\Core::view() -> set('datalist', array());
		}
		\Core::view() -> load('wechat_menuCond');
	}

	public function do_conditional_add() {
		\Core::view() -> load('wechat_menuCondAdd');
	}

	public function do_conditional_save() {
		$group = \Core::postRawBody();
		if (!$group) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$group = json_decode($group, true);
		$menuAPI = getWxLibrary('\Wechat\WechatMenu');
		//创建个性菜单
		$result = $menuAPI -> createCondMenu($group['group']);
		if ($result === FALSE) {
			// 接口失败的处理
			if ($menuAPI -> errCode == 0) {
				showJSON(0, \Core::L('wechat_config_error'));
			}
			showJSON(10, $menuAPI -> errMsg);
		} else {
			//保存进缓存
			$menu = \Core::cache() -> get('wechat_menu');
			$group['group']['menuid'] = $result;
			$menu['conditionalmenu'][] = $group['group'];
			\Core::cache() -> set('wechat_menu', $menu);
			$this -> log(\Core::L('update,wechat_menu'), 'update');
			showJSON(200, \Core::L('create,wechat_menu_default,sucess'));
		}
	}

	public function do_conditional_del() {
		$menuid = \Core::get('menuid');
		if (!$menuid) {
			\Core::message(\Core::L('parameter_error'), adminUrl('wechat_menu', 'conditional'), 'tip', 3, 'message');
		}
		$menuAPI = getWxLibrary('\Wechat\WechatMenu');
		$result = $menuAPI -> deleteCondMenu($menuid);
		if ($result === FALSE) {
			if ($menuAPI -> errCode == 0) {
				\Core::message(\Core::L('wechat_config_error'), null, 'tip', 0, 'message', false);
			}
			\Core::message($menuAPI -> errMsg, adminUrl('wechat_menu', 'conditional'), 'tip', 3, 'message');
		} else {
			// 缓存处理
			$menu = \Core::cache() -> get('wechat_menu');
			$condmenu=$menu['conditionalmenu'];
			$condmenunew=array();
			foreach($condmenu as $v){
				if(\Core::arrayKeyExists('menuid', $v) && $v['menuid']!=$menuid){
					$condmenunew[]=$v;
				}
			}
            $menu['conditionalmenu']=$condmenunew;
			\Core::cache() -> set('wechat_menu', $menu);
			$this -> log(\Core::L('delete,wechat_menu_conditional'), 'delete');
			\Core::message(\Core::L('delete,wechat_menu_conditional,success'), adminUrl('wechat_menu', 'conditional'), 'suc', 3, 'message');
		}
	}

}
