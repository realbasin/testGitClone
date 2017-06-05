<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
define('FRAME_PATH', str_replace('\\', '/', dirname(__FILE__)));

class Base {
	private static $appConfig;
	/**
	 * 初始化框架配置
	 */
	public static function initialize() {
		//加载框架支持文件
		\Base::loadFrames(array("exception","cliargs","core","interface","controller","model","bean","business","dao","session","view","route","router","appconfig","xspdo","db","language"));
		//初始化配置文件
		self::$appConfig = new \AppConfig();
		//注册类自动加载
		if (function_exists('__autoload')) {
			spl_autoload_register('__autoload');
		}
		spl_autoload_register(array('Base', 'classAutoloader'));
		//清理魔法转义
		if (get_magic_quotes_gpc()) {
			$stripList = array('_GET', '_POST', '_COOKIE');
			foreach ($stripList as $val) {
				global $$val;
				$$val = \Core::stripSlashes($$val);
			}
		}
		return self::$appConfig;
	}

	/**
	 * 包类库自动加载器
	 * @param type $className
	 */
	public static function classAutoloader($className) {
		$config = self::$appConfig;
		$className = str_replace(array('\\', '_'), '/', $className);
		foreach (self::$appConfig->getPackages() as $path) {
			if (file_exists($filePath = $path . $config -> getClassesDirName() . '/' . $className . '.php')) {
				\Core::includeOnce($filePath);
				break;
			}
		}
	}

	public static function loadFrame($fileName) {
		$filePath = FRAME_PATH . '/' . $fileName . '.php';
		include_once ($filePath);
	}

	public static function loadFrames($fileNames) {
		if (!is_array($fileNames) || empty($fileNames))
			return;
		foreach ($fileNames as $fileName) {
			\Base::loadFrame($fileName);
		}
	}

	/**
	 * 获取运行配置
	 */
	public static function & getConfig() {
		return self::$appConfig;
	}

	/**
	 * 运行调度
	 */
	public static function run() {
		if (\Core::isPluginMode()) {
			self::runPlugin();//如果是插件模式不执行路由
		} elseif (\Core::isCli()) {
			self::runCli();
		} else {
			$canRunWeb = !\Core::config() -> getIsMaintainMode();
			if (!$canRunWeb) {
				foreach (\Core::config()->getMaintainIpWhitelist() as $ip) {
					$info = explode('/', $ip);
					$netmask = empty($info[1]) ? '32' : $info[1];
					if (\Core::ipInfo(\Core::clientIp() . '/' . $netmask, 'netaddress') == \Core::ipInfo($info[0] . '/' . $netmask, 'netaddress')) {
						$canRunWeb = true;
						break;
					}
				}
			}
			if ($canRunWeb) {
				self::runWeb();
			} else {
				$handle = \Core::config() -> getMaintainModeHandle();
				if (is_object($handle)) {
					$handle -> handle();
				}
			}
		}
	}

private static function initSession() {
		$config = self::getConfig();
		//session初始化
		$sessionConfig = $config->getSessionConfig();
		@ini_set('session.auto_start', 0);
		@ini_set('session.gc_probability', 1);
		@ini_set('session.gc_divisor', 100);
		@ini_set('session.gc_maxlifetime', $sessionConfig['lifetime']);
		@ini_set('session.referer_check', '');
		@ini_set('session.entropy_file', '/dev/urandom');
		@ini_set('session.entropy_length', 16);
		@ini_set('session.use_cookies', 1);
		@ini_set('session.use_only_cookies', 1);
		@ini_set('session.use_trans_sid', 0);
		@ini_set('session.hash_function', 1);
		@ini_set('session.hash_bits_per_character', 5);
		session_cache_limiter('nocache');
		session_set_cookie_params(
			$sessionConfig['lifetime'], $sessionConfig['cookie_path'], preg_match('/^[^\\.]+$/', \Core::server('HTTP_HOST')) ? null : $sessionConfig['cookie_domain']
		);
		if (!empty($sessionConfig['session_save_path'])) {
			session_save_path($sessionConfig['session_save_path']);
		}
		session_name($sessionConfig['session_name']);
		register_shutdown_function('session_write_close');
		//session托管检测
		$sessionHandle = $config->getSessionHandle();
		if ($sessionHandle && $sessionHandle instanceof Xs_Session) {
			$sessionHandle->init();
		}
		if ($sessionConfig['autostart']) {
			\Core::sessionStart();
		}
		//session初始化完毕
	}

	/**
	 * web模式运行
	 * @throws Xs_Exception_404
	 */
	private static function runWeb() {
		$config = self::getConfig();
		$class = '';
		$method = '';
		foreach ($config->getRouters() as $router) {
			$route = $router -> find($config -> getRequest());
			if ($route -> found()) {
				$config -> setRoute($route);
				$class = $route -> getController();
				$method = $route -> getMethod();
				break;
			}
		}
		if (empty($route)) {
			throw new \Xs_Exception_500('none router was found in configuration');
		}
		$_route = \Core::config() -> getRoute();
		//当前域名有绑定hmvc模块,需要处理hmvc模块
		if ($hmvcModuleName = \Core::config() -> getCurrentDomainHmvcModuleNname()) {
			if (\Base::checkHmvc($hmvcModuleName, false)) {
				$_route -> setHmvcModuleName($hmvcModuleName);
				$_route -> setFound(true);
			}
		}
		if (empty($class)) {
			$class = $config -> getControllerDirName() . '_' . $config -> getDefaultController();
			$_route -> setController($class);
		}
		if (empty($method)) {
			$method = $config -> getMethodPrefix() . $config -> getDefaultMethod();
			$_route -> setMethod($method);
		}
		$config -> setRoute($_route);
		if (!\Core::classIsExists($class)) {
			throw new \Xs_Exception_404('Controller [ ' . $class . ' ] not found');
		}
		//初始化session
		self::initSession();
		$controllerObject = new $class();
		if (!($controllerObject instanceof Controller)) {
			throw new \Xs_Exception_404('[ ' . $class . ' ] not a valid Controller');
		}
		//前置方法检查执行
		if (method_exists($controllerObject, 'before')) {
			$controllerObject -> before(str_replace($config -> getMethodPrefix(), '', $method), $route -> getArgs());
		}
		//方法检测
		if (!method_exists($controllerObject, $method)) {
			throw new \Xs_Exception_404('Method [ ' . $class . '->' . $method . '() ] not found');
		}
		//方法缓存检测
		$cacheClassName = preg_replace('/^' . \Core::config() -> getControllerDirName() . '_/', '', $class);
		$cacheMethodName = preg_replace('/^' . \Core::config() -> getMethodPrefix() . '/', '', $method);
		$methodKey = $cacheClassName . '::' . $cacheMethodName;
		$cacheMethodConfig = $config -> getMethodCacheConfig();
		if (!empty($cacheMethodConfig) && \Core::arrayKeyExists($methodKey, $cacheMethodConfig) && $cacheMethodConfig[$methodKey]['cache'] && ($cacheMethoKey = $cacheMethodConfig[$methodKey]['key']())) {
			if (!($contents = \Core::cache() -> get($cacheMethoKey))) {
				@ob_start();
				$response = call_user_func_array(array($controllerObject, $method), $route -> getArgs());
				$contents = @ob_get_contents();
				@ob_end_clean();
				$contents .= is_array($response) ? \Core::view() -> set($response) -> load("$cacheClassName/$cacheMethodName") : $response;
				\Core::cache() -> set($cacheMethoKey, $contents, $cacheMethodConfig[$methodKey]['time']);
			}
		} else {
			if (method_exists($controllerObject, 'after')) {
				//如果有后置方法，这里应该捕获输出然后传递给后置方法处理
				@ob_start();
				$response = call_user_func_array(array($controllerObject, $method), $route -> getArgs());
				$contents = @ob_get_contents();
				@ob_end_clean();
				$contents .= is_array($response) ? \Core::view() -> set($response) -> load("$cacheClassName/$cacheMethodName") : $response;
			} else {
				$response = call_user_func_array(array($controllerObject, $method), $route -> getArgs());
				$contents = is_array($response) ? \Core::view() -> set($response) -> load("$cacheClassName/$cacheMethodName") : $response;
			}
		}
		//后置方法检查执行
		if (method_exists($controllerObject, 'after')) {
			echo $controllerObject -> after(str_replace($config -> getMethodPrefix(), '', $method), $route -> getArgs(), $contents);
		} else {
			echo $contents;
		}
	}

	/**
	 * 命令行模式运行
	 */
	private static function runCli() {
		if(!class_exists("Task",FALSE)) self::loadFrames(array("task","generator"));
		$task = str_replace('/', '_', \Core::getOpt('task'));
		
		$hmvcModuleName = \Core::getOpt('hmvc');
		
		if (empty($task)) {
			exit('require a task name,please use --task=<taskname>' . "\n");
		}
		if(!\Core::strBeginsWith($task, 'task_')){
			$task='task_'.$task;
		}
		if (!empty($hmvcModuleName)) {
			self::checkHmvc($hmvcModuleName);
		}
		$taskName = $task;
		if (!class_exists($taskName)) {
			throw new \Xs_Exception_500('class [ ' . $taskName . ' ] not found');
		}
		$taskObject = new $taskName();
		if (!($taskObject instanceof Task)) {
			throw new \Xs_Exception_500('[ ' . $taskName . ' ] not a valid Task');
		}
		$args = \Core::getOpt();
		$args = empty($args) ? array() : $args;
		$taskObject -> _execute(new \CliArgs($args));
	}

	/**
	 * 插件模式运行
	 */
	private static function runPlugin() {
		//插件模式
	}

	/**
	 * 检测并加载hmvc模块,成功返回模块文件夹名称，失败返回false或抛出异常
	 * @staticvar array $loadedModules
	 * @param type $hmvcModuleName  hmvc模块在URI中的名称，即注册配置hmvc模块数组的键名称
	 * @throws Xs_Exception_404
	 */
	public static function checkHmvc($hmvcModuleName, $throwException = true) {
		//hmvc检测
		if (!empty($hmvcModuleName)) {
			$config = \Base::getConfig();
			$hmvcModules = $config -> getHmvcModules();
			if (empty($hmvcModules[$hmvcModuleName])) {
				if ($throwException) {
					throw new \Xs_Exception_500('Hmvc Module [ ' . $hmvcModuleName . ' ] not found, please check your config.');
				} else {
					return FALSE;
				}
			}
			//避免重复加载，提高性能
			static $loadedModules = array();
			$hmvcModuleDirName = $hmvcModules[$hmvcModuleName];
			if (!\Core::arrayKeyExists($hmvcModuleName, $loadedModules)) {
				$loadedModules[$hmvcModuleName] = 1;
				//找到hmvc模块,去除hmvc模块名称，得到真正的路径
				$hmvcModulePath = $config -> getApplicationDir() . $config -> getHmvcDirName() . '/' . $hmvcModuleDirName . '/';
				//设置hmvc子项目目录为主目录，同时注册hmvc子项目目录到主包容器，以保证高优先级
				$config -> setApplicationDir($hmvcModulePath) -> addMasterPackage($hmvcModulePath) -> bootstrap();
			}
			return $hmvcModuleDirName;
		}
		return FALSE;
	}

}
?>