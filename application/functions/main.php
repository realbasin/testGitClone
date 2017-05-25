<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/*
 * 获取缓存，如果不存在则从数据库创建缓存
 * @key 缓存key
 * @dao 如果不存在，从指定dao中建立
 */
$setting_config = array();

//获取系统配置缓存
function getGlobalConfig() {
	global $setting_config;
	$setting_config = \Core::cache() -> get('setting_config');
	if ($setting_config == null) {
		$setting_config = \Core::db() -> select('*') -> from('setting') -> execute() -> key('name') -> rows();
		\Core::cache() -> set('setting_config', $setting_config);
		//getWxConfig(true);
	}
	return $setting_config;
}

//获取水印文字缓存
function getWatermarkFonts() {
	$fonts = \Core::cache() -> get('watermark_fonts');
	if ($fonts == null) {
		$fonts = \Core::db() -> select('*') -> from('watermark_font') -> execute() -> rows();
		\Core::cache() -> set('watermark_fonts', $fonts);
	}
	return $fonts;
}

//检测表单是否已提交
function chksubmit() {
	$submit = \Core::post('form_submit');
	if ($submit != 'ok')
		return false;
	return true;
}

//导出excel
function exportExcel($filename, $header = array(), $data = array()) {
	$filename = $filename . ".xlsx";
	header('Content-disposition: attachment; filename="' . $filename . '"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	$writer = \Core::library("XLSXWriter");
	$writer -> setAuthor("Xiaoshu InfoTech Ltd.");
	if (empty($header)) {
		$writer -> writeSheet($data);
		$writer -> writeToStdOut();
	} else {
		$writer -> writeSheetHeader('Sheet1', $header);
		foreach ($data as $row)
			$writer -> writeSheetRow('Sheet1', $row);
		$writer -> writeToStdOut();
	}
}

/*
 * 快速获得配置内容
 */
function C($key) {
	if (\Core::arrayKeyExists($key, $GLOBALS['setting_config'])) {
		return $GLOBALS['setting_config'][$key]['value'];
	}
	return '';
}

/**
 * 获取微信配置
 */
function getWxConfig($reload = false) {
	static $options = array();
	if (empty($options) || $reload) {
		$options = array('token' => C('wechat_token'), // 填写你设定的key
		'appid' => C('wechat_app_id'), // 填写高级调用功能的app id, 请在微信开发模式后台查询
		'appsecret' => C('wechat_app_secret'), // 填写高级调用功能的密钥
		'encodingaeskey' => C('wechat_aes_key'), // 填写加密用的EncodingAESKey（可选，接口传输选择加密时必需）
		'mch_id' => C('wechat_mch_id'), // 微信支付，商户ID（可选）
		'partnerkey' => C('wechat_partner_key'), // 微信支付，密钥（可选）
		'ssl_cer' => C('wechat_ssl_cer'), // 微信支付，证书cert的路径（可选，操作退款或打款时必需）
		'ssl_key' => C('wechat_ssl_key'), // 微信支付，证书key的路径（可选，操作退款或打款时必需）
		'cachepath' => \Base::getConfig()->getStorageDirPath().'wechat', // 设置SDK缓存目录（可选，默认位置在./src/Cache下，请保证写权限）
		);
	}
	return $options;
}

/*
 * 获取微信接口
 */ 
function getWxLibrary($api){
	return \Core::factory($api, null, getWxConfig());
}

/*
 * 输出JSON内容
 */
function showJSON($code = '', $message = '', $data = array()) {
	echo \Core::json($code, $message, $data);
	exit ;
}

/*
 * 生成admin访问地址
 */
function adminUrl($ctl, $mtd = '', $args = array()) {
	return \Core::getUrl($ctl, $mtd, \Core::config() -> getAdminModule(), $args);
}

/*
 * 获取文件夹大小
 */
function getDirSize($path, $size = 0) {
	$dir = @dir($path);
	if (!empty($dir -> path) && !empty($dir -> handle)) {
		while ($filename = $dir -> read()) {
			if ($filename != '.' && $filename != '..') {
				if (is_dir($path . DIRECTORY_SEPARATOR . $filename)) {
					$size += getDirSize($path . DIRECTORY_SEPARATOR . $filename);
				} else {
					$size += filesize($path . DIRECTORY_SEPARATOR . $filename);
				}
			}
		}
	}
	return $size ? $size : 0;
}

/*
 * 获取文件列表(所有子目录文件)
 *
 * @param string $path 目录
 * @param array $file_list 存放所有子文件的数组
 * @param array $ignore_dir 需要忽略的目录或文件
 * @return array 数据格式的返回结果
 */
function readFileList($path, &$file_list, $ignore_dir = array()) {
	$path = rtrim($path, DIRECTORY_SEPARATOR);
	if (is_dir($path)) {
		$handle = @opendir($path);
		if ($handle) {
			while (false !== ($dir = readdir($handle))) {
				if ($dir != '.' && $dir != '..') {
					if (!in_array($dir, $ignore_dir)) {
						if (is_file($path . DIRECTORY_SEPARATOR . $dir)) {
							$file_list[] = $path . DIRECTORY_SEPARATOR . $dir;
						} elseif (is_dir($path . DIRECTORY_SEPARATOR . $dir)) {
							readFileList($path . DIRECTORY_SEPARATOR . $dir, $file_list, $ignore_dir);
						}
					}
				}
			}
			@closedir($handle);
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function delDir($path) {
	$dh = opendir($path);
	while ($file = readdir($dh)) {
		if ($file != "." && $file != "..") {
			$fullpath = $path . DIRECTORY_SEPARATOR . $file;
			if (!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				delDir($fullpath);
			}
		}
	}
	closedir($dh);
	if (rmdir($path)) {
		return true;
	} else {
		return false;
	}
}

function readDirList($path) {
	if (is_dir($path)) {
		$handle = @opendir($path);
		$dir_list = array();
		if ($handle) {
			while (false !== ($dir = readdir($handle))) {
				if ($dir != '.' && $dir != '..' && is_dir($path . DIRECTORY_SEPARATOR . $dir)) {
					$dir_list[] = $dir;
				}
			}
			return $dir_list;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

//格式化数字
function priceFormat($price) {
	$price_format = number_format($price, 2, '.', '');
	return $price_format;
}

//获得32位随机数
function getRandString($prefix = '') {
	return md5($prefix . microtime() . mt_rand());
}

//设置流水号
function setTransactionId()
{
    list($usec, $sec) = explode(" ", microtime());

    $msec = round($usec*1000);

    $millisecond = str_pad($msec, 3 , '0', STR_PAD_RIGHT);

    $transaction_id = date("YmdHis").$millisecond;

    return $transaction_id;
}
?>