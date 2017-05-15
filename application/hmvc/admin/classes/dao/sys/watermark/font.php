<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_sys_watermark_font extends Dao {

	public function getColumns() {
		return array(
				'font_id'//自动ID
				,'font_name'//字体名称
				,'font_path'//字体文件路径
				,'sys'//是否是系统字体
				);
	}

	public function getPrimaryKey() {
		return 'font_id';
	}

	public function getTable() {
		return 'watermark_font';
	}

}
