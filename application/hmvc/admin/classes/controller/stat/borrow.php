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
	
	//借款人数统计
	public function do_borrower(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_borrower');
	}
	
	//已放款借款人统计数据
	public function do_borrower_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoDeal = \Core::dao('loan_loanbid');
		$data = $daoDeal -> getStatBorrower(strtotime($datestart), strtotime($dateend));
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
	
	//借款人统计导出
	public function do_borrower_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['借款人数量'] = 'integer';

		$daoDeal = \Core::dao('loan_loanbid');
		$data = $daoDeal -> getStatBorrower(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出已放款借款人次统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('已放款借款人次统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}
	
	//借款额统计
	public function do_borrowerAmount(){
		
	}
	
	public function do_borrowerAmount_json(){
		
	}
	
	public function do_borrowerAmount_export(){
		
	}
	
	//已还统计
	public function do_repayment(){
		
	}
	
	//待还统计
	public function do_noRepayment(){
		
	}
	
	//逾期排名
	public function do_overdueDetail(){
		
	}
	
	//逾期分析
	public function do_overdueAnalyze(){
		
	}
	
	//逾期波动
	public function do_overdueDay(){
		
	}
}