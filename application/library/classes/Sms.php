<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");

/*
 * 手机短信
 */
class Sms {
	//类型
	private $sms_type;
	//用户ID，企业ID
	private $sms_user_id;
	//账户
	private $sms_acount;
	//密码
	private $sms_password;
	//签名
	private $sms_sign;
	//签名位置不为空表示尾部
	private $sms_sign_location;
	//反馈信息
	private $sms_log = '';

	public function __construct() {
		$this -> sms_type = C('sms_type');
		$this -> sms_user_id = C('sms_user_id');
		$this -> sms_acount = C('sms_acount');
		$this -> sms_password = \Core::decrypt(C('sms_password'));
		$this -> sms_sign = C('sms_sign');
		$this -> sms_sign_location = C('sms_sign_location');
	}

	//参数设置
	public function setSms($sms_type, $sms_user_id, $sms_acount, $sms_password, $sms_sign, $sms_sign_location) {
		$this -> sms_type = $sms_type;
		$this -> sms_user_id = $sms_user_id;
		$this -> sms_acount = $sms_acount;
		$this -> sms_password = $sms_password;
		$this -> sms_sign = $sms_sign;
		$this -> sms_sign_location = $sms_sign_location;
	}

	//发送手机短信
	public function sendSms($mobile, $message) {
		$method = 'send_' . $this -> sms_type;
		if (!method_exists($this, $method)) {
			return false;
		}
		return $this -> $method($mobile, $message);
	}

	//沃动科技
	public function send_wdkj($mobile, $message) {
		$url = "http://115.29.242.32:8888/sms.aspx?action=send";
		if (!$mobile || !$message || !$this -> sms_user_id || !$this -> sms_acount || !$this -> sms_password) {
			return false;
		}
		if (is_array($mobile)) {
			$mobile = implode(",", $mobile);
		}
		$mobile = str_replace(';', ',', $mobile);
		$mobile = urlencode($mobile);
		if ($this -> sms_sign_location) {
			$message = $message . $this -> sms_sign;
		} else {
			$message = $this -> sms_sign . $message;
		}
		$content = urlencode($message);
		//建立数据数组
		$data = array();
		$data['userid'] = $this -> sms_user_id;
		$data['account'] = $this -> sms_acount;
		$data['password'] = $this -> sms_password;
		$data['mobile'] = $mobile;
		$data['content'] = $content;

		$http = \Core::library('Http');
		$res = $http -> post($url, $data);

		$doc = new DOMDocument();
		$doc -> loadXML($res);
		$statenode = $doc -> getElementsByTagName('returnstatus');
		$msgnode = $doc -> getElementsByTagName('message');
		$pointnode = $doc -> getElementsByTagName('remainpoint');
		$countsnode = $doc -> getElementsByTagName('successCounts');
		$taskidnode = $doc -> getElementsByTagName('taskID');

		if (!is_null($msgnode)) {
			$this -> sms_log .= '服务商反馈信息：' . $msgnode -> item(0) -> nodeValue . '<br>';
		}
		if (!is_null($pointnode)) {
			$this -> sms_log .= '短信余额：' . $pointnode -> item(0) -> nodeValue . '<br>';
		}
		if (!is_null($countsnode)) {
			$this -> sms_log .= '发送成功条数：' . $countsnode -> item(0) -> nodeValue . '<br>';
		}
		if (!is_null($taskidnode)) {
			$this -> sms_log .= '发送任务ID：' . $taskidnode -> item(0) -> nodeValue . '<br>';
		}
			$this -> sms_log .= '发送内容：' . $message. '<br>';
			$this -> sms_log .= '发送手机号：' . $mobile. '<br>';
		if (is_null($statenode)) {
			return false;
		}
		$ret = strtolower($statenode -> item(0) -> nodeValue);
		$ok = $ret == "success";
		if ($ok) {
			return true;
		}
		return false;
	}

	//获取异常信息
	public function getSmsLog() {
		return $this -> sms_log;
	}

}
?>