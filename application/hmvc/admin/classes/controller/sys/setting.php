<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_sys_setting extends controller_sysBase {

	private $uploadTabs = array( array('ctl' => 'sys_setting', 'act' => 'upload', 'lang' => 'upload_local_setting'), array('ctl' => 'sys_setting', 'act' => 'upload_qiniu', 'lang' => 'upload_qiniu_setting'), array('ctl' => 'sys_setting', 'act' => 'upload_oss', 'lang' => 'upload_oss_setting'), array('ctl' => 'sys_setting', 'act' => 'upload_upyun', 'lang' => 'upload_upyun_setting'), );
	private $watermarkTaps = array( array('ctl' => 'sys_setting', 'act' => 'watermark', 'lang' => 'watermark_setting'), array('ctl' => 'sys_setting', 'act' => 'watermark_font', 'lang' => 'watermark_font_setting'), );
	private $loginTaps = array( array('ctl' => 'sys_setting', 'act' => 'login_qq', 'lang' => 'login_qq_setting'), array('ctl' => 'sys_setting', 'act' => 'login_sina', 'lang' => 'login_sina_setting'), array('ctl' => 'sys_setting', 'act' => 'login_wechat', 'lang' => 'login_wechat_setting'), array('ctl' => 'sys_setting', 'act' => 'login_sms', 'lang' => 'login_sms_setting'), );

	public function before($method, $args) {
		\Language::read('setting');
	}

	public function do_index() {
		$this -> do_base();
	}

	//基础设置
	public function do_base() {
		if (chksubmit()) {
			$site_name = \Core::post('site_name');
			$site_icp = \Core::post('site_icp');
			$url_model=\Core::post('url_model');
			$time_zone = \Core::post('time_zone');
			$statistics_code = \Core::post('statistics_code');
			$sys_log = \Core::post('sys_log');
			$maintain_mode = \Core::post('maintain_mode');
			$maintain_mode_white = \Core::post('maintain_mode_white');
			$maintain_mode_tip = \Core::post('maintain_mode_tip');

			$update[] = array('name' => 'site_name', 'value' => $site_name);
			$update[] = array('name' => 'site_icp', 'value' => $site_icp);
			$update[] = array('name' => 'url_model', 'value' => $url_model);
			$update[] = array('name' => 'time_zone', 'value' => $time_zone);
			$update[] = array('name' => 'statistics_code', 'value' => $statistics_code);
			$update[] = array('name' => 'sys_log', 'value' => $sys_log);
			$update[] = array('name' => 'maintain_mode', 'value' => $maintain_mode);
			$update[] = array('name' => 'maintain_mode_white', 'value' => $maintain_mode_white);
			$update[] = array('name' => 'maintain_mode_tip', 'value' => $maintain_mode_tip);

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,base_setting'), 'update');
			\Core::message(\Core::L('update,base_setting,success'), \Core::getUrl('sys_setting', 'base', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load("sys_baseSetting");
	}

	//本地上传设置
	public function do_upload() {
		if (chksubmit()) {
			$upload_driver = \Core::post('upload_driver');
			$upload_size = \Core::post('upload_size');
			$upload_ext = \Core::post('upload_ext');
			$update[] = array('name' => 'upload_driver', 'value' => $upload_driver);
			$update[] = array('name' => 'upload_size', 'value' => $upload_size);
			$update[] = array('name' => 'upload_ext', 'value' => $upload_ext);
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,upload_setting'), 'update');
			\Core::message(\Core::L('update,upload_setting,success'), \Core::getUrl('sys_setting', 'upload', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load("sys_uploadSetting", $this -> createTaps($this -> uploadTabs, 'upload'));
	}

	//七牛上传配置
	public function do_upload_qiniu() {
		if (chksubmit()) {
			$upload_driver = \Core::post('upload_driver');
			$bucket = \Core::post('bucket');
			$accesskey = \Core::post('accesskey');
			$secretkey = \Core::post('secretkey');
			$data = array('bucket' => $bucket, 'accesskey' => $accesskey, 'secretkey' => $secretkey);
			$update[] = array('name' => 'upload_driver', 'value' => $upload_driver);
			$update[] = array('name' => 'upload_qiniu', 'value' => serialize($data));
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,upload_qiniu_setting'), 'update');
			\Core::message(\Core::L('update,upload_qiniu_setting,success'), \Core::getUrl('sys_setting', 'upload_qiniu', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('upload_qiniu')) {
			\Core::view() -> set(unserialize(C('upload_qiniu')));
		}
		\Core::view() -> load("sys_uploadQiniuSetting", $this -> createTaps($this -> uploadTabs, 'upload_qiniu'));
	}

	//OSS上传配置
	public function do_upload_oss() {
		if (chksubmit()) {
			$upload_driver = \Core::post('upload_driver');
			$bucket = \Core::post('bucket');
			$accesskey = \Core::post('accesskey');
			$secretkey = \Core::post('secretkey');
			$endpoint = \Core::post('endpoint');
			$data = array('bucket' => $bucket, 'accesskey' => $accesskey, 'secretkey' => $secretkey, 'endpoint' => $endpoint);
			$update[] = array('name' => 'upload_driver', 'value' => $upload_driver);
			$update[] = array('name' => 'upload_oss', 'value' => serialize($data));
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,upload_oss_setting'), 'update');
			\Core::message(\Core::L('update,upload_oss_setting,success'), \Core::getUrl('sys_setting', 'upload_oss', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('upload_oss')) {
			\Core::view() -> set(unserialize(C('upload_oss')));
		}
		\Core::view() -> load("sys_uploadOssSetting", $this -> createTaps($this -> uploadTabs, 'upload_oss'));
	}

	//UPYUN上传配置
	public function do_upload_upyun() {
		if (chksubmit()) {
			$upload_driver = \Core::post('upload_driver');
			$bucket = \Core::post('bucket');
			$username = \Core::post('username');
			$password = \Core::post('password');
			$endpoint = \Core::post('endpoint');
			$data = array('bucket' => $bucket, 'username' => $username, 'password' => $password, 'endpoint' => $endpoint);
			$update[] = array('name' => 'upload_driver', 'value' => $upload_driver);
			$update[] = array('name' => 'upload_upyun', 'value' => serialize($data));
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,upload_oss_setting'), 'update');
			\Core::message(\Core::L('update,upload_upyun_setting,success'), \Core::getUrl('sys_setting', 'upload_upyun', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('upload_upyun')) {
			\Core::view() -> set(unserialize(C('upload_upyun')));
		}
		\Core::view() -> load("sys_uploadUpyunSetting", $this -> createTaps($this -> uploadTabs, 'upload_upyun'));
	}

	//图片水印
	public function do_watermark() {
		if (chksubmit()) {
			$upload_watermark_img = \Core::post('upload_watermark_img');
			$upload_watermark_text = \Core::post('upload_watermark_text');
			$update[] = array('name' => 'upload_watermark_img', 'value' => $upload_watermark_img);
			$update[] = array('name' => 'upload_watermark_text', 'value' => $upload_watermark_text);
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,watermark_setting'), 'update');
			\Core::message(\Core::L('update,watermark_setting,success'), \Core::getUrl('sys_setting', 'watermark', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}

		\Core::view() -> load("sys_watermarkSetting", $this -> createTaps($this -> watermarkTaps, 'watermark'));
	}

	//水印字体
	public function do_watermark_font() {
		if (chksubmit()) {
			\Language::read("upload");

			$font_name = \Core::post('upload_watermark_text_font_name');
			//初始化上传
			$upload = \Core::library("FileUpload");
			$upload -> setInputFileName("watermark_text_font");
			$upload -> setFileName(getRandString());
			$upload -> setSavePath(DZ_WATERMARK_FONT_PATH);
			$upload -> setExtensions(array('ttf'));
			$upload -> setFileSize(2048000000);

			$upload_result = $upload -> upload();
			$upload_data = $upload -> getUploadedData();
			$upload_err = $upload -> getErrorMessage();

			if ($upload_result) {
				//保存到数据库
				if (is_array($upload_data) && !empty($upload_data)) {
					$fontpath = $upload_data[0]['new_name'];
					$fontdao = \Core::dao('sys_watermark_font');
					if ($fontdao -> insert(array('font_name' => $font_name, 'font_path' => $fontpath))) {
						$this -> log(\Core::L('add,upload_watermark_text_font') . '[' . $font_name . ']', 'add');
						\Core::cache() -> delete('watermark_fonts');
						\Core::message(\Core::L('upload_watermark_font_success'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
					} else {
						\Core::message(\Core::L('upload_watermark_font_save_error'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'tip', 3, 'message');
					}
				} else {
					//数据错误
					\Core::message(\Core::L('upload_watermark_font_data_error'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'tip', 3, 'message');
				}
			} else {
				//上传失败
				$msgs = '';
				if (is_array($upload_err) && !empty($upload_err)) {
					foreach ($upload_err as $msg) {
						$msgs .= $msg . ' ';
					}
				} else {
					$msgs = \Core::L('upload_watermark_font_error');
				}
				\Core::message($msgs, \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
			}
		}
		//获取字体
		$fonts = getWatermarkFonts();

		\Core::view() -> set('fonts', $fonts) -> load("sys_watermarkFont", $this -> createTaps($this -> watermarkTaps, 'watermark_font'));
	}

	//删除水印字体
	public function do_watermark_font_del() {
		$font_id = \Core::get("id");
		if (!$font_id || !is_numeric($font_id)) {
			\Core::message(\Core::L('parameter_error'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
		}
		$fontdao = \Core::dao('sys_watermark_font');
		$font = $fontdao -> find($font_id);
		if (!$font) {
			\Core::message(\Core::L('delete_watermark_font_error_noexsits'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
		}
		if ($font['sys']) {
			\Core::message(\Core::L('delete_watermark_font_error_sys'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
		}

		if ($fontdao -> delete($font_id)) {
			\Core::cache() -> delete('watermark_fonts');
			$this -> log(\Core::L('delete,upload_watermark_text_font') . '[' . $font['font_name'] . ']', 'delete');
			\Core::cache() -> delete('watermark_fonts');
			@unlink(DZ_WATERMARK_FONT_PATH . $font['font_path']);
			\Core::message(\Core::L('delete_watermark_font_sucess'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		} else {
			\Core::message(\Core::L('delete_watermark_font_error'), \Core::getUrl('sys_setting', 'watermark_font', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
		}
	}

	//邮件设置
	public function do_email() {
		if (chksubmit()) {
			$smtp_server = \Core::post('smtp_server');
			$smtp_port = \Core::post('smtp_port');
			$smtp_protocol = \Core::post('smtp_protocol');
			$smtp_send = \Core::post('smtp_send');
			$smtp_user = \Core::post('smtp_user');
			$smtp_password = \Core::post('smtp_password');

			$update[] = array('name' => 'smtp_server', 'value' => $smtp_server);
			$update[] = array('name' => 'smtp_port', 'value' => $smtp_port);
			$update[] = array('name' => 'smtp_protocol', 'value' => $smtp_protocol);
			$update[] = array('name' => 'smtp_send', 'value' => $smtp_send);
			$update[] = array('name' => 'smtp_user', 'value' => $smtp_user);
			$update[] = array('name' => 'smtp_password', 'value' => \Core::encrypt($smtp_password));
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,email_setting'), 'update');
			\Core::message(\Core::L('update,email_setting,success'), \Core::getUrl('sys_setting', 'email', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load('sys_emailSetting');
	}

	//邮件测试
	public function do_email_test() {
		$smtp_server = \Core::postGet('smtp_server');
		$smtp_port = \Core::postGet('smtp_port');
		$smtp_protocol = \Core::postGet('smtp_protocol');
		$smtp_send = \Core::postGet('smtp_send');
		$smtp_user = \Core::postGet('smtp_user');
		$smtp_password = \Core::postGet('smtp_password');
		$smtp_test = \Core::postGet('smtp_test');

		$email = \Core::library('Email');
		$email -> setServer($smtp_server, $smtp_port);
		if ($smtp_protocol) {
			$email -> setProtocol($email::TLS);
		} else {
			$email -> setProtocol('');
		}
		$email -> setLogin($smtp_user, $smtp_password);
		$email -> setFrom($smtp_send);
		$email -> setSubject(sprintf(\Core::L('smtp_test_subject'), C('site_name')));
		$email -> setMessage(sprintf(\Core::L('smtp_test_message'), C('site_name')));
		$email -> addTo($smtp_test);
		if ($email -> send()) {
			showJSON(200, \Core::L('smtp_test_success'));
		} else {
			showJSON(0, \Core::L('smtp_test_error'));
		}

	}

	//短信设置
	public function do_sms() {
		if (chksubmit()) {
			$sms_type = \Core::post('sms_type');
			$sms_user_id = \Core::post('sms_user_id');
			$sms_acount = \Core::post('sms_acount');
			$sms_password = \Core::post('sms_password');
			$sms_sign = \Core::post('sms_sign');
			$sms_sign_location = \Core::post('sms_sign_location');

			$update[] = array('name' => 'sms_type', 'value' => $sms_type);
			$update[] = array('name' => 'sms_user_id', 'value' => $sms_user_id);
			$update[] = array('name' => 'sms_acount', 'value' => $sms_acount);
			$update[] = array('name' => 'sms_password', 'value' => \Core::encrypt($sms_password));
			$update[] = array('name' => 'sms_sign', 'value' => $sms_sign);
			$update[] = array('name' => 'sms_sign_location', 'value' => $sms_sign_location);
			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,sms_setting'), 'update');
			\Core::message(\Core::L('update,sms_setting,success'), \Core::getUrl('sys_setting', 'sms', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load('sys_smsSetting');
	}

	//短信测试
	public function do_sms_test() {
		$sms_type = \Core::postGet('sms_type');
		$sms_user_id = \Core::postGet('sms_user_id');
		$sms_acount = \Core::postGet('sms_acount');
		$sms_password = \Core::postGet('sms_password');
		$sms_sign = \Core::postGet('sms_sign');
		$sms_sign_location = \Core::postGet('sms_sign_location');
		$sms_test = \Core::postGet('sms_test');

		$sms = \Core::library('Sms');
		$sms -> setSms($sms_type, $sms_user_id, $sms_acount, $sms_password, $sms_sign, $sms_sign_location);

		if ($sms -> sendSms($sms_test, \Core::L('sms_test_text'))) {
			showJSON(200, $sms -> getSmsLog());
		} else {
			showJSON(0, $sms -> getSmsLog());
		}
	}

	//前端登录设置
	public function do_login() {
		$this -> do_login_qq();
	}

	//QQ登录
	public function do_login_qq() {
		if (chksubmit()) {
			$login_qq = \Core::post("login_qq");
			$appid = \Core::post("appid");
			$appkey = \Core::post("appkey");
			$metacode = \Core::post("metacode");

			$update[] = array('name' => 'login_qq', 'value' => $login_qq);
			$update[] = array('name' => 'login_qq_setting', 'value' => serialize(array('appid' => $appid, 'appkey' => $appkey, 'metacode' => $metacode)));

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,login_qq_setting'), 'update');
			\Core::message(\Core::L('update,login_qq_setting,success'), \Core::getUrl('sys_setting', 'login_qq', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('login_qq_setting')) {
			\Core::view() -> set(unserialize(C('login_qq_setting')));
		}
		\Core::view() -> load('sys_loginQqSetting', $this -> createTaps($this -> loginTaps, 'login_qq'));
	}

	//新浪微博登录
	public function do_login_sina() {
		if (chksubmit()) {
			$login_sina = \Core::post("login_sina");
			$appid = \Core::post("appid");
			$appkey = \Core::post("appkey");
			$metacode = \Core::post("metacode");

			$update[] = array('name' => 'login_sina', 'value' => $login_sina);
			$update[] = array('name' => 'login_sina_setting', 'value' => serialize(array('appid' => $appid, 'appkey' => $appkey, 'metacode' => $metacode)));

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,login_sina_setting'), 'update');
			\Core::message(\Core::L('update,login_sina_setting,success'), \Core::getUrl('sys_setting', 'login_sina', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('login_sina_setting')) {
			\Core::view() -> set(unserialize(C('login_sina_setting')));
		}
		\Core::view() -> load('sys_loginSinaSetting', $this -> createTaps($this -> loginTaps, 'login_sina'));
	}

	//微信登录
	public function do_login_wechat() {
		if (chksubmit()) {
			$login_wechat = \Core::post("login_wechat");
			$appid = \Core::post("appid");
			$appkey = \Core::post("appkey");

			$update[] = array('name' => 'login_wechat', 'value' => $login_wechat);
			$update[] = array('name' => 'login_wechat_setting', 'value' => serialize(array('appid' => $appid, 'appkey' => $appkey)));

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,login_wechat_setting'), 'update');
			\Core::message(\Core::L('update,login_wechat_setting,success'), \Core::getUrl('sys_setting', 'login_wechat', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		if (C('login_wechat_setting')) {
			\Core::view() -> set(unserialize(C('login_wechat_setting')));
		}
		\Core::view() -> load('sys_loginWechatSetting', $this -> createTaps($this -> loginTaps, 'login_wechat'));
	}

	//短信登录
	public function do_login_sms() {
		if (chksubmit()) {
			$login_sms = \Core::post("login_sms");

			$update[] = array('name' => 'login_sms', 'value' => $login_sms);

			$setting = \Core::dao('sys_setting');
			$setting -> updateBatch($update, 'name');
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('update,login_sms_setting'), 'update');
			\Core::message(\Core::L('update,login_sms_setting,success'), \Core::getUrl('sys_setting', 'login_sms', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
		}
		\Core::view() -> load('sys_loginSmsSetting', $this -> createTaps($this -> loginTaps, 'login_sms'));
	}

	//管理员权限设置
	public function do_permission() {
		$this -> do_permission_list();
	}

	//管理员权限列表
	public function do_permission_list() {
		\Core::view() -> set('datalist', \Core::dao('sys_admin_auth') -> findAll()) -> load('sys_permissionList');
	}

	//管理员权限增加
	public function do_permission_add() {
		if (chksubmit()) {
			$auth = \Core::dao('sys_admin_auth');
			$gname = \Core::post('gname');
			$info = \Core::post('info');
			$permission = \Core::post('permission');
			if ($permission && is_array($permission)) {
				$permission = preg_replace("/,/", "&", $permission);
				$permission = implode('|', $permission);
				$permission = \Core::encrypt($permission);
			}
			if (!$gname) {
				\Core::message(\Core::L('auth_name_null'), adminUrl('sys_setting', 'permission_add'), 'tip', 3, 'message');
			}
			if (!$info) {
				\Core::message(\Core::L('auth_info_null'), adminUrl('sys_setting', 'permission_add'), 'tip', 3, 'message');
			}
			if (!$permission) {
				\Core::message(\Core::L('auth_permission_null'), adminUrl('sys_setting', 'permission_add'), 'tip', 3, 'message');
			}
			if ($auth -> find(array('gname' => $gname))) {
				\Core::message(\Core::L('auth_name_repeat'), adminUrl('sys_setting', 'permission_add'), 'tip', 3, 'message');
			}
			if ($auth -> insert(array('gname' => $gname, 'info' => $info, 'permission' => $permission))) {
				$this -> log(\Core::L('add,permission_setting') . '[' . $gname . ']', 'add');
				\Core::message(\Core::L('add,permission_setting,success'), adminUrl('sys_setting', 'permission_list'), 'suc', 3, 'message');
			} else {
				\Core::message(\Core::L('add,permission_setting,fail'), adminUrl('sys_setting', 'permission_add'), 'fail', 3, 'message');
			}
		}
		\Core::view() -> set('authlist', $this -> get_nav()) -> load('sys_permissionAdd');
	}

	public function do_permission_edit() {
		$gid = \Core::get("gid");
		if (!$gid || !is_numeric($gid)) {
			\Core::message(\Core::L('parameter_error'), adminUrl('sys_setting', 'permission'), 'fail', 3, 'message');
		}
		if (chksubmit()) {
			$auth = \Core::dao('sys_admin_auth');
			$gname = \Core::post('gname');
			$info = \Core::post('info');
			$permission = \Core::post('permission');
			if ($permission && is_array($permission)) {
				$permission = preg_replace("/,/", "&", $permission);
				$permission = implode('|', $permission);
				$permission = \Core::encrypt($permission);
			}
			if (!$gname) {
				\Core::message(\Core::L('auth_name_null'), adminUrl('sys_setting', 'permission_edit', array('gid' => $gid)), 'tip', 3, 'message');
			}
			if (!$info) {
				\Core::message(\Core::L('auth_info_null'), adminUrl('sys_setting', 'permission_edit', array('gid' => $gid)), 'tip', 3, 'message');
			}
			if (!$permission) {
				\Core::message(\Core::L('auth_permission_null'), adminUrl('sys_setting', 'permission_edit', array('gid' => $gid)), 'tip', 3, 'message');
			}
			if ($auth -> find(array('gname' => $gname, 'gid <>' => $gid))) {
				\Core::message(\Core::L('auth_name_repeat'), adminUrl('sys_setting', 'permission_edit', array('gid' => $gid)), 'tip', 3, 'message');
			}
			if ($auth -> update(array('gname' => $gname, 'info' => $info, 'permission' => $permission), $gid)) {
				$this -> log(\Core::L('edit,permission_setting') . '[gid:' . $gid . ',gname:' . $gname . ']', 'edit');
				\Core::message(\Core::L('update,permission_setting,success'), adminUrl('sys_setting', 'permission_list'), 'suc', 3, 'message');
			} else {
				\Core::message(\Core::L('update,permission_setting,fail'), adminUrl('sys_setting', 'permission_edit', array('gid' => $gid)), 'fail', 3, 'message');
			}
		}
		$dao = \Core::dao('sys_admin_auth');
		$auth = $dao -> find($gid);
		if (!$auth) {
			\Core::message(\Core::L('data_empty'), adminUrl('sys_setting', 'permission'), 'fail', 3, 'message');
		}
		$auth['permission'] = explode("|", preg_replace("/&/", ",", \Core::decrypt($auth['permission'])));
		\Core::view() -> set('authlist', $this -> get_nav()) -> load('sys_permissionEdit', $auth);
	}

	public function do_permission_del() {
		$gid = \Core::get('gid');
		if (!$gid || !is_numeric($gid)) {
			\Core::message(\Core::L('parameter_error'), adminUrl('sys_setting', 'permission'), 'fail', 3, 'message');
		}
		//查看是否有管理员使用该权限配置
		if (\Core::dao('sys_admin_admin') -> find(array('admin_gid' => $gid))) {
			\Core::message(\Core::L('auth_using'), adminUrl('sys_setting', 'permission'), 'fail', 3, 'message');
		}
		//删除
		if (\Core::dao('sys_admin_auth') -> delete($gid)) {
			$this -> log(\Core::L('delete,permission_setting') . '[gid:' . $gid . ']', 'edit');
			\Core::message(\Core::L('delete,permission_setting,success'), adminUrl('sys_setting', 'permission'), 'suc', 3, 'message');
		}
		\Core::message(\Core::L('delete,permission_setting,fail'), adminUrl('sys_setting', 'permission'), 'fail', 3, 'message');
	}

	//管理员设置
	public function do_admin() {
		$this -> do_admin_list();
	}

	//管理员列表
	public function do_admin_list() {
		$admin = \Core::db() -> select('*') -> from('adminuser') -> join('admin_auth', 'adminuser.admin_gid=admin_auth.gid', 'left') -> execute() -> rows();
		\Core::view() -> set('datalist', $admin) -> load('sys_adminList');
	}

	//增加管理员
	public function do_admin_add() {
		if (chksubmit()) {
			$admin_name = \Core::post('admin_name');
			$admin_password = \Core::post('admin_password');
			$admin_gid = \Core::post('admin_gid');

			$dao_admin = \Core::dao('sys_admin_admin');
			//检查重名
			if ($dao_admin -> find(array('admin_name' => $admin_name))) {
				\Core::message(\Core::L('admin_name_repeat'), adminUrl('sys_setting', 'admin_add'), 'tip', 3, 'message');
			}
			//加入数据库
			if ($dao_admin -> insert(array('admin_name' => $admin_name, 'admin_password' => md5($admin_password), 'admin_gid' => $admin_gid))) {
				$this -> log(\Core::L('add,admin') . '[' . $admin_name . ']', 'add');
				\Core::message(\Core::L('add,admin,success'), adminUrl('sys_setting', 'admin'), 'suc', 3, 'message');
			}
			\Core::message(\Core::L('add,admin,fail'), adminUrl('sys_setting', 'admin'), 'fail', 3, 'message');
		}
		\Core::view() -> set('authlist', \Core::dao('sys_admin_auth') -> findAll()) -> load('sys_adminAdd');
	}

	//编辑管理员
	public function do_admin_edit() {
		$admin_id = \Core::get('admin_id');
		$dao_admin = \Core::dao('sys_admin_admin');
		if (!$admin_id || !is_numeric($admin_id)) {
			\Core::message(\Core::L('parameter_error'), adminUrl('sys_setting', 'admin'), 'fail', 3, 'message');
		}
		$admin = $dao_admin -> find($admin_id);
		if (!$admin) {
			\Core::message(\Core::L('data_empty'), adminUrl('sys_setting', 'admin'), 'fail', 3, 'message');
		}
		if (chksubmit()) {
			$admin_name = \Core::post('admin_name');
			$admin_password = \Core::post('admin_password');
			$admin_gid = \Core::post('admin_gid');
			//检查重名
			if ($dao_admin -> find(array('admin_name' => $admin_name, 'admin_id <>' => $admin_id))) {
				\Core::message(\Core::L('admin_name_repeat'), adminUrl('sys_setting', 'admin_add'), 'tip', 3, 'message');
			}
			//修改
			$update = array();
			$update['admin_name'] = $admin_name;
			$update['admin_gid'] = $admin_gid;
			if ($admin_password) {
				$update['admin_password'] = md5($admin_password);
			}
			if ($dao_admin -> update($update, $admin_id)) {
				$this -> log(\Core::L('edit,admin') . '[admin_id:' . $admin_id . ',admin_name:' . $admin_name . ']', 'edit');
				\Core::message(\Core::L('edit,admin,success'), adminUrl('sys_setting', 'admin'), 'suc', 3, 'message');
			}
			\Core::message(\Core::L('edit,admin,fail'), adminUrl('sys_setting', 'admin_edit', array('admin_id' => $admin_id)), 'fail', 3, 'message');
		}
		\Core::view() -> set('authlist', \Core::dao('sys_admin_auth') -> findAll()) -> load('sys_adminEdit', $admin);
	}

	//删除管理员
	public function do_admin_del() {
		$admin_id = \Core::get('admin_id');
		if (!$admin_id || !is_numeric($admin_id)) {
			\Core::message(\Core::L('parameter_error'), adminUrl('sys_setting', 'admin'), 'fail', 3, 'message');
		}
		if (\Core::dao('sys_admin_admin') -> delete($admin_id)) {
			$this -> log(\Core::L('delete,admin') . '[admin_id:' . $admin_id . ']', 'delete');
			\Core::message(\Core::L('delete,admin,success'), adminUrl('sys_setting', 'admin'), 'suc', 3, 'message');
		}
		\Core::message(\Core::L('delete,admin,fail'), adminUrl('sys_setting', 'admin'), 'fail', 3, 'message');
	}

	//管理员日志
	public function do_log() {
		\Core::view() -> load('sys_adminLogList');
	}

	//删除日志
	public function do_log_del() {
		$id = \Core::get("id");
		if (!$id) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$ids = explode(',', $id);
		if (\Core::dao('sys_admin_log') -> delete($ids)) {
			$this -> log(\Core::L('delete,log_setting') . '[id:' . $id . ']', 'delete');
			showJSON(200, '');
		}
		showJSON(1, \Core::L('parameter_error'));
	}

	//日志导出Excel
	//TODO:以后需要考虑到行数过大分页(10W行数据或以上才需要)
	public function do_log_export() {
		$id = \Core::get("id");
		$fields = "id,admin_name,content,ip,operatetime";
		$where = array();
		$orderby = array();
		if ($id) {
			$ids = explode(",", $id);
			$where['id'] = $ids;
		}
		//查询条件
		if (\Core::postGet('query') && in_array(\Core::postGet('qtype'), array('admin_name', 'content', 'ip'))) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//排序
		if (\Core::postGet('sortorder') && in_array(\Core::postGet('sortname'), array('admin_name', 'ip', 'operatetime'))) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		//Excel头部
		$header = array();
		$header['id'] = 'integer';
		$header[\Core::L('admin_log_admin_name')] = 'integer';
		$header[\Core::L('admin_log_content')] = 'string';
		$header[\Core::L('admin_log_admin_ip')] = 'string';
		$header[\Core::L('admin_log_time')] = 'datetime';

		$dao_log = \Core::dao('sys_admin_log');
		//Excel内容
		$data = $dao_log -> findAll($where, $orderby, null, $fields);
		foreach ($data as $k => $v) {
			$v['operatetime'] = date('Y-m-d H:i:s', $v['operatetime']);
			$data[$k] = $v;
		}
		//导出
		$export_range = $id ? $id : \Core::L('all');
		$this -> log(\Core::L('export,log_setting') . '[id:' . $export_range . ']', 'export');
		exportExcel(\Core::L('log_setting'), $header, $data);
	}

	//获取管理员日志
	public function do_log_json() {
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		$fields = "id,admin_name,content,ip,operatetime";
		$where = array();
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//查询条件
		if (\Core::postGet('query') && in_array(\Core::postGet('qtype'), array('admin_name', 'content', 'ip'))) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//排序
		if (\Core::postGet('sortorder') && in_array(\Core::postGet('sortname'), array('admin_name', 'ip', 'operatetime'))) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		$data = \Core::dao('sys_admin_log') -> getFlexPage($page, $pagesize, $fields, $where, $orderby);
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = "<a class='btn red' onclick='flexDelete({$v['id']})'><i class='fa fa-trash-o'></i> " . \Core::L('delete') . "</a>";
			$row['cell'][] = $v['admin_name'];
			$row['cell'][] = $v['content'];
			$row['cell'][] = $v['ip'];
			$row['cell'][] = date('Y-m-d H:i:s', $v['operatetime']);
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}

	//系统变量
	public function do_variablessys() {
		\Core::view() -> load('sys_variablesSysSetting');
	}
	
	public function do_variablessys_json(){
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		$fields = "name,value,info";
		$where = array('sys' => 1);
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//查询条件
		if (\Core::postGet('query') && in_array(\Core::postGet('qtype'), array('name', 'value', 'info'))) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//排序
		if (\Core::postGet('sortorder') && in_array(\Core::postGet('sortname'), array('name'))) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		$data = \Core::dao('sys_setting') -> getFlexPage($page, $pagesize, $fields, $where, $orderby);
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['name'];
			$row['cell'][] = $v['name'];
			$row['cell'][] = $v['value'];
			$row['cell'][] = $v['info'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}

	//自定义变量
	public function do_variables() {
		$this -> do_variables_list();
	}

	//自定义变量列表
	public function do_variables_list() {
		\Core::view() -> load('sys_variablesSetting');
	}

	//添加变量
	public function do_variables_add() {
		if (chksubmit()) {
			$name = \Core::post('name');
			$value = \Core::post('value');
			$info = \Core::post('info');
			$insert = array();
			$insert['name'] = $name;
			$insert['value'] = $value;
			$insert['info'] = $info;
			if (\Core::dao('sys_setting') -> insert($insert, false)) {
				\Core::cache() -> delete('setting_config');
				$this -> log(\Core::L('add,variables_setting') . '[name:' . $name . ']', 'add');
				\Core::message(\Core::L('add,variables_setting,success'), adminUrl('sys_setting', 'variables_add'), 'suc', 3, 'message');
			}
			\Core::message(\Core::L('add,variables_setting,fail'), adminUrl('sys_setting', 'variables_add'), 'fail', 3, 'message');
		}
		\Core::view() -> load('sys_variablesAdd');
	}

	//验证变量名称
	public function do_variables_add_verify() {
		$name = \Core::post('name');
		$param = \Core::post('param');
		if ($name && $param) {
			if (!\Core::dao('sys_setting') -> find($param)) {
				echo @json_encode(array('info' => \Core::L('verify_success'), 'status' => 'y'));
				exit ;
			}
		}
		echo @json_encode(array('info' => \Core::L('verify_fail'), 'status' => 'n'));
		exit ;
	}

	//编辑变量
	public function do_variables_edit() {
		$name = \Core::postGet('name');
		if (chksubmit()) {
			$value = \Core::post('value');
			$info = \Core::post('info');
			$update = array();
			$update['value'] = $value;
			$update['info'] = $info;
			if (\Core::dao('sys_setting') -> update($update, $name)) {
				\Core::cache() -> delete('setting_config');
				$this -> log(\Core::L('update,variables_setting') . '[name:' . $name . ']', 'update');
				\Core::message(\Core::L('update,variables_setting,success'), adminUrl('sys_setting', 'variables_edit', array('name' => $name)), 'suc', 3, 'message');
			}
			\Core::message(\Core::L('update,variables_setting,fail'), adminUrl('sys_setting', 'variables_edit', array('name' => $name)), 'fail', 3, 'message');
		}
		$data = \Core::dao('sys_setting') -> find($name);
		\Core::view() -> load('sys_variablesEdit', $data);
	}

	//删除变量
	public function do_variables_del() {
		$id = \Core::get("id");
		if (!$id) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$ids = explode(',', $id);
		if (\Core::dao('sys_setting') -> delete($ids)) {
			\Core::cache() -> delete('setting_config');
			$this -> log(\Core::L('delete,variables_setting') . '[name:' . $id . ']', 'delete');
			showJSON(200, \Core::L('delete,variables_setting,success'));
		}
		showJSON(1, \Core::L('parameter_error'));
	}

	//获取自定义变量JSON
	public function do_variables_json() {
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		$fields = "name,value,info";
		$where = array('sys <>' => 1);
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//查询条件
		if (\Core::postGet('query') && in_array(\Core::postGet('qtype'), array('name', 'value', 'info'))) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//排序
		if (\Core::postGet('sortorder') && in_array(\Core::postGet('sortname'), array('name'))) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		$data = \Core::dao('sys_setting') -> getFlexPage($page, $pagesize, $fields, $where, $orderby);
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['name'];
			$row['cell'][] = "<a class='btn red' onclick=\"flexDelete('{$v['name']}')\"><i class='fa fa-trash-o'></i> " . \Core::L('delete') . "</a> <a class='btn blue' onclick=\"flexEdit('{$v['name']}')\"><i class='fa fa-pencil-square-o'></i> " . \Core::L('edit') . "</a>";
			$row['cell'][] = $v['name'];
			$row['cell'][] = $v['value'];
			$row['cell'][] = $v['info'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}

	//缓存设置
	public function do_cache() {
		$this -> do_cache_list();
	}

	public function do_cache_list() {
		Language::read('cache');
		\Core::view() -> load('sys_cache');
	}

	//删除缓存
	public function do_cache_del() {
		$id = \Core::get("id");
		if (!$id) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$ids = explode(',', $id);
		foreach ($ids as $v) {
			\Core::cache() -> delete($v);
		}
		$this -> log(\Core::L('clear,cache_setting') . '[name:' . $id . ']', 'delete');
		showJSON(200, \Core::L('delete,cache_setting,success'));
	}

	public function do_cache_json() {
		Language::read('cache');
		$cacheList = array();
		$cache =
		require_once (BASE_PATH . DIRECTORY_SEPARATOR . 'cachelist.php');
		$cacheList['page'] = 1;
		$cacheList['total'] = count($cache);
		foreach ($cache as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = "<a class='btn red' onclick=\"flexDelete('{$v['id']}')\"><i class='fa fa-trash-o'></i> " . \Core::L('clear') . "</a>";
			$row['cell'][] = $v['cache_name'];
			$row['cell'][] = $v['cache_des'];
			$row['cell'][] = '';
			$cacheList['rows'][] = $row;
		}
		echo @json_encode($cacheList);
	}

	//数据库备份
	public function do_dbbackup() {
		$vol = \Core::get('vol');
		$bs_database = \Core::business('sys_database');
		if ($vol && is_numeric($vol)) {
			$bs_database -> backup($vol);
			$err = $bs_database -> getError();
			if ($err) {
				//备份出错
				\Core::message($err, adminUrl('sys_setting', 'dbbackup'), 'fail', 3, 'message');
			}
			$next = $bs_database -> getVolNext();
			if (!$next) {
				//备份完成
				if ($this -> hasPermission('sys_setting&dbrestore')) {
					\Core::message(\Core::L('backup_complete'), adminUrl('sys_setting', 'dbrestore'), 'suc', 3, 'message');
				}
				\Core::message(\Core::L('backup_complete'), adminUrl('sys_setting', 'dbbackup'), 'suc', 3, 'message');
			}
			//继续备份
			$vol++;
			\Core::message(sprintf(\Core::L('backup_doing'), $vol), adminUrl('sys_setting', 'dbbackup', array('vol' => $vol)), 'tip', 3, 'message', false);
		}

		$tableslist = $bs_database -> getPdoTables();
		if (chksubmit()) {
			$backup_name = \Core::post('backup_name');
			$backup_volume_size = \Core::post('backup_volume_size');
			$table = \Core::post('table');
			if (is_dir(STORAGE_PATH . 'backup' . DIRECTORY_SEPARATOR . $backup_name)) {
				\Core::message(\Core::L('backup_name_exsits'), adminUrl('sys_setting', 'dbbackup'), 'tip', 3, 'message');
			}
			if (!$table || !is_array($table)) {
				\Core::message(\Core::L('backup_tables_null'), adminUrl('sys_setting', 'dbbackup'), 'tip', 3, 'message');
			}
			$bs_database -> setVolumeSize($backup_volume_size) -> setTables($table) -> setName($backup_name);
			$this -> log(\Core::L('backup_database') . '[name:' . $backup_name . ']', 'backup');
			\Core::message(sprintf(\Core::L('backup_doing'), 1), adminUrl('sys_setting', 'dbbackup', array('vol' => 1)), 'tip', 3, 'message', false);
		}

		\Core::view() -> set('tableslist', $tableslist) -> load('sys_databaseBackup');
	}

	//数据库恢复
	public function do_dbrestore() {
		\Core::view() -> load('sys_databaseRestore');
	}

	public function do_dbrestore_restore() {
		$id = \Core::get("id");
		if (!$id) {
			\Core::message(\Core::L('parameter_error'), adminUrl('sys_setting', 'dbrestore'), 'tip', 3, 'message');
		}
		$path=STORAGE_PATH . 'backup' . DIRECTORY_SEPARATOR . $id;
		if (!is_dir($path)) {
			\Core::message(\Core::L('restore_dir_no_exsits'), adminUrl('sys_setting', 'dbrestore'), 'tip', 3, 'message');
		}
		//获取文件夹下全部还原文件
		$filelist=array();
		$filelist=glob($path.DIRECTORY_SEPARATOR.'*.sql');
		if(!$filelist || empty($filelist)){
			\Core::message(\Core::L('restore_file_no_exsits'), adminUrl('sys_setting', 'dbrestore'), 'tip', 3, 'message');
		}
        //开始还原
        $this -> log(\Core::L('database_restore') . '[name:' . $id . ']', 'restore');
        \Core::business('sys_database')->setRestoreFiles($filelist);
		\Core::message(\Core::L('restore_ready'), adminUrl('sys_setting', 'dbrestore_doing'), 'tip', 2, 'message',false);
	}
	
	//还原
	public function do_dbrestore_doing(){
		$db=\Core::business('sys_database');
		$step=\Core::get('step');
		$current=$db->getCurrentRestoreFile();
		if($current===false){
			\Core::message(\Core::L('restore_file_no_exsits'), adminUrl('sys_setting', 'dbrestore'), 'tip', 3, 'message');
		}
		if($step){
			$db->restore();
			$err = $db -> getError();
			if ($err) {
				//备份出错
				\Core::message($err, adminUrl('sys_setting', 'dbrestore'), 'fail', 3, 'message');
			}
			if(!$db->getRestoreNext()){
				//备份完成
				\Core::message(\Core::L('restore_complete'), adminUrl('sys_setting', 'dbrestore'), 'suc', 3, 'message');
			}
			$current=$db->getCurrentRestoreFile();
			\Core::message(sprintf(\Core::L('restore_doing'), $current), adminUrl('sys_setting', 'dbrestore_doing',array('step'=>1)), 'tip', 3, 'message',false);
		}
		\Core::message(sprintf(\Core::L('restore_doing'), $current), adminUrl('sys_setting', 'dbrestore_doing',array('step'=>1)), 'tip', 3, 'message',false);
	}

	public function do_dbrestore_del() {
		$id = \Core::get("id");
		if (!$id) {
			showJSON(0, \Core::L('parameter_error'));
		}
		$ids = explode(',', $id);
		foreach ($ids as $v) {
			delDir(\Core::config() -> getStorageDirPath() . 'backup' . DIRECTORY_SEPARATOR . $v);
		}
		$this -> log(\Core::L('delete,database_backup') . '[name:' . $id . ']', 'delete');
		showJSON(200, \Core::L('delete,database_backup,success'));
	}

	public function do_dbrestore_json() {
		$dirlist = readDirList(\Core::config() -> getStorageDirPath() . 'backup');
		$datalist = array();
		if ($dirlist && is_array($dirlist)) {
			foreach ($dirlist as $k => $v) {
				$row = array();
				$row['id'] = $v;
				$row['cell'][] = "<a class='btn red' onclick=\"flexDelete('{$v}')\"><i class='fa fa-trash-o'></i> " . \Core::L('delete') . "</a> <a class='btn blue' onclick=\"flexRestore('{$v}')\"><i class='fa fa-database'></i> " . \Core::L('restore') . "</a>";
				$row['cell'][] = $v;
				$row['cell'][] = count(glob(\Core::config() -> getStorageDirPath() . 'backup' . DIRECTORY_SEPARATOR . $v . DIRECTORY_SEPARATOR . "*.sql"));
				$row['cell'][] = date('Y-m-d H:i:s', filemtime(\Core::config() -> getStorageDirPath() . 'backup' . DIRECTORY_SEPARATOR . $v));
				$row['cell'][] = number_format((getDirSize(\Core::config() -> getStorageDirPath() . 'backup' . DIRECTORY_SEPARATOR . $v) / 1024), 2) . ' kb';
				$row['cell'][] = '';
				$datalist['rows'][] = $row;
			}
		}
		$datalist['page'] = 1;
		$datalist['total'] = count($dirlist);
		echo @json_encode($datalist);
	}

	//获取全部菜单
	private function get_nav() {
		$lang = Language::getLangContent();
		$arr = array();
		$array =
		require_once (BASE_PATH . '/menu.php');
		//将LIST部分叠加，去除没有link的
		$i = 0;
		foreach ($array as $k => $v) {
			$arr[$i] = $v;
			if (\Core::arrayKeyExists('list', $v) && $v['list']) {
				$list = array();
				$this -> get_nav_list($v['list'], $list);
				$arr[$i]['list'] = $list;
			}
			$i++;
		}
		return $arr;
	}

	private function get_nav_list($menus, &$list) {
		foreach ($menus as $v) {
			if (\Core::arrayKeyExists('link', $v) && $v['link']) {
				$list[] = $v;
			}
			if (\Core::arrayKeyExists('sub', $v)) {
				$this -> get_nav_list($v['sub'], $list);
			}
		}
	}

}
