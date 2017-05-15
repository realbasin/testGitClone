<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_captcha extends Controller {
	
	public function do_index() {
		\Core::sessionSet('captcha_code', \Core::library("Captcha")->create());
	}
}
?>