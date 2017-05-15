<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/*
 * 视图模板类
 * */
class View {
	private static $vars = array();
	public function add($key, $value = array()) {
		if(!$key)return;
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				if (!\Core::arrayKeyExists($k, self::$vars)) {
					self::$vars[$k] = $v;
				}
			}
		} else {
			if (!\Core::arrayKeyExists($key, self::$vars)) {
				self::$vars[$key] = $value;
			}
		}
		return $this;
	}

	public function set($key, $value = array()) {
		if(!$key)return;
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				self::$vars[$k] = $v;
			}
		} else {
			self::$vars[$key] = $value;
		}
		return $this;
	}

	private function _load($path, $data = array(), $return = false) {
		if (!file_exists($path)) {
			throw new \Xs_Exception_500('view file : [ ' . $path . ' ] not found');
		}
		$data = array_merge(self::$vars, $data);
		if (!empty($data)) {
			extract($data);
		}
		if ($return) {
			@ob_start();
			include $path;
			$html = ob_get_contents();
			@ob_end_clean();
			return $html;
		} else {
			include $path;
			return;
		}
	}

	/**
	 * 加载一个视图<br/>
	 * @param string $viewName 视图名称
	 * @param array  $data     视图中可以使用的数据
	 * @param bool   $return   是否返回视图内容
	 * @return string
	 */
	public function load($viewName, $data = array(), $return = false) {
		$config = \Core::config();
		$viewName = str_replace('_', '/', $viewName);
		$path = $config -> getApplicationDir() . $config -> getViewsDirName() . '/' . $viewName . '.php';
		$hmvcModules = $config -> getHmvcModules();
		$hmvcDirName = \Core::arrayGet($hmvcModules, $config -> getRoute() -> getHmvcModuleName(), '');
		//当load方法在主项目的视图中被调用，然后hmvc主项目load了这个视图，那么这个视图里面的load应该使用的是主项目视图。
		//hmvc访问
		if ($hmvcDirName) {
			$hmvcPath = \Core::realPath($config -> getPrimaryApplicationDir() . $config -> getHmvcDirName() . '/' . $hmvcDirName);
			$trace = debug_backtrace();
			$calledIsInHmvc = false;
			$appPath = \Core::realPath($config -> getApplicationDir());
			foreach ($trace as $t) {
				$filepath = \Core::arrayGet($t, 'file', '');
				if (!empty($filepath)) {
					$filepath = \Core::realPath($filepath);
					$checkList = array('load', 'runWeb', 'message', 'redirect');
					$function = \Core::arrayGet($t, 'function', '');
					if ($filepath && in_array($function, $checkList) && strpos($filepath, $appPath) === 0 && strpos($filepath, $hmvcPath) === 0) {
						$calledIsInHmvc = true;
						break;
					} elseif (!in_array($function, $checkList)) {
						break;
					}
				}
			}
			//发现load是在主项目中被调用的，使用主项目视图
			if (!$calledIsInHmvc) {
				$path = $config -> getPrimaryApplicationDir() . $config -> getViewsDirName() . '/' . $viewName . '.php';
			}
		}
		return $this -> _load($path, $data, $return);
	}

	/**
	 * 加载主项目的视图<br/>
	 * 这个一般是在hmvc模块中使用到，用于复用主项目的视图文件，比如通用的header等。<br/>
	 * @param string $viewName 主项目视图名称
	 * @param array  $data     视图中可以使用的数据
	 * @param bool   $return   是否返回视图内容
	 * @return string
	 */
	public function loadParent($viewName, $data = array(), $return = false) {
		$config = \Core::config();
		$path = $config -> getPrimaryApplicationDir() . $config -> getViewsDirName() . '/' . $viewName . '.php';
		return $this -> _load($path, $data, $return);
	}

}
?>