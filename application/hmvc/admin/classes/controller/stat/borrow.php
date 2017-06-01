<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_stat_borrow extends controller_sysBase {
	
	//逾期排名顶部tab
	private $overDueTaps = array(
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_saleman', 'text' => '归属业务员'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_saleman1', 'text' => '归属行长'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_referrer', 'text' => '推荐人'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_checker', 'text' => '初审人'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_month', 'text' => '月排行'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_day', 'text' => '日排行'), 
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_area', 'text' => '地区'),
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_college', 'text' => '学校'),  
	array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_age', 'text' => '年龄'),  
	);
	
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

		$daoBid = \Core::dao('loan_loanbid');
		$data = $daoBid -> getStatBorrower(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出已放款借款人次统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('已放款借款人次统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}
	
	//借款额统计
	public function do_borrowerAmount(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_borrowerAmount');
	}
	
	public function do_borrowerAmount_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoBase = \Core::dao('loan_loanbase');
		$data = $daoBase->getStatApplyBorrow(strtotime($datestart), strtotime($dateend));
		//执行运算
		$output=array();
		if ($data) {
			foreach($data as $v){
				$row=array();
				$row['createdate']=$v['createdate'];
				if(\Core::arrayKeyExists($v['createdate'], $output)){
					$oldrow=$output[$v['createdate']];
					$row['apply_borrow_amount']=$oldrow['apply_borrow_amount']+$v['apply_borrow_amount'];
					$row['apply_user_count']=$oldrow['apply_user_count']+1;
					$row['real_borrow_amount']=$v['is_has_loans']?$oldrow['real_borrow_amount']+$v['borrow_amount']:$oldrow['real_borrow_amount'];
					$row['fail_borrow_amount']=($v['deal_status']==3)?$oldrow['fail_borrow_amount']+$v['borrow_amount']:$oldrow['fail_borrow_amount'];
					$row['audit_borrow_amount']=$v['loan_id']?$oldrow['audit_borrow_amount']+$v['borrow_amount']:$oldrow['audit_borrow_amount'];
					$row['audit_user_count']=$v['loan_id']?$oldrow['audit_user_count']+1:$oldrow['audit_user_count'];
				}else{
					$row['apply_borrow_amount']=$v['apply_borrow_amount'];
					$row['apply_user_count']=1;
					$row['real_borrow_amount']=$v['is_has_loans']?$v['borrow_amount']:0;
					$row['fail_borrow_amount']=($v['deal_status']==3)?$v['borrow_amount']:0;
					$row['audit_borrow_amount']=$v['loan_id']?$v['borrow_amount']:0;
					$row['audit_user_count']=$v['loan_id']?1:0;
				}
				$output[$v['createdate']]=$row;
			}
		}else{
			$datarow['createdate'] = $datestart;
			$datarow['apply_borrow_amount']=0;
			$datarow['apply_user_count']=0;
			$datarow['real_borrow_amount']=0;
			$datarow['fail_borrow_amount']=0;
			$datarow['audit_borrow_amount']=0;
			$datarow['audit_user_count']=0;
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$output[] = $datarow;
			$output[] = $datarowend;
		}
		showJSON('200', '', $output);
	}
	
	public function do_borrowerAmount_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['申请借款金额'] = 'price';
		$header['申请人数'] = 'integer';
		$header['满标放款金额'] = 'price';
		$header['流标失败金额'] = 'price';
		$header['审核通过金额'] = 'price';
		$header['审核通过人数'] = 'integer';

		$daoBase = \Core::dao('loan_loanbase');
		$data = $daoBase->getStatApplyBorrow(strtotime($datestart), strtotime($dateend));
		//执行运算
		$output=array();
		if ($data) {
			foreach($data as $v){
				$row=array();
				$row['createdate']=$v['createdate'];
				if(\Core::arrayKeyExists($v['createdate'], $output)){
					$oldrow=$output[$v['createdate']];
					$row['apply_borrow_amount']=$oldrow['apply_borrow_amount']+$v['apply_borrow_amount'];
					$row['apply_user_count']=$oldrow['apply_user_count']+1;
					$row['real_borrow_amount']=$v['is_has_loans']?$oldrow['real_borrow_amount']+$v['borrow_amount']:$oldrow['real_borrow_amount'];
					$row['fail_borrow_amount']=($v['deal_status']==3)?$oldrow['fail_borrow_amount']+$v['borrow_amount']:$oldrow['fail_borrow_amount'];
					$row['audit_borrow_amount']=$v['loan_id']?$oldrow['audit_borrow_amount']+$v['borrow_amount']:$oldrow['audit_borrow_amount'];
					$row['audit_user_count']=$v['loan_id']?$oldrow['audit_user_count']+1:$oldrow['audit_user_count'];
				}else{
					$row['apply_borrow_amount']=$v['apply_borrow_amount'];
					$row['apply_user_count']=1;
					$row['real_borrow_amount']=$v['is_has_loans']?$v['borrow_amount']:0;
					$row['fail_borrow_amount']=($v['deal_status']==3)?$v['borrow_amount']:0;
					$row['audit_borrow_amount']=$v['loan_id']?$v['borrow_amount']:0;
					$row['audit_user_count']=$v['loan_id']?1:0;
				}
				$output[$v['createdate']]=$row;
			}
		}else{
			$datarow['createdate'] = $datestart;
			$datarow['apply_borrow_amount']=0;
			$datarow['apply_user_count']=0;
			$datarow['real_borrow_amount']=0;
			$datarow['fail_borrow_amount']=0;
			$datarow['audit_borrow_amount']=0;
			$datarow['audit_user_count']=0;
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$output[] = $datarow;
			$output[] = $datarowend;
		}
		$this -> log('导出借款金额/人次统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('借款金额/人次统计(' . $datestart . ' - ' . $dateend . ')', $header, $output);
	}
	
	//已还统计
	public function do_repayment(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_repayment');
	}
	
	public function do_repayment_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay->getStatHasPayment(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			$datarow = array();
			$datarow['payment_date'] = $datestart;
			$datarow['payment_amount'] = "0";
			$datarow['payment_capital'] = "0";
			$datarow['payment_interest'] = "0";
			$datarow['payment_fine'] = "0";
			$datarow['payment_penalty'] = "0";
			$datarow['invest_fee'] = "0";
			$datarow['loan_fee'] = "0";
			$datarow['platform_income'] = "0";
			$datarow['payment_number'] = "0";
			$datarowend = $datarow;
			$datarowend['payment_date'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}
	
	public function do_repayment_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['已还总额'] = 'price';
		$header['已还本金'] = 'price';
		$header['已还利息'] = 'price';
		$header['提前还款罚息'] = 'price';
		$header['逾期还款罚金'] = 'price';
		$header['投资者管理费'] = 'price';
		$header['借款者管理费'] = 'price';
		$header['平台收入'] = 'price';
		$header['还款人次'] = 'integer';

		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay->getStatHasPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出已还统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('已还统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}
	
	//待还统计
	public function do_noRepayment(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_noRepayment');
	}
	
	public function do_noRepayment_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay->getStatNoPayment(strtotime($datestart), strtotime($dateend));
		if (!$data) {
			$datarow = array();
			$datarow['nopayment_date'] = $datestart;
			$datarow['nopayment_amount'] = "0";
			$datarow['nopayment_capital'] = "0";
			$datarow['nopayment_interest'] = "0";
			$datarow['nopayment_number'] = "0";
			$datarowend = $datarow;
			$datarowend['nopayment_date'] = $dateend;
			$data[] = $datarow;
			$data[] = $datarowend;
		}
		showJSON('200', '', $data);
	}
	
	public function do_noRepayment_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['待还总额'] = 'price';
		$header['待还本金'] = 'price';
		$header['待还利息'] = 'price';
		$header['待还人次'] = 'integer';

		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay->getStatNoPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出待还统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('待还统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}
	
	//逾期排名
	public function do_overdueDetail(){
		$this->do_overdueDetail_saleman();
	}
	
	//逾期排名 - 归属业务员
	public function do_overdueDetail_saleman(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_saleman');
		\Core::view()->set('datestart',$datestart);
		\Core::view()->set('dateend',$dateend);
		\Core::view()->load('stat_overdueDetailSaleman',$pagetabs);
	}
	
	public function do_overdueDetail_saleman_json(){
	}
	
	//逾期排名 - 归属行长
	public function do_overdueDetail_saleman1(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_saleman1');
	}
	
	//逾期排名 - 推荐人
	public function do_overdueDetail_referrer(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_referrer');
	}
	
	//逾期排名 - 初审人
	public function do_overdueDetail_checker(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_checker');
	}
	
	//逾期排名 - 月排行
	public function do_overdueDetail_month(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_month');
	}
	
	//逾期排名 - 日排行
	public function do_overdueDetail_day(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_day');
	}
	
	//逾期排名 - 地区
	public function do_overdueDetail_area(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_area');
	}
	
	//逾期排名 - 学校
	public function do_overdueDetail_college(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_college');
	}
	
	//逾期排名 - 年龄
	public function do_overdueDetail_age(){
		$pagetabs=$this -> createTaps($this -> overDueTaps, 'overdueDetail_age');
	}
	
	//逾期分析
	public function do_overdueAnalyze(){
		
	}
	
	//逾期波动
	public function do_overdueDay(){
		
	}
}