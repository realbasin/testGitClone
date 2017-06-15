<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
abstract class Router {

	protected $route;
	public function __construct() {
		$this -> route = new \Route();
	}


	public abstract function find();
	public function & route() {
		return $this -> route;
	}

}

class Router_Get_Default extends Router {
	public function find() {
		$config = \Core::config();
		$query = $config -> getRequest() -> getQueryString();
		//pathinfo非空说明是pathinfo路由，get路由器不再处理直接返回
		if ($config -> getRequest() -> getPathInfo() || !$query) {
			return $this -> route -> setFound(FALSE);
		}
		parse_str($query, $get);
		$controllerName = \Core::arrayGet($get, $config -> getRouterUrlControllerKey(), '');
		$methodName = \Core::arrayGet($get, $config -> getRouterUrlMethodKey(), '');
		$hmvcModule = \Core::arrayGet($get, $config -> getRouterUrlModuleKey(), '');
		$_hmvcModule = $config -> getCurrentDomainHmvcModuleNname();
		if (!$_hmvcModule) {
			if ($config -> hmvcIsDomainOnly($hmvcModule)) {
				//当前域名没有绑定任何hmvc模块，而且当前hmvc模块是domainOnly的，禁止访问当前hmvc模块
				$hmvcModule = '';
			}
		} else {
			//当前域名绑定了hmvc模块，就重置$hmvcModule为绑定的hvmc模块
			$hmvcModule = $_hmvcModule;
		}
		//处理hmvc模块
		$hmvcModuleDirName = \Base::checkHmvc($hmvcModule, false);
		if ($controllerName) {
			$controllerName = $config -> getControllerDirName() . '_' . $controllerName;
		}
		if ($methodName) {
			$methodName = $config -> getMethodPrefix() . $methodName;
		}
		return $this -> route -> setHmvcModuleName($hmvcModuleDirName ? $hmvcModule : '') -> setController($controllerName) -> setMethod($methodName) -> setFound($hmvcModuleDirName || $controllerName);
	}

}

class Router_PathInfo_Default extends Router {
	public function find() {
		$config = \Base::getConfig();
		$uri = $config -> getRequest() -> getPathInfo();
		$uri = trim($uri, '/');
		if (empty($uri)) {
			//没有找到hmvc模块名称，或者控制器名称
			return $this -> route -> setFound(FALSE);
		} else {
			if ($uriRewriter = $config -> getUriRewriter()) {
				$uri = $uriRewriter -> rewrite($uri);
			}
		}
		//到此$uri形如：Welcome/index.do , Welcome/User , Welcome
		$_info = explode('/', $uri);
		$hmvcModule = current($_info);
		//当前域名绑定了hvmc模块$_hmvcModule就是模块名称，反之为空
		$_hmvcModule = $config -> getCurrentDomainHmvcModuleNname();
		if (!$_hmvcModule) {
			if ($config -> hmvcIsDomainOnly($hmvcModule)) {
				//当前域名没有绑定任何hmvc模块，而且当前hmvc模块是domainOnly的，禁止访问当前hmvc模块
				$hmvcModule = '';
			}
		} else {
			//当前域名绑定了hmvc模块，那么当前域名就指向固定的配置的hmvc模块，重置$hmvcModule为绑定的hvmc模块
			$hmvcModule = $_hmvcModule;
		}
		//处理hmvc模块
		$hmvcModuleDirName = \Base::checkHmvc($hmvcModule, FALSE);
		if (!$_hmvcModule && $hmvcModuleDirName && !$config -> hmvcIsDomainOnly($hmvcModule)) {
			//当前域名没有绑定hvmc,且访问的是hmvc模块，且是非domainOnly的，那么就去除hmvc模块名称，得到真正的路径
			$uri = ltrim(substr($uri, strlen($hmvcModule)), '/');
		}
		//首先控制器名和方法名初始化为默认
		$controller = $config -> getDefaultController();
		$method = $config -> getDefaultMethod();
		$subfix = $config -> getMethodUriSubfix();
		/**
		 * 到此，如果上面$uri被去除掉hmvc模块名称后，$uri有可能是空
		 * 或者$uri有控制器名称或者方法-参数名称
		 * 形如：1.Welcome/article.do , 2.Welcome/article-001.do ,
		 *      3.article-001.do ,4.article.do , 5.Welcome/User , 6.Welcome
		 */
		if ($uri) {
			//解析路径
			$methodPathArr = explode($subfix, $uri);
			//找到了控制器名或者方法-参数名(1,2,3,4)
			if (\Core::strEndsWith($uri, $subfix)) {
				//找到了控制器名和方法-参数名(1,2)，覆盖上面的默认控制器名和方法-参数名
				if (stripos($methodPathArr[0], '/') !== false) {
					$controller = str_replace('/', '_', dirname($uri));
					$method = basename($methodPathArr[0]);
				} else {
					//只找到了方法-参数名(3,4)，覆盖上面的默认方法名
					$method = basename($methodPathArr[0]);
				}
			} else {
				//只找到了控制器名(5,6)，覆盖上面的默认控制器名
				$controller = str_replace('/', '_', $uri);
			}
		}
		$controller = $config -> getControllerDirName() . '_' . $controller;
		//统一解析方法-参数名
		$methodAndParameters = explode($config -> getMethodParametersDelimiter(), $method);
		$method = $config -> getMethodPrefix() . current($methodAndParameters);
		array_shift($methodAndParameters);
		$parameters = $methodAndParameters;
		return $this -> route -> setHmvcModuleName($hmvcModuleDirName ? $hmvcModule : '') -> setController($controller) -> setMethod($method) -> setArgs($parameters) -> setFound(TRUE);
	}

}

class Router_PathInfo_Api  extends Router
{
    public function find()
    {
        $config = \Base::getConfig();
        $uri = $config->getRequest()->getPathInfo();
        $uri = trim($uri, '/');
        if (empty($uri)) {
            //没有找到hmvc模块名称，或者控制器名称
            return $this->route->setFound(FALSE);
        } else {
            if ($uriRewriter = $config->getUriRewriter()) {
                $uri = $uriRewriter->rewrite($uri);
            }
        }
        //到此$uri形如：Welcome/index.do , Welcome/User , Welcome
        $_info = explode('/', $uri);
        $_info = array_filter($_info);

        $moduleName = $_info[0];
        $hmvcModuleDirName = \Base::checkHmvc($moduleName, FALSE);
        if($hmvcModuleDirName){
            array_shift($_info);
        }else{
            //没找到hmvc下的模块，认定application下主模块
            $moduleName = '';
        }

        $controllerName =  $controllerName = $config->getControllerDirName() . '_' . $_info[0];
        if(class_exists($controllerName)){
            array_shift($_info);
        }else{
            $controllerName = $config->getControllerDirName() . '_' .$config -> getDefaultController();
        }

        $method = $config->getMethodPrefix() . $_info[0];
        if(method_exists($controllerName,$method)){
            array_shift($_info);
        }else{
            $method = $config->getMethodPrefix() . $config -> getDefaultMethod();
        }

        PathInfoArgs::set($_info);

        return $this->route->setHmvcModuleName($moduleName)->setController($controllerName)->setMethod($method)->setArgs($_info)->setFound(TRUE);
    }
}

class PathInfoArgs{
    private static $args = null;

    public static function set($args){
        self::$args = $args;
    }

    /**
     * 获取通过PATH_INFO传递的参数
     * @param $position int 位置，从1开始
     * @return null|string
     */
    public static function get($position){
        if(self::$args == null){
            return null;
        }else{
            $index = $position - 1;
            return isset(self::$args[$index]) ? self::$args[$index] : null;
        }
    }
}
?>