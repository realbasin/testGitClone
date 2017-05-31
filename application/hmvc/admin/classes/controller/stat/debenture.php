<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 债券转让统计
 */
class  controller_stat_debenture extends controller_sysBase {
	public function before() {
		
	}
	
	//债券转让
	public function do_debentureTransfer(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_debentureTransfer');
	}
	
	public function do_debentureTransfer_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$statBusiness = \Core::business('loan_stat');
		$data = $statBusiness->getStatTransferData(strtotime($datestart), strtotime($dateend));
		showJSON('200', '', $data);
	}
	
	public function do_debentureTransfer_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['债券转让笔数'] = 'integer';
		$header['债券转让金额'] = 'price';
		$header['成功转让笔数'] = 'integer';
		$header['成功转让金额'] = 'price';
		$header['债券转让管理费'] = 'price';

		$statBusiness = \Core::business('loan_stat');
		$datas = $statBusiness->getStatTransferData(strtotime($datestart), strtotime($dateend));
		//二次处理
		$exportData=array();
		foreach($datas as $k=>$v){
			$row=array();
			$row[]=$v['date'];
			$row[]=\Core::arrayKeyExists('transfernum', $v)?$v['transfernum']:0;
			$row[]=\Core::arrayKeyExists('transfermoney', $v)?$v['transfermoney']:0;
			$row[]=\Core::arrayKeyExists('successnum', $v)?$v['successnum']:0;
			$row[]=\Core::arrayKeyExists('successmoney', $v)?$v['successmoney']:0;
			$row[]=\Core::arrayKeyExists('transferfeemoney', $v)?$v['transferfeemoney']:0;
			$exportData[]=$row;
		}
		//导出
		$this -> log('导出债券转让汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('债券转让汇总(' . $datestart . ' - ' . $dateend . ')', $header, $exportData);
	}
}