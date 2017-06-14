<?php

defined("IN_XIAOSHU") or exit("Access Invalid!");
define("BASE_PATH",str_replace('\\','/',dirname(__FILE__)));
//hmvc项目配置，下面是hmvc项目可以覆盖主项目配置的配置项，需要覆盖主项目的配置去掉相应的注释即可。
\Base::getConfig()
	//->setExceptionControl(true)
	//->setTimeZone('PRC')
	//->setStorageDirPath(APP_PATH . 'storage/')
	->addMasterPackages(array(
		//dirname(__FILE__) . '/packages/misc/'
	))
	->addAutoloadFunctions(array(
		//'main'
	))
	//->setEnvironment(($env = (($cliEnv = \Core::getOpt('env')) ? $cliEnv : \Core::arrayGet($_SERVER, 'ENVIRONMENT'))) ? \Core::config()->getServerEnvironment($env) : \Core::ENV_DEVELOPMENT)
	//->setShowError(\Core::config()->getEnvironment() != \Core::ENV_PRODUCTION)
	/**
	 * 下面配置中可以使用：
	 * 1.主项目的claseses目录，类库目录，拓展包里面的类
	 * 2.当前hmvc子项目的claseses目录，类库目录，拓展包里面的类
	 * 3.这几个目录如果存在同名类，使用的优先级高到低是：
	 * hmvc子项目拓展包->hmvc子项目类库目录->hmvc子项目claseses目录->主项目拓展包->主项目类库目录->主项目claseses目录
	 */
	//->setIsMaintainMode(false)
	//->setMaintainIpWhitelist(array('127.0.0.2', '192.168.0.2/32'))
	//->setMaintainModeHandle(new \I_Maintain_Handle_Default())
	//->setBackendServerIpWhitelist(array('192.168.2.2'))
	//->addLoggerWriter(new \Logger_MyWriter())
	//->setExceptionHandle(new \Exception_HandleTest())
	//->setIsRewrite(false)
	//->setRouterUrlControllerKey('c')
	//->setRouterUrlMethodKey('a')
	//->setRouterUrlModuleKey('m')
	->setDefaultController('index')
	->setDefaultMethod('index')
	->setMethodPrefix('do_')
	->setMethodUriSubfix('.do')
	//->setSessionConfig(array(
	//    'autostart' => false,
	//    'cookie_path' => '/',
	//    'cookie_domain' => Core::server('HTTP_HOST'),
	//    'session_name' => 'DAZHOU',
	//    'lifetime' => 3600,
	//    'session_save_path'=>null,//\Core::config()->getStorageDirPath().'/sessions'
	//))
	//->setCookiePrefix('')
	//->setEncryptKey(array(
	//    Core::ENV_DEVELOPMENT => '', //开发环境密钥
	//    Core::ENV_TESTING => '', //测试环境密钥
	//    Core::ENV_PRODUCTION => ''//产品环境密钥
	//))
	//->setCacheConfig('cache')
	//->setDatabseConfig('database')
	//->setExceptionHandle(new \I_Exception_Handle_Default())
	//->addLoggerWriter(new \Logger_FileWriter(\Core::config()->getStorageDirPath() . 'logs/', false))
	//->setLogsSubDirNameFormat('Y-m-d/H')
	//->setSessionHandle('session')
	//->setMethodCacheConfig('method_cache')
	//->setDataCheckRules('rules')
	//->setOutputJsonRender(function() {
	//	$args = func_get_args();
	//	$code = \Core::arrayGet($args, 0, '');
	//	$message = \Core::arrayGet($args, 1, '');
	//	$data = \Core::arrayGet($args, 2, '');
	//	return @json_encode(array('code' => $code, 'message' => $message, 'data' => $data));
	//})
	//->setExceptionJsonRender(function(Exception $e) {
	//	$json['environment'] = $e->getEnvironment();
	//	$json['file'] = $e->getErrorFile();
	//	$json['line'] = $e->getErrorLine();
	//	$json['message'] = $e->getErrorMessage();
	//	$json['type'] = $e->getErrorType();
	//	$json['code'] = $e->getErrorCode();
	//	$json['time'] = date('Y/m/d H:i:s T');
	//	$json['trace'] = $e->getTraceCliString();
	//	return @json_encode($json);
	//})

;
