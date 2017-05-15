<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
class AppConfig {
	private $applicationDir = '', //项目目录
	$primaryApplicationDir = '', //主项目目录
	$indexDir = '', //入口文件目录
	$indexName = '', //入口文件名称
	$gzip = FALSE, //是否启用GZIP压缩
	$showErrNotice=FALSE,//错误是否显示notice信息
	$adminModule='admin',//管理模块
	$classesDirName = 'classes', $hmvcDirName = 'hmvc', $libraryDirName = 'library', $functionsDirName = 'functions', $languageDirName = 'language', $languageTypeDirName = 'zh_cn', $languageCharset = 'UTF-8', $storageDirPath = '', $viewsDirName = 'views', $configDirName = 'config', $controllerDirName = 'controller', $businessDirName = 'business', $daoDirName = 'dao', $beanDirName = 'bean', $modelDirName = 'model', $taskDirName = 'task', $defaultController = 'Index', $defaultMethod = 'index', $methodPrefix = 'do_', $methodUriSubfix = '.do', $routerUrlModuleKey = 'm', $routerUrlControllerKey = 'c', $routerUrlMethodKey = 'a', $methodParametersDelimiter = '-', $logsSubDirNameFormat = 'Y-m-d/H', $cookiePrefix = '', $backendServerIpWhitelist = array(), $isRewrite = FALSE, $request, $showError = true, $routersContainer = array(), $packageMasterContainer = array(), $packageContainer = array(), $loggerWriterContainer = array(), $uriRewriter, $exceptionHandle, $route, $environment = 'development', $hmvcModules = array(), $isMaintainMode = false, $maintainIpWhitelist = array(), $maintainModeHandle, $databseConfig, $cacheHandles = array(), $cacheConfig, $sessionConfig, $sessionHandle, $methodCacheConfig, $dataCheckRules, $outputJsonRender, $exceptionJsonRender, $xzMethods = array(), $encryptKey=array('default' => '7a17db8b65b0a7082b639758e06030b9a8cfedc3', ), $hmvcDomains = array(), $errorMemoryReserveSize = 512000;

	public function setGzip($zip) {
		$this -> gzip = $zip;
		if ($zip && function_exists('ob_gzhandler')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		}
	}

	public function getGzip() {
		return $this -> gzip;
	}

	/**
	 * 按照包的顺序查找配置文件
	 * @param type $filename
	 * @return string
	 */
	public function find($filename) {
		foreach ($this->getPackages() as $packagePath) {
			$path = $packagePath . $this -> getConfigDirName() . '/';
			$filePath = $path . $this -> getEnvironment() . '/' . $filename . '.php';
			$fileDefaultPath = $path . 'default/' . $filename . '.php';
			if (file_exists($filePath)) {
				return $filePath;
			} elseif (file_exists($fileDefaultPath)) {
				return $fileDefaultPath;
			}
		}
		return "";
	}

	public function getExceptionMemoryReserveSize() {
		return $this -> errorMemoryReserveSize;
	}

	public function setExceptionMemoryReserveSize($exceptionMemoryReserveSize) {
		$this -> errorMemoryReserveSize = $exceptionMemoryReserveSize;
		return $this;
	}

	public function setExceptionControl($isExceptionControl,$showNotice=false) {
		$this->showErrNotice=$showNotice;
		if ($isExceptionControl && !\Core::isPluginMode()) {
			//注册错误处理
			if (!class_exists("Logger_Writer_Dispatcher", false)) {
				\Base::loadFrame("logger");
			}
			\Logger_Writer_Dispatcher::initialize();
		}
		return $this;
	}
	
	public function getAdminModule(){
		return $this->adminModule;
	}
	
	public function setAdminModule($adminModule){
		$this->adminModule=$adminModule;
		return $this;
	}
	
	public function getShowErrNotice(){
		return $this->showErrNotice;
	}


	public function getStorageDirPath() {
		return empty($this -> storageDirPath) ? $this -> getPrimaryApplicationDir() . 'storage/' : $this -> storageDirPath;
	}

	public function setStorageDirPath($storageDirPath) {
		$this -> storageDirPath = \Core::realPath($storageDirPath, true);
		return $this;
	}

	public function getCurrentDomainHmvcModuleNname() {
		if (!$this -> hmvcDomains['enable']) {
			return false;
		}
		$_domain = \Core::server('http_host');
		$domain = explode('.', $_domain);
		$length = count($domain);
		$topDomain = '';
		if ($length >= 2) {
			$topDomain = $domain[$length - 2] . '.' . $domain[$length - 1];
		}
		foreach ($this->hmvcDomains['domains'] as $prefix => $hvmc) {
			if (($hvmc['isFullDomain'] ? $prefix : ($prefix . '.' . $topDomain)) == $_domain) {
				return $hvmc['enable'] ? $hvmc['hmvcModuleName'] : false;
			}
		}
		return '';
	}

	public function hmvcIsDomainOnly($hmvcModuleName) {
		if (!$hmvcModuleName || !$this -> hmvcDomains['enable']) {
			return false;
		}
		foreach ($this->hmvcDomains['domains'] as $hvmc) {
			if ($hmvcModuleName == $hvmc['hmvcModuleName'] && $hvmc['enable']) {
				return $hvmc['domainOnly'];
			}
		}
		return false;
	}

	public function setHmvcDomains(Array $hmvcDomains) {
		$this -> hmvcDomains = $hmvcDomains;
		return $this;
	}

	public function getEncryptKey() {
		$key = $this -> getEnvironment();
		if (isset($this -> encryptKey[$key])) {
			return $this -> encryptKey[$key];
		} elseif (isset($this -> encryptKey['default'])) {
			return $this -> encryptKey['default'];
		}
		return '';
	}

	public function setEncryptKey($encryptKey) {
		if (is_array($encryptKey)) {
			$this -> encryptKey = $encryptKey;
		} else {
			$this -> encryptKey = array('default' => $encryptKey, );
		}
		return $this;
	}

	public function getXsMethods() {
		return $this -> xsMethods;
	}

	public function setXsMethods(array $xsMethods) {
		$this -> xsMethods = $xsMethods;
		return $this;
	}

	public function getExceptionJsonRender() {
		return $this -> exceptionJsonRender;
	}

	public function setExceptionJsonRender($exceptionJsonRender) {
		$this -> exceptionJsonRender = $exceptionJsonRender;
		return $this;
	}

	public function getOutputJsonRender() {
		return $this -> outputJsonRender;
	}

	public function setOutputJsonRender($outputJsonHandle) {
		$this -> outputJsonRender = $outputJsonHandle;
		return $this;
	}

	public function getDataCheckRules() {
		return $this -> dataCheckRules;
	}

	public function setDataCheckRules($dataCheckRules) {
		$this -> dataCheckRules = is_array($dataCheckRules) ? $dataCheckRules : \Core::config($dataCheckRules, false);
		return $this;
	}

	public function getMethodCacheConfig() {
		return $this -> methodCacheConfig;
	}

	public function setMethodCacheConfig($methodCacheConfig) {
		$this -> methodCacheConfig = is_array($methodCacheConfig) ? $methodCacheConfig : \Core::config($methodCacheConfig, false);
		return $this;
	}

	public function getViewsDirName() {
		return $this -> viewsDirName;
	}

	public function setViewsDirName($viewsDirName) {
		$this -> viewsDirName = $viewsDirName;
		return $this;
	}

	public function getCacheHandle($key = '') {
		if (empty($this -> cacheConfig)) {
			$this -> cacheConfig = array('default_type' => 'file', 'drivers' => array('file' => array('class' => 'I_Cache_File',
			//缓存文件保存路径
			'config' => \Core::config() -> getStorageDirPath() . 'cache/'), ));
		}
		if (is_array($key)) {
			$className = $key['class'];
			$config = $key['config'];
			return is_null($config) ? new $className() : new $className($config);
		} else {
			$key = $key ? $key : $this -> cacheConfig['default_type'];
			if (!\Core::arrayKeyExists("drivers.$key", $this -> cacheConfig)) {
				throw new \Xs_Exception_500('unknown cache type [ ' . $key . ' ]');
			}
			$config = $this -> cacheConfig['drivers'][$key]['config'];
			$className = $this -> cacheConfig['drivers'][$key]['class'];
			if (!\Core::arrayKeyExists($key, $this -> cacheHandles)) {
				$this -> cacheHandles[$key] = is_null($config) ? new $className() : new $className($config);
			}
			return $this -> cacheHandles[$key];
		}
	}

	public function getCacheConfig() {
		return $this -> cacheConfig;
	}

	public function setCacheConfig($cacheConfig) {
		$this -> cacheHandles = array();
		if (is_string($cacheConfig)) {
			$this -> cacheConfig = \Core::config($cacheConfig, false);
		} elseif (is_array($cacheConfig)) {
			$this -> cacheConfig = $cacheConfig;
		} else {
			throw new \Xs_Exception_500('unknown type of cache configure , it should be a string or an array .');
		}
		return $this;
	}

	public function getSessionHandle() {
		return $this -> sessionHandle;
	}

	public function setSessionHandle($sessionHandle) {
		if ($sessionHandle instanceof Xs_Session) {
			$this -> sessionHandle = $sessionHandle;
		} else {
			$this -> sessionHandle = \Core::config($sessionHandle, false);
		}
		return $this;
	}

	public function getSessionConfig() {
		if (empty($this->sessionConfig)) {
			$this->sessionConfig = array(
			    'autostart' => false,
			    'cookie_path' => '/',
			    'cookie_domain' => \Core::server('HTTP_HOST'),
			    'session_name' => 'DAZHOU',
			    'lifetime' => 3600,
			);
		}
		return $this->sessionConfig;
	}

	public function setSessionConfig($sessionConfig) {
		if (is_array($sessionConfig)) {
			$this -> sessionConfig = $sessionConfig;
		} else {
			$this -> sessionConfig = \Core::config($sessionConfig, false);
		}
		return $this;
	}

	public function getDatabseConfig($group = null) {
		if (empty($group)) {
			return $this -> databseConfig;
		} else {
			return \Core::arrayKeyExists($group, $this -> databseConfig) ? $this -> databseConfig[$group] : array();
		}
	}

	public function setDatabseConfig($databseConfig) {
		\Core::clearDbInstances();
		$this -> databseConfig = is_array($databseConfig) ? $databseConfig : \Core::config($databseConfig, false);
		return $this;
	}

	public function getIsMaintainMode() {
		return $this -> isMaintainMode;
	}

	public function getMaintainModeHandle() {
		return $this -> maintainModeHandle;
	}

	public function setIsMaintainMode($isMaintainMode) {
		$this -> isMaintainMode = $isMaintainMode;
		return $this;
	}

	public function setMaintainModeHandle(Xs_Maintain_Handle $maintainModeHandle) {
		$this -> maintainModeHandle = $maintainModeHandle;
		return $this;
	}

	public function getMaintainIpWhitelist() {
		return $this -> maintainIpWhitelist;
	}

	public function setMaintainIpWhitelist($maintainIpWhitelist) {
		$this -> maintainIpWhitelist = $maintainIpWhitelist;
		return $this;
	}

	public function getMethodParametersDelimiter() {
		return $this -> methodParametersDelimiter;
	}

	public function setMethodParametersDelimiter($methodParametersDelimiter) {
		$this -> methodParametersDelimiter = $methodParametersDelimiter;
		return $this;
	}

	public function getRouterUrlModuleKey() {
		return $this -> routerUrlModuleKey;
	}

	public function getRouterUrlControllerKey() {
		return $this -> routerUrlControllerKey;
	}

	public function getRouterUrlMethodKey() {
		return $this -> routerUrlMethodKey;
	}

	public function setRouterUrlModuleKey($routerUrlModuleKey) {
		$this -> routerUrlModuleKey = $routerUrlModuleKey;
		return $this;
	}

	public function setRouterUrlControllerKey($routerUrlControllerKey) {
		$this -> routerUrlControllerKey = $routerUrlControllerKey;
		return $this;
	}

	public function setRouterUrlMethodKey($routerUrlMethodKey) {
		$this -> routerUrlMethodKey = $routerUrlMethodKey;
		return $this;
	}

	public function getUriRewriter() {
		return $this -> uriRewriter;
	}

	public function setUriRewriter(Xs_Uri_Rewriter $uriRewriter) {
		$this -> uriRewriter = $uriRewriter;
		return $this;
	}

	public function getPrimaryApplicationDir() {
		return $this -> primaryApplicationDir;
	}

	public function setPrimaryApplicationDir($primaryApplicationDir) {
		$this -> primaryApplicationDir = \Core::realPath($primaryApplicationDir) . '/';
		return $this;
	}

	public function getBackendServerIpWhitelist() {
		return $this -> backendServerIpWhitelist;
	}

	/**
	 * 如果服务器是ngix之类代理转发请求到后端apache运行的PHP<br>
	 * 那么这里应该设置信任的nginx所在服务器的ip<br>
	 * nginx里面应该设置 X_FORWARDED_FOR server变量来表示真实的客户端IP<br>
	 * 不然通过\Core::clientIp()是获取不到真实的客户端IP的<br>
	 * @param type $backendServerIpWhitelist
	 * @return \AppConfig
	 */
	public function setBackendServerIpWhitelist(Array $backendServerIpWhitelist) {
		$this -> backendServerIpWhitelist = $backendServerIpWhitelist;
		return $this;
	}

	public function getCookiePrefix() {
		return $this -> cookiePrefix;
	}

	public function setCookiePrefix($cookiePrefix) {
		$this -> cookiePrefix = $cookiePrefix;
		return $this;
	}

	public function getLogsSubDirNameFormat() {
		return $this -> logsSubDirNameFormat;
	}

	/**
	 * 设置日志子目录格式，参数就是date()函数的第一个参数,默认是 Y-m-d/H
	 * @param type $logsSubDirNameFormat
	 */
	public function setLogsSubDirNameFormat($logsSubDirNameFormat) {
		$this -> logsSubDirNameFormat = $logsSubDirNameFormat;
		return $this;
	}

	public function addAutoloadFunctions(Array $funciontsFileNameArray) {
		foreach ($funciontsFileNameArray as $functionsFileName) {
			\Core::functions($functionsFileName);
		}
		return $this;
	}

	public function getFunctionsDirName() {
		return $this -> functionsDirName;
	}

	public function setFunctionsDirName($functionsDirName) {
		$this -> functionsDirName = $functionsDirName;
		return $this;
	}

	public function setLanguageDirName($languageDirName) {
		$this -> languageDirName = $languageDirName;
		return $this;
	}

	public function getLanguageDirName() {
		return $this -> languageDirName;
	}

	public function setLanguageTypeDirName($languageTypeDirName) {
		$this -> languageTypeDirName = $languageTypeDirName;
		return $this;
	}

	public function getLanguageTypeDirName() {
		return $this -> languageTypeDirName;
	}

	public function setLanguageCharset($languageCharset) {
		$this -> languageCharset = $languageCharset;
		return $this;
	}

	public function getLanguageCharset() {
		return $this -> languageCharset;
	}

	public function getModelDirName() {
		return $this -> modelDirName;
	}

	public function setModelDirName($modelDirName) {
		$this -> modelDirName = $modelDirName;
		return $this;
	}

	public function getBeanDirName() {
		return $this -> beanDirName;
	}

	public function setBeanDirName($beanDirName) {
		$this -> beanDirName = $beanDirName;
		return $this;
	}

	public function getBusinessDirName() {
		return $this -> businessDirName;
	}

	public function getDaoDirName() {
		return $this -> daoDirName;
	}

	public function getTaskDirName() {
		return $this -> taskDirName;
	}

	public function setBusinessDirName($businessDirName) {
		$this -> businessDirName = $businessDirName;
		return $this;
	}

	public function setDaoDirName($daoDirName) {
		$this -> daoDirName = $daoDirName;
		return $this;
	}

	public function setTaskDirName($taskDirName) {
		$this -> taskDirName = $taskDirName;
		return $this;
	}

	public function getEnvironment() {
		return $this -> environment;
	}

	public function setEnvironment($environment) {
		$this -> environment = $environment;
		return $this;
	}

	public function getConfigDirName() {
		return $this -> configDirName;
	}

	public function setConfigDirName($configDirName) {
		$this -> configDirName = $configDirName;
		return $this;
	}

	public function getRoute() {
		return empty($this -> route) ? new \Route() : $this -> route;
	}

	public function setRoute($route) {
		$this -> route = $route;
		return $this;
	}

	public function getLibraryDirName() {
		return $this -> libraryDirName;
	}

	public function setLibraryDirName($libraryDirName) {
		$this -> libraryDirName = $libraryDirName;
		return $this;
	}

	public function getHmvcDirName() {
		return $this -> hmvcDirName;
	}

	public function setHmvcDirName($hmvcDirName) {
		$this -> hmvcDirName = $hmvcDirName;
		return $this;
	}

	public function getHmvcModules() {
		return $this -> hmvcModules;
	}

	public function setHmvcModules($hmvcModules) {
		$this -> hmvcModules = $hmvcModules;
		return $this;
	}

	public function getControllerDirName() {
		return $this -> controllerDirName;
	}

	public function setControllerDirName($controllerDirName) {
		$this -> controllerDirName = $controllerDirName;
		return $this;
	}

	public function getExceptionHandle() {
		return $this -> exceptionHandle;
	}

	public function setExceptionHandle($exceptionHandle) {
		$this -> exceptionHandle = $exceptionHandle;
		return $this;
	}

	public function getApplicationDir() {
		return $this -> applicationDir;
	}

	public function getIndexDir() {
		return $this -> indexDir;
	}

	public function getIndexName() {
		return $this -> indexName;
	}

	public function setApplicationDir($applicationDir) {
		$this -> applicationDir = \Core::realPath($applicationDir) . '/';
		if (empty($this -> primaryApplicationDir)) {
			$this -> primaryApplicationDir = $this -> applicationDir;
		}
		return $this;
	}

	public function setIndexDir($indexDir) {
		$this -> indexDir = \Core::realPath($indexDir) . '/';
		return $this;
	}

	public function setIndexName($indexName) {
		$this -> indexName = $indexName;
		return $this;
	}

	public function setLoggerWriterContainer(Xs_Logger_Writer $loggerWriterContainer) {
		$this -> loggerWriterContainer = $loggerWriterContainer;
		return $this;
	}

	public function getMethodPrefix() {
		return $this -> methodPrefix;
	}

	public function getMethodUriSubfix() {
		return $this -> methodUriSubfix;
	}

	public function setMethodPrefix($methodPrefix) {
		$this -> methodPrefix = $methodPrefix;
		return $this;
	}

	public function setMethodUriSubfix($methodUriSubfix) {
		if (!$methodUriSubfix) {
			throw new \Xs_Exception_500('"Method Uri Subfix" can not be empty.');
		}
		$this -> methodUriSubfix = $methodUriSubfix;
		return $this;
	}

	public function getDefaultController() {
		return $this -> defaultController;
	}

	public function getDefaultMethod() {
		return $this -> defaultMethod;
	}

	public function setDefaultController($defaultController) {
		$this -> defaultController = $defaultController;
		return $this;
	}

	public function setDefaultMethod($defaultMethod) {
		$this -> defaultMethod = $defaultMethod;
		return $this;
	}

	public function getClassesDirName() {
		return $this -> classesDirName;
	}

	public function setClassesDirName($classesDirName) {
		$this -> classesDirName = $classesDirName;
		return $this;
	}

	public function getPackages() {
		return array_merge($this -> packageMasterContainer, $this -> packageContainer);
	}

	public function addMasterPackages(Array $packagesPath) {
		foreach ($packagesPath as $packagePath) {
			$this -> addMasterPackage($packagePath);
		}
		return $this;
	}

	public function addMasterPackage($packagePath) {
		$packagePath = \Core::realPath($packagePath) . '/';
		if (!in_array($packagePath, $this -> packageMasterContainer)) {
			//注册“包”到主包容器中
			array_push($this -> packageMasterContainer, $packagePath);
			if (file_exists($library = $packagePath . $this -> getLibraryDirName() . '/')) {
				array_push($this -> packageMasterContainer, $library);
			}
		}
		return $this;
	}

	public function addPackages(Array $packagesPath) {
		foreach ($packagesPath as $packagePath) {
			$this -> addPackage($packagePath);
		}
		return $this;
	}

	public function addPackage($packagePath) {
		$packagePath = \Core::realPath($packagePath) . '/';
		if (!in_array($packagePath, $this -> packageContainer)) {
			//注册“包”到包容器中
			array_push($this -> packageContainer, $packagePath);
			if (file_exists($library = $packagePath . $this -> getLibraryDirName() . '/')) {
				array_push($this -> packageContainer, $library);
			}
		}
		return $this;
	}

	/**
	 * 加载项目目录下的bootstrap.php配置
	 */
	public function bootstrap() {
		//引入“bootstrap”配置
		if (file_exists($bootstrap = $this -> getApplicationDir() . 'bootstrap.php')) {
			\Core::includeOnce($bootstrap);
		}
	}

	public function getShowError() {
		return $this -> showError;
	}

	public function getRoutersContainer() {
		return $this -> routersContainer;
	}

	public function setShowError($showError) {
		$this -> showError = $showError;
		return $this;
	}

	public function getRequest() {
		return $this -> request;
	}

	public function setRequest(Xs_Request $request) {
		$this -> request = $request;
		return $this;
	}

	public function addRouter(Router $router) {
		array_unshift($this -> routersContainer, $router);
		return $this;
	}

	public function getRouters() {
		return $this -> routersContainer;
	}

	public function addLoggerWriter(Xs_Logger_Writer $loggerWriter) {
		$this -> loggerWriterContainer[] = $loggerWriter;
		return $this;
	}

	public function getLoggerWriters() {
		return $this -> loggerWriterContainer;
	}

	public function getIsRewrite() {
		return $this -> isRewrite;
	}

	public function setTimeZone($timeZone) {
		date_default_timezone_set($timeZone);
		return $this;
	}

	public function setIsRewrite($isRewrite) {
		$this -> isRewrite = $isRewrite;
		return $this;
	}

}
?>