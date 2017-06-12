<?php
//新框架测试入口文件

define("IN_XIAOSHU",TRUE);
define("ROOT_PATH",str_replace('\\','/',dirname(__FILE__)));
/* 引入核心 */
require dirname(__FILE__) . '/core/base.php';
/* 项目目录路径 */
define('APP_PATH', ROOT_PATH . '/application/');
/* 项目拓展包路径 */
define('PACKAGES_PATH', APP_PATH . 'packages/');
/* 本地数据存储路径 */
define('STORAGE_PATH',  ROOT_PATH.'/storage/');
/*供前端引用的项目资源路径，必须是相对地址或者http地址*/
define('RS_PATH', '/resource/');
/*供前端引用的项目存储路径，必须是相对地址或者http地址*/
define('ST_PATH', '/storage/');
//以下定义是方维原先的
//数据库加密
define("AES_DECRYPT_KEY","__FANWEP2P__");
/* 初始化配置 */
\Base::initialize()
	/* 设置异常管理程序保留的内存大小，单位byte */
	->setExceptionMemoryReserveSize(512000)
	/* 设置程序管理异常错误，第2个参数表示是否处理E_NOTICE错误 */
	->setExceptionControl(true,true)
	/* 时区设置 */
	->setTimeZone('PRC')
	/* 项目目录路径 */
	->setApplicationDir(APP_PATH)
	/* 项目存储数据目录路径，必须可写 */
	->setStorageDirPath(STORAGE_PATH)
	/* 注册项目包 */
	->addPackage(APP_PATH)
    ->addPackage(PACKAGES_PATH.'gearman')
    ->addPackage(PACKAGES_PATH.'cron')
	/* 注册自动加载的函数文件 */
	->addAutoloadFunctions(array(
		'main'
	))
	/* 设置运行环境 ,值就是config配置目录下面的子文件夹名称,区分大小写 */
	->setEnvironment(($env = (($cliEnv = \Core::getOpt('env')) ? $cliEnv : \Core::arrayGet($_SERVER, 'ENVIRONMENT'))) ? $env : 'development')
	/* 系统错误显示设置，非产品环境才显示 */
	->setShowError(\Core::config()->getEnvironment() != 'production')
	/**
	 * 下面配置中可以使用：
	 * 1.主项目的claseses目录，主项目类库目录，主项目拓展包里面的类
	 * 2.这几个目录如果存在同名类，使用的优先级高到低是：
	 *   主项目classes->类库classes->拓展包classes->拓展包类库classes
	 */
	/* 入口文件所在目录 */
	->setIndexDir(dirname(__FILE__) . '/')
	/* 入口文件名称 */
	->setIndexName(pathinfo(__FILE__, PATHINFO_BASENAME))
	/* 宕机维护模式 */
	->setIsMaintainMode(false)
	/* 宕机维护模式IP白名单 */
	//->setMaintainIpWhitelist(array('127.0.0.2', '192.168.0.2/32'))
	/* 宕机维护模式处理方法 */
	->setMaintainModeHandle(new I_Maintain_Handle_Default())
	/**
	 * 如果服务器是ngix之类代理转发请求到后端apache运行的PHP。
	 * 那么这里应该设置信任的nginx所在服务器的ip。
	 * nginx里面应该设置 X_FORWARDED_FOR server变量来表示真实的客户端IP。
	 * 不然通过\Core::clientIp()是获取不到真实的客户端IP的。
	 * 参数是数组，有多个ip放入数组即可。
	 */
	//->setBackendServerIpWhitelist(array('192.168.2.2'))
	/* 初始化请求 */
	->setRequest(new I_Request_Default())
	/* 网站是否开启了nginx或者apache的url“伪静态”重写，开启了这里设置为true，
	  这样Sr::url方法在生成url的时候就知道是否加上入口文件名称 */
	->setIsRewrite(false)
	/* 注册默认pathinfo路由器 */
	->addRouter(new \Router_PathInfo_Default())
	/* pathinfo路由器,注册uri重写 */
	->setUriRewriter(new I_Uri_Rewriter_Default())
	/* 注册默认get路由器 */
	->addRouter(new \Router_Get_Default())
	/* get路由器,url中的控制器的get变量名 */
	->setRouterUrlControllerKey('c')
	/* get路由器,url中的方法的get变量名 */
	->setRouterUrlMethodKey('a')
	/* get路由器,url中的hmvc模块的get变量名 */
	->setRouterUrlModuleKey('m')
	/* 默认控制器 */
	->setDefaultController('Index')
	/* 默认方法 */
	->setDefaultMethod('index')
	/* 控制器方法前缀 */
	->setMethodPrefix('do_')
	/* 方法url后缀 */
	->setMethodUriSubfix('.do')
	/* 注册hmvc模块，数组键是uri里面的hmvc模块名称，值是hmvc模块文件夹名称 */
	->setHmvcModules(array(
		'admin' => 'admin',
		'api'=>'api'
	))
	/* 管理后台模块 */
	->setAdminModule('admin')
	/* hvmc模块域名绑定
	 * 1.子域名绑定
	 * domains的键是二级开始的域，不包含顶级域名.
	 * 比如顶级域名是test.com,这里的domains的键是demo代表demo.test.com
	 * 再比如domains的键是i.user代表i.user.test.com
	 * isFullDomain这里设置为false.
	 * 2.完整域名绑定
	 * domains的键是完整的域名,比如demo.com,
	 * isFullDomain这里设置为true.
	 * 配置项介绍:
	 * 	0.最外层的enable是总开关.
	 * 	1.hmvcModuleName是域名要绑定的hmvc模块名称，
	 * 	   也就是对应着上面的setHmvcModules()注册的关联数组中的"键"名称.
	 * 	2.demo下面的enable是单个域名绑定是否启用.
	 * 	3.domainOnly是否只能通过绑定的域名访问hvmc模块.
	 * 	4.绑定完整的域名isFullDomain这里设置为true.
	 * 	   绑定子域名isFullDomain这里设置为false.
	 */
	->setHmvcDomains(array(
	    'enable' => false, //总开关，是否启用
	    'domains' => array(
		'admin' => array(
		    'hmvcModuleName' => 'admin', //hvmc模块名称
		    'enable' => false, //单个开关，是否启用
		    'domainOnly' => true, //是否只能通过绑定的域名访问
		    'isFullDomain' => false//绑定完整的域名设置为true；子域名设置为false
		)
	    )
	))
	/* 设置session信息 */
//	->setSessionConfig(array(
//	    'autostart' => false,
//	    'cookie_path' => '/',
//	    'cookie_domain' => \Core::server('HTTP_HOST'),
//	    'session_name' => 'XSSESSION',
//	    'lifetime' => 3600,
//	    'session_save_path' => STORAGE_PATH.'sessions', //null这里用相对路径会出错
//	))
	/* 设置cookie key前缀，当我们使用\Core::setCookie()的时候，
	 * 参数里面的key自动加上这里设置的前缀 */
	->setCookiePrefix('XiaoShu')
	/* 设置加密方法\Core::encrypt()和解密方法\Core::decrypt()使用的密钥,
	 * 只有这里设置了密钥，当不传递key的时候，这两个方法才能正常使用。
	 * 提示：这里可以使用数组指定三个环境下的密钥，还可以传递一个字符串
	 * 这个字符串就是所有环境使用的密钥。 */
//	->setEncryptKey(array(
//	    'development' => '', //开发环境密钥
//	    'testing' => '', //测试环境密钥
//	    'production' => ''//产品环境密钥
//	))
	/**
	 * 配置缓存
	 * 1.setCacheHandle可以直接传入缓存配置数组。
	 * 2.setCacheHandle也可以传入配置文件名称，配置文件里面要返回一个缓存配置数组。
	 * 缓存配置数组可以参考缓存配置文件：application/config/default/cache.php里面return的数组。
	 * 3.如果这里不设置(保留注释)，\Core::cache()默认使用的是文件缓存，
	 * 缓存数据默认存储在application/storage/cache
	 */
	->setCacheConfig('cache')
	/* 设置数据库连接信息，参数可以是配置文件名称；也可以是数据库配置信息数组，即配置文件返回的那个数组。 */
	->setDatabseConfig('database')
	/* 设置自定义的错误显示控制处理类 */
	->setExceptionHandle(new I_Exception_Handle_Default())
	/* 错误日志记录，注释掉这行会关闭日志记录，去掉注释则开启日志文件记录,
	 * 第一个参数是日志文件路径，第二个参数为是否记录404类型异常 */
	->addLoggerWriter(new I_Logger_FileWriter(STORAGE_PATH . 'logs/', false))
	/* 设置日志记录子目录格式，参数就是date()函数的第一个参数,默认是 Y-m-d/H */
	->setLogsSubDirNameFormat('Y-m-d/H')
	/*
	 * 拓展类的方法，参数是关联数组，键是拓展的方法名称，值是前缀字符串或者回调匿名函数
	 */
	->setXsMethods(array())
	/**
	 * 设置session托管类型
	 * 1.setSessionHandle可以直接传入Xs_Session类对象
	 * 2.setSessionHandle也可以传入配置文件名称，配置文件里面要返回一个Xs_Session类对象。
	 */
	//->setSessionHandle('session')
	/* 设置控制器方法缓存规则，参数可以是配置文件名称，也可以是配置规则数组 */
	//->setMethodCacheConfig('method_cache')
	/* 设置自定义数据验证规则，参数可以是配置文件名称，也可以是规则数组 */
	//->setDataCheckRules('rules')
	/* 设置\Core::json()输出处理回调函数，这里可以自定义json输出格式 */
	->setOutputJsonRender(function() {
		$args = func_get_args();
		$code = \Core::arrayGet($args, 0, '');
		$message = \Core::arrayGet($args, 1, '');
		$data = \Core::arrayGet($args, 2, '');
		return @json_encode(array('code' => $code, 'message' => $message, 'data' => $data));
	})
	/* 设置发生异常的时候，调用异常对象的renderJson()方法输出json的回调函数，这里可以自定义json输出格式 */
	->setExceptionJsonRender(function(Exception $e) {
		$json['environment'] = $e->getEnvironment();
		$json['file'] = $e->getErrorFile();
		$json['line'] = $e->getErrorLine();
		$json['message'] = $e->getErrorMessage();
		$json['type'] = $e->getErrorType();
		$json['code'] = $e->getErrorCode();
		$json['time'] = date('Y/m/d H:i:s T');
		$json['trace'] = $e->getTraceCliString();
		return @json_encode($json);
	})
;
//设置全局缓存
getGlobalConfig();
//获取全局缓存之后，根据需要，需要做设置调整
//启动
\Base::run();
?>