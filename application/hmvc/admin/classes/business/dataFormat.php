<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * 用于common.php执行excel导出时需要格式化数据的方法集
 */
class  business_dataFormat extends Business {
	//全部调用该方法
	public function format($func,$datas){
		if(function_exists($func)){
			return $this->$func($datas);
		}
		return $datas;
	}
	
	//例子
	private function formatA($datas){
		$formatDatas=array();
		//具体执行内容
		return $formatDatas;
	}
}
