<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
class Route {
	private $found = false;
	private $controller, $method, $args, $hmvcModuleName;
	public function getHmvcModuleName() {
		return $this -> hmvcModuleName;
	}

	public function setHmvcModuleName($hmvcModuleName) {
		$this -> hmvcModuleName = $hmvcModuleName;
		return $this;
	}

	public function found() {
		return $this -> found;
	}

	public function setFound($found) {
		$this -> found = $found;
		return $this;
	}

	public function getController() {
		return $this -> controller;
	}

	public function getMethod() {
		return $this -> method;
	}

	public function getControllerShort() {
		return preg_replace('/^' . \Core::config() -> getControllerDirName() . '_/', '', $this -> getController());
	}

	public function getMethodShort() {
		return preg_replace('/^' . \Core::config() -> getMethodPrefix() . '/', '', $this -> getMethod());
	}

	public function getArgs() {
		return $this -> args;
	}

	public function __construct() {
		$this -> args = array();
	}

	public function setController($controller) {
		$this -> controller = $controller;
		return $this;
	}

	public function setMethod($method) {
		$this -> method = $method;
		return $this;
	}

	public function setArgs(array $args) {
		$this -> args = $args;
		return $this;
	}

}
?>