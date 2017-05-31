<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 债券转让统计
 */
class  controller_stat_debenture extends controller_sysBase {
	public function before() {
		
	}
	
	//债券转让
	public function debentureTransfer(){
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
	
	public function debentureTransfer_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoBid = \Core::dao('loan_loanbid');
		$data = $daoBid->getStatBorrower(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			$datarow = array();
			$datarow['createdate'] = $datestart;
			$datarow['usertotal'] = "0";
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}
	
	public function debentureTransfer_export(){
		
	}
}