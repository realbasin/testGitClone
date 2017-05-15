<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_wechat_setting extends controller_sysBase {

	public function before($method, $args) {
		\Language::read('wechat');
	}

	public function do_index() {
		$this -> do_access();
	}

	//基础设置
	public function do_access() {
		if (chksubmit()) {
			$wechat_app_id = \Core::post('wechat_app_id');
			$wechat_app_secret = \Core::post('wechat_app_secret');
			$wechat_aes_key = \Core::post('wechat_aes_key');
			$wechat_token = \Core::post('wechat_token');

			$update[] = array('name' => 'wechat_app_id', 'value' => $wechat_app_id);
			$update[] = array('name' => 'wechat_app_secret', 'value' => $wechat_app_secret);
			$update[] = array('name' => 'wechat_aes_key', 'value' => $wechat_aes_key);
			$update[] = array('name' => 'wechat_token', 'value' => $wechat_token);

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,wechat_access_setting'), 'update');
			\Core::message(\Core::L('update,wechat_access_setting,success'), \Core::getUrl('wechat_setting', 'access', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load("wechat_accessSetting");
	}

	//支付设置
	public function do_payment() {
		if (chksubmit()) {
			$wechat_mch_id = \Core::post('wechat_mch_id');
			$wechat_partner_key = \Core::post('wechat_partner_key');
			$wechat_ssl_cer = \Core::post('filecer');
			$wechat_ssl_key = \Core::post('filekey');

			if ($wechat_ssl_cer) {
				$upload = \Core::library("FileUpload");
				$upload -> setInputFileName("wechat_ssl_cer");
				$upload -> setFileName(getRandString());
				$upload -> setSavePath(STORAGE_PATH . 'certificate' . DIRECTORY_SEPARATOR);
				$upload -> setExtensions(array('pem'));
				$upload -> setFileSize(2048000000);
				$upload_result_cer = $upload -> upload();
				$upload_data_cer = $upload -> getUploadedData();
				$upload_err_cer = $upload -> getErrorMessage();

				if ($upload_result_cer) {
					if (is_array($upload_data_cer) && !empty($upload_data_cer)) {
						$wechat_ssl_cer = $upload_data_cer[0]['new_name'];
					} else {
						//上传错误
						\Core::message(\Core::L('wechat_ssl_cer_upload_error'), adminUrl('wechat_setting', 'payment'), 'tip', 3, 'message');
					}
				} else {
					//上传失败
					$msgs = '';
					if (is_array($upload_err_cer) && !empty($upload_err_cer)) {
						foreach ($upload_err_cer as $msg) {
							$msgs .= $msg . ' ';
						}
					} else {
						$msgs = \Core::L('wechat_ssl_cer_upload_error');
					}
					\Core::message($msgs, adminUrl('wechat_setting', 'payment'), 'fail', 3, 'message');
				}
			}

			if ($wechat_ssl_key) {
				$upload1 = \Core::library("FileUpload");
				$upload1 -> setInputFileName("wechat_ssl_key");
				$upload1 -> setFileName(getRandString());
				$upload1 -> setSavePath(STORAGE_PATH . 'certificate' . DIRECTORY_SEPARATOR);
				$upload1 -> setExtensions(array('pem'));
				$upload1 -> setFileSize(2048000000);
				$upload_result_key = $upload1 -> upload();
				$upload_data_key = $upload1 -> getUploadedData();
				$upload_err_key = $upload1 -> getErrorMessage();
				
				if ($upload_result_key) {
					if (is_array($upload_data_key) && !empty($upload_data_key)) {
						$wechat_ssl_key = $upload_data_key[0]['new_name'];
					} else {
						//上传错误
						\Core::message(\Core::L('wechat_ssl_key_upload_error'), adminUrl('wechat_setting', 'payment'), 'tip', 3, 'message');
					}
				} else {
					//上传失败
					$msgs = '';
					if (is_array($upload_err_key) && !empty($upload_err_key)) {
						foreach ($upload_err_key as $msg) {
							$msgs .= $msg . ' ';
						}
					} else {
						$msgs = \Core::L('wechat_ssl_key_upload_error');
					}
					\Core::message($msgs, adminUrl('wechat_setting', 'payment'), 'fail', 3, 'message');
				}
			}

			$update[] = array('name' => 'wechat_mch_id', 'value' => $wechat_mch_id);
			$update[] = array('name' => 'wechat_partner_key', 'value' => $wechat_partner_key);
			if($wechat_ssl_cer){
				$update[] = array('name' => 'wechat_ssl_cer', 'value' => $wechat_ssl_cer);
			}
			if($wechat_ssl_key){
				$update[] = array('name' => 'wechat_ssl_key', 'value' => $wechat_ssl_key);
			}

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,wechat_payment_setting'), 'update');
			\Core::message(\Core::L('update,wechat_payment_setting,success'), \Core::getUrl('wechat_setting', 'payment', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load("wechat_paymentSetting");
	}

}
