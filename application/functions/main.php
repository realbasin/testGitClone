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

/**
 * 循环创建目录
 *
 * @param string $dir 待创建的目录
 * @param  $mode 权限
 * @return boolean
 */
function mk_dir($dir, $mode = '0777') {
	if (is_dir($dir) || @mkdir($dir, $mode))
		return true;
	if (!mk_dir(dirname($dir), $mode))
		return false;
	return @mkdir($dir, $mode);
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

/**
 * 读取目录列表
 * 不包括 . .. 文件 三部分
 *
 * @param string $path 路径
 * @return array 数组格式的返回结果
 */
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


//类似js的escape
function phpEscape($str) {
	preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r);
	$ar = $r[0];
	foreach ($ar as $k => $v) {
		if (ord($v[0]) < 128)
			$ar[$k] = rawurlencode($v);
		else
			$ar[$k] = "%u" . bin2hex(iconv("GB2312", "UCS-2", $v));
	}
	return join("", $ar);
}

/**
 * 取上一步来源地址
 *
 * @param
 * @return string 字符串类型的返回结果
 */
function getReferer() {
	return empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
}

/**
 * 邮件/短信/通知 内容转换函数
 *
 * @param string $message 内容模板内容
 * @param array $param 内容参数数组
 * @return string 通知内容
 */
function ReplaceText($message, $param) {
	if (!is_array($param))
		return false;
	foreach ($param as $k => $v) {
		$message = str_replace('{$' . $k . '}', $v, $message);
	}
	return $message;
}

/**
 * 字符串切割函数，一个字母算一个位置,一个字算2个位置
 *
 * @param string $string 待切割的字符串
 * @param int $length 切割长度
 * @param string $dot 尾缀
 */
function strCut($string, $length, $dot = '') {
	$string = str_replace(array('&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array(' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strlen = strlen($string);
	if ($strlen <= $length)
		return $string;
	$maxi = $length - strlen($dot);
	$strcut = '';
	if (strtolower(CHARSET) == 'utf-8') {
		$n = $tn = $noc = 0;
		while ($n < $strlen) {
			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t < 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}
			if ($noc >= $maxi)
				break;
		}
		if ($noc > $maxi)
			$n -= $tn;
		$strcut = substr($string, 0, $n);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen;
		for ($i = 0; $i < $maxi; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
		}
	}
	$strcut = str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), $strcut);
	return $strcut . $dot;
}

/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

//获得32位随机数
function getRandString($prefix = '') {
	return md5($prefix . microtime() . mt_rand());
}

//设置流水号
function setTransactionId() {
    list($usec, $sec) = explode(" ", microtime());

    $msec = round($usec*1000);

    $millisecond = str_pad($msec, 3 , '0', STR_PAD_RIGHT);

    $transaction_id = date("YmdHis").$millisecond.mt_rand(100, 999);

    return $transaction_id;
}

/**
 * 获取指定日期段内每一天的日期
 * @param  Date  $startdate 开始日期
 * @param  Date  $enddate   结束日期
 * @param  Blean $unixdate  是否是unix时间
 * @return Array
 */
function getDateFromRange($startdate, $enddate,$unixdate=false){
	if(!$unixdate){
		$stimestamp = strtotime($startdate);
    	$etimestamp = strtotime($enddate);
	}else{
		$stimestamp=$startdate;
		$etimestamp=$enddate;
	}
    // 计算日期段内有多少天
    $days = ($etimestamp-$stimestamp)/86400+1;

    // 保存每天日期
    $date = array();

    for($i=0; $i<$days; $i++){
        $date[] = date('Y-m-d', $stimestamp+(86400*$i));
    }
    return $date;
}

function get_image_cdn_host()
{
	$cdn_host = '';
	$domain_suffix = get_domain_suffix();
	$app_env = \Core::config()->getEnvironment();
	switch ($app_env) {
		case 'production': //正式环境
			$cdn_host = 'image.' . $domain_suffix;
			break;
		case 'test':  //测试环境
			$cdn_host = 'image.' . $domain_suffix;
			break;
		case 'development': //开发环境
		default:
			$cdn_host = 'image.' . $domain_suffix;
			break;
	}
	return $cdn_host;
}

function get_domain_suffix()
{
	$domain_suffix = '';  // xiaoshushidai.com  |  xiaoshushidai.cn  |  xs2buy.com
	$host_parts = explode('.', get_domain());
	foreach ($host_parts as $k => $host_part) {
		$l_host_part = strtolower($host_part);
		if ($l_host_part == 'com' || $l_host_part == 'cn') {
			$domain_suffix = $host_parts[$k - 1] . '.' . $host_part;
		}
	}
	return $domain_suffix;
}

function get_domain(){
	/* 协议 */
	$protocol = get_http();

	$host = get_host();

	$port = $_SERVER['SERVER_PORT'];

	if ($port == 80 || $port == 443) {
		$port = '';
	} else {
		$port = ':' . $port;
	}
	return $protocol . $host . $port;
}

function get_http(){
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}

function get_host(){
	$host = $_SERVER['SERVER_NAME'];
	if (empty($host))
		$host = $_SERVER['HTTP_HOST'];
	return $host;
}

// 修改图片的CDN地址
function set_cdn_host($url)
{
	$url = trim($url);
	if ($url == '')
		return '';

	$cdn_host = get_image_cdn_host();

	if (strpos($url, '.com') !== false) {
		$url_parts = explode('.com', $url);
		$url = $cdn_host . $url_parts[1];
	} else if (strpos($url, '.cn') !== false) {
		$url_parts = explode('.cn', $url);
		$url = $cdn_host . $url_parts[1];
	} else {
		$url = $cdn_host . '/' . $url;
	}

	$url = str_replace('/./', '/', $url);
	$url = str_replace('//', '/', $url);
	//$url = 'http://' . $url;
	$url = get_http() . $url;

	return $url;
}
/*
 * 增加german队列
 * @option $taskName 要执行的任务名称，不含Task_前缀
 * @option $taskArgs 任务执行参数 eg:array('deal_id'=>1000)
 * @option $hmvc 要执行的task所在的MVC，如果留空，默认主mvc
 */
function addGerman($taskName,Array $taskArgs=array(),$hmvc='',$sync = false){
    $gearmanClient = \Core::library('XSGearmanClient');
    $gearmanClient->setFunctionNameAndArgs($taskName,$taskArgs);
    $flag = $gearmanClient->send($sync);
    if($flag){
        return $gearmanClient->getTaskId();
	}else{
    	return false;
	}
}

/*
 * 增加rabbitmq队列
 */
function addRabbitQueue($taskName,$taskArgs=array(),$hmvc=''){
	//TODO 增加RabbitMQ队列

}

function strim($str)
{
	return quotes(htmlspecialchars(trim($str)));
}

function quotes($content)
{
	//if $content is an array
	if (is_array($content)) {
		foreach ($content as $key => $value) {
			//$content[$key] = mysql_real_escape_string($value);
			$content[$key] = addslashes($value);
		}
	} else {
		//if $content is not an array
		$content = addslashes($content);
		//mysql_real_escape_string($content);
	}
	return $content;
}

/**
 * 获取post payload内容，同时把数据转为数组方式
 * @return mixed
 */
function getRequestJSON(){
    $content = file_get_contents('php://input');
    return json_decode($content,true);
}

/**
 * 兼容中文JSON编码
 *
 * @param string|array $var
 * @return mixed
 */
function json_encode_cn($var)
{
    $var = json_encode($var);

    return preg_replace_callback("/\\\u([0-9a-f]{4})/i", function ($r) {
        return iconv('UCS-2BE', 'UTF-8', pack('H*', $r[1]));
    }, $var);
}

/**
 * 获取指定时间戳过了指定月份之后的时间戳
 *
 * @param $time
 * @param int $m
 * @return mixed
 */
function next_replay_month($time,$m=1){
	$str_t = strtotime(date($time,'Y-m-d H:i:s')." ".$m." month ");
	return $str_t;
}

/**
 * curl post方式请求
 * @param $url
 * @param $data
 * @param array $headers
 * @return mixed
 */
function curl_post($url, $data, $headers = [])
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if (!empty($headers)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }

    if (!empty($data) && count($data) > 0) {
        $tmp = [];
        foreach ($data as $k => $v) {
            $tmp[] = "{$k}=" . urlencode($v);
        }

        $data_str = implode('&', $tmp);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_str);
    }

    $http_response = curl_exec($curl);
    curl_close($curl);

    return $http_response;
}

/**
 * 获取xssd_conf的配置
 * @param $key string
 * @param string $type type=value获取value的值,否则获取一行数据
 * @return string|array
 */
function getXSConf($key , $type='value'){
    $conf = \Core::cache() -> get('xssd_conf');
    if($conf == null){
        $conf = \Core::db() -> select('*') -> from('conf') -> execute() -> key('name') -> rows();
        \Core::cache() -> set('xssd_conf', $conf);
    }

    if (\Core::arrayKeyExists($key, $conf)) {
        if($type == 'value'){
            return $conf[$key]['value'];
        }else{
            return $conf[$key];
        }
    }
    return '';
}
