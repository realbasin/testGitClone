<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
class Logger_Writer_Dispatcher {
	private static $instance;
	private static $memReverse;

	public static function initialize() {
		if (empty(self::$instance)) {
			//保留内存
			self::$memReverse = str_repeat("x", \Base::getConfig() -> getExceptionMemoryReserveSize());
			self::$instance = new \Logger_Writer_Dispatcher();
			error_reporting(E_ALL);
			//插件模式打开错误显示，web和命令行模式关闭错误显示
			\Core::isPluginMode() ? ini_set('display_errors', TRUE) : ini_set('display_errors', FALSE);
			set_exception_handler(array(self::$instance, 'handleException'));
			set_error_handler(array(self::$instance, 'handleError'), \Base::getConfig() -> getShowErrNotice() ? E_ALL : E_ALL & ~E_NOTICE);
			register_shutdown_function(array(self::$instance, 'handleFatal'));
		}
	}

	final public function handleException($exception) {
		if (is_subclass_of($exception, 'Xs_Exception')) {
			$this -> dispatch($exception);
		} else {
			$this -> dispatch(new \Xs_Exception_500($exception -> getMessage(), $exception -> getCode(), get_class($exception), $exception -> getFile(), $exception -> getLine()));
		}
	}

	final public function handleError($code, $message, $file, $line) {
		if (0 == error_reporting()) {
			return;
		}

		$this -> dispatch(new \Xs_Exception_500($message, $code, 'General Error', $file, $line));
	}

	final public function handleFatal() {
		if (0 == error_reporting()) {
			return;
		}

		$lastError = error_get_last();
		$fatalError = array(1, 256, 64, 16, 4, 4096);
		if (!\Core::arrayKeyExists("type", $lastError) || !in_array($lastError["type"], $fatalError)) {
			return;
		}
		//当发生致命错误的时候，释放保留的内存，提供给下面的处理代码使用
		self::$memReverse = null;
		$this -> dispatch(new \Xs_Exception_500($lastError['message'], $lastError['type'], 'Fatal Error', $lastError['file'], $lastError['line']));
	}

	final public function dispatch(Xs_Exception $exception) {
		$config = \Core::config();
		ini_set('display_errors', TRUE);
		$loggerWriters = $config -> getLoggerWriters();
		foreach ($loggerWriters as $loggerWriter) {
			$loggerWriter -> write($exception);
		}
		if ($config -> getShowError()) {
			$handle = $config -> getExceptionHandle();
			if ($handle instanceof Xs_Exception_Handle) {
				$handle -> handle($exception);
			} else {
				$exception -> render();
			}
		} elseif (\Core::isCli()) {
			$exception -> render();
		}
		exit();
	}

}
?>