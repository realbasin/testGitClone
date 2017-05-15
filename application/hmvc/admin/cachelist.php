<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

$cache = array(
			array(
				'id' => 'setting_config',
				'cache_name'=>\Core::L('cache_setting_config'),
				'cache_des'=>\Core::L('cache_setting_config_des')
			),
			array(
				'id' => 'watermark_fonts',
				'cache_name'=>\Core::L('cache_watermark_fonts'),
				'cache_des'=>\Core::L('cache_watermark_fonts_des')
			),
			array(
				'id' => 'wechat_menu',
				'cache_name'=>\Core::L('cache_wechat_menu'),
				'cache_des'=>\Core::L('cache_wechat_menu_des')
			),
			array(
				'id' => 'sor_code',
				'cache_name'=>\Core::L('cache_sor_code'),
				'cache_des'=>\Core::L('cache_sor_code_des')
			),
			array(
				'id' => 'deal_cate',
				'cache_name'=>'投标类型',
				'cache_des'=>'如果修改了投标类型，且数据不匹配时需要重置该缓存'
			),
			array(
				'id' => 'deal_use_type',
				'cache_name'=>'贷款用途',
				'cache_des'=>'如果修改了贷款用途，且数据不匹配时需要重置该缓存'
			),
			array(
				'id' => 'deal_loan_type',
				'cache_name'=>'贷款类型',
				'cache_des'=>'贷款类型'
			),
		);

return $cache;
?>
