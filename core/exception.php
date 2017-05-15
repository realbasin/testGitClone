<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
abstract class Xs_Exception extends Exception {

	protected $errorMessage, $errorCode, $errorFile, $errorLine, $errorType, $trace, $httpStatusLine = 'HTTP/1.0 500 Internal Server Error', $exceptionName = 'Xs_Exception';
	public function __construct($errorMessage = '', $errorCode = 0, $errorType = 'Exception', $errorFile = '', $errorLine = '0') {
		parent::__construct($errorMessage, $errorCode);
		$this -> errorMessage = $errorMessage;
		$this -> errorCode = $errorCode;
		$this -> errorType = $errorType;
		$this -> errorFile = \Core::realPath($errorFile);
		$this -> errorLine = $errorLine;
		$this -> trace = debug_backtrace(false);
	}

	public function errorType2string($errorType) {
		$value = $errorType;
		$levelNames = array(E_ERROR => 'ERROR', E_WARNING => 'WARNING', E_PARSE => 'PARSE', E_NOTICE => 'NOTICE', E_CORE_ERROR => 'CORE_ERROR', E_CORE_WARNING => 'CORE_WARNING', E_COMPILE_ERROR => 'COMPILE_ERROR', E_COMPILE_WARNING => 'COMPILE_WARNING', E_USER_ERROR => 'USER_ERROR', E_USER_WARNING => 'USER_WARNING', E_USER_NOTICE => 'USER_NOTICE');
		if (defined('E_STRICT')) {
			$levelNames[E_STRICT] = 'STRICT';
		}
		if (defined('E_DEPRECATED')) {
			$levelNames[E_DEPRECATED] = 'DEPRECATED';
		}
		if (defined('E_USER_DEPRECATED')) {
			$levelNames[E_USER_DEPRECATED] = 'USER_DEPRECATED';
		}
		if (defined('E_RECOVERABLE_ERROR')) {
			$levelNames[E_RECOVERABLE_ERROR] = 'RECOVERABLE_ERROR';
		}
		$levels = array();
		if (($value & E_ALL) == E_ALL) {
			$levels[] = 'E_ALL';
			$value &= ~E_ALL;
		}
		foreach ($levelNames as $level => $name) {
			if (($value & $level) == $level) {
				$levels[] = $name;
			}
		}
		if (empty($levelNames[$this -> errorCode])) {
			return $this -> errorType ? $this -> errorType : 'General Error';
		}
		return implode(' | ', $levels);
	}

	public function getErrorMessage() {
		return $this -> errorMessage ? $this -> errorMessage : $this -> getMessage();
	}

	public function getErrorCode() {
		return $this -> errorCode ? $this -> errorCode : $this -> getCode();
	}

	public function getEnvironment() {
		return \Core::config() -> getEnvironment();
	}

	public function getErrorFile($safePath = FALSE) {
		$file = $this -> errorFile ? $this -> errorFile : $this -> getFile();
		return $safePath ? \Core::safePath($file) : $file;
	}

	public function getErrorLine() {
		return $this -> errorLine ? $this -> errorLine : ($this -> errorFile ? $this -> errorLine : $this -> getLine());
	}

	public function getErrorType() {
		return $this -> errorType2string($this -> errorCode);
	}

	public function render($isJson = FALSE, $return = FALSE) {
		if ($isJson) {
			$string = $this -> renderJson();
		} elseif (\Core::isCli()) {
			$string = $this -> renderCli();
		} else {
			$string = str_replace('</body>', $this -> getTraceString(FALSE) . '</body>', $this -> renderHtml());
		}
		if ($return) {
			return $string;
		} else {
			echo $string;
		}
	}

	public function getTraceCliString() {
		return $this -> getTraceString(TRUE);
	}

	public function getTraceHtmlString() {
		return $this -> getTraceString(FALSE);
	}

	private function getTraceString($isCli) {
		$trace = array_reverse($this -> trace);
		$str = $isCli ? "[ Debug Backtrace ]\n" : '<div style="padding:10px;">[ Debug Backtrace ]<br/>';
		if (empty($trace)) {
			return '';
		}
		$i = 1;
		foreach ($trace as $e) {
			$file = \Core::safePath(\Core::arrayGet($e, 'file'));
			$line = \Core::arrayGet($e, 'line');
			$func = (!empty($e['class']) ? "{$e['class']}{$e['type']}{$e['function']}()" : "{$e['function']}()");
			$str .= "&rarr; " . ($i++) . ".{$func} " . ($line ? "[ line:{$line} {$file} ]" : '') . ($isCli ? "\n" : '<br/>');
		}
		$str .= $isCli ? "\n" : '</div>';
		return $str;
	}

	public function renderCli() {
		return "$this->exceptionName [ " . $this -> getErrorType() . " ]\n" . "Environment: " . $this -> getEnvironment() . "\n" . "Line: " . $this -> getErrorLine() . ". " . $this -> getErrorFile() . "\n" . "Message: " . $this -> getErrorMessage() . "\n" . "Time: " . date('Y/m/d H:i:s T') . "\n";
	}

	public function renderHtml() {
		return '<body style="padding:0;margin:0;background:black;color:whitesmoke;">' . '<div style="padding:10px;background:red;font-size:18px;">' . $this -> exceptionName . ' [ ' . $this -> getErrorType() . ' ] </div>' . '<div style="padding:10px;background:black;font-size:14px;color:yellow;line-height:1.5em;">' . '<font color="whitesmoke">Environment: </font>' . $this -> getEnvironment() . '<br/>' . '<font color="whitesmoke">Line: </font>' . $this -> getErrorLine() . ' [ ' . $this -> getErrorFile(TRUE) . ' ]<br/>' . '<font color="whitesmoke">Message: </font>' . htmlspecialchars($this -> getErrorMessage()) . '</br>' . '<font color="whitesmoke">Time: </font>' . date('Y/m/d H:i:s T') . '</div>' . '</body>';
	}

	public function renderJson() {
		$render = \Base::getConfig() -> getExceptionJsonRender();
		if (is_callable($render)) {
			return $render($this);
		}
		return '';
	}

	public function setHttpHeader() {
		if (!\Core::isCli()) {
			header($this -> httpStatusLine);
		}
		return $this;
	}

	public function __toString() {
		return $this -> render(FALSE, TRUE);
	}

}

class Xs_Exception_404 extends Xs_Exception {
	protected $exceptionName = 'Xs_Exception_404', $httpStatusLine = 'HTTP/1.0 404 Not Found';
}

class Xs_Exception_500 extends Xs_Exception {
	protected $exceptionName = 'Xs_Exception_500', $httpStatusLine = 'HTTP/1.0 500 Internal Server Error';
}

class Xs_Exception_Database extends Xs_Exception {
	protected $exceptionName = 'Xs_Exception_Database', $httpStatusLine = 'HTTP/1.0 500 Internal Server Error';
}
?>