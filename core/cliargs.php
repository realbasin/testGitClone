<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
class CliArgs {
	private $args;
	public function __construct() {
		$this->args = \Core::getOpt();
	}
	public function get($key = null, $default = null) {
		if (empty($key)) {
			return $this->args;
		}
		return \Core::arrayGet($this->args, $key, $default);
	}
}
?>