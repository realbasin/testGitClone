<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_stat_borrow extends controller_sysBase {
	public function before() {
		
	}
	
	//借出汇总
	public function do_all() {
		$datestart = \Core::post('datestart');
		$dateend = \Core::post('dateend');
		$where = array();
		if ($datestart) {
			$timestart = strtotime($datestart);
		} else {
			$timestart = 0;
		}
		if ($dateend) {
			$timeend = strtotime($dateend);
		} else {
			$timeend = 0;
		}

		$data=\Core::dao('loan_dealrepay')->getStatBorrowAll($timestart,$timeend);
		$data['rebate_all']=\Core::dao('loan_dealload')->getAllRebate($timestart,$timeend);
		
		//格式化金额
		foreach($data as $k=>$v){
			$data[$k]=priceFormat($v);
		}
		
		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;
		
		\Core::view() -> set($data) -> load('stat_borrowAll');
	}
}