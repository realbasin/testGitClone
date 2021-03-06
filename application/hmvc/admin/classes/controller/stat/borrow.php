<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_stat_borrow extends controller_sysBase {

	//逾期排名顶部tab
	private $overDueTaps = array( array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_agent', 'text' => '归属业务员'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_saleman', 'text' => '归属行长'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_referrer', 'text' => '推荐人'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_checker', 'text' => '初审人'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_month', 'text' => '月排行'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_day', 'text' => '日排行'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_area', 'text' => '地区'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_college', 'text' => '学校'), array('ctl' => 'stat_borrow', 'act' => 'overdueDetail_age', 'text' => '年龄'), );

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

		$data = \Core::dao('loan_dealrepay') -> getStatBorrowAll($timestart, $timeend);
		$data['rebate_all'] = \Core::dao('loan_dealload') -> getAllRebate($timestart, $timeend);

		//格式化金额
		foreach ($data as $k => $v) {
			$data[$k] = priceFormat($v);
		}

		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;

		\Core::view() -> set($data) -> load('stat_borrowAll');
	}

	//借款人数统计
	public function do_borrower() {
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
	public function do_borrower_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoBid = \Core::dao('loan_loanbid');
		$data = $daoBid -> getStatBorrower(strtotime($datestart), strtotime($dateend));
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
	public function do_borrower_export() {
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
	public function do_borrowerAmount() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_borrowerAmount');
	}

	public function do_borrowerAmount_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoBase = \Core::dao('loan_loanbase');
		$data = $daoBase -> getStatApplyBorrow(strtotime($datestart), strtotime($dateend));
		//执行运算
		$output = array();
		if ($data) {
			foreach ($data as $v) {
				$row = array();
				$row['createdate'] = $v['createdate'];
				if (\Core::arrayKeyExists($v['createdate'], $output)) {
					$oldrow = $output[$v['createdate']];
					$row['apply_borrow_amount'] = $oldrow['apply_borrow_amount'] + $v['apply_borrow_amount'];
					$row['apply_user_count'] = $oldrow['apply_user_count'] + 1;
					$row['real_borrow_amount'] = $v['is_has_loans'] ? $oldrow['real_borrow_amount'] + $v['borrow_amount'] : $oldrow['real_borrow_amount'];
					$row['fail_borrow_amount'] = ($v['deal_status'] == 3) ? $oldrow['fail_borrow_amount'] + $v['borrow_amount'] : $oldrow['fail_borrow_amount'];
					$row['audit_borrow_amount'] = $v['loan_id'] ? $oldrow['audit_borrow_amount'] + $v['borrow_amount'] : $oldrow['audit_borrow_amount'];
					$row['audit_user_count'] = $v['loan_id'] ? $oldrow['audit_user_count'] + 1 : $oldrow['audit_user_count'];
				} else {
					$row['apply_borrow_amount'] = $v['apply_borrow_amount'];
					$row['apply_user_count'] = 1;
					$row['real_borrow_amount'] = $v['is_has_loans'] ? $v['borrow_amount'] : 0;
					$row['fail_borrow_amount'] = ($v['deal_status'] == 3) ? $v['borrow_amount'] : 0;
					$row['audit_borrow_amount'] = $v['loan_id'] ? $v['borrow_amount'] : 0;
					$row['audit_user_count'] = $v['loan_id'] ? 1 : 0;
				}
				$output[$v['createdate']] = $row;
			}
		} else {
			$datarow['createdate'] = $datestart;
			$datarow['apply_borrow_amount'] = 0;
			$datarow['apply_user_count'] = 0;
			$datarow['real_borrow_amount'] = 0;
			$datarow['fail_borrow_amount'] = 0;
			$datarow['audit_borrow_amount'] = 0;
			$datarow['audit_user_count'] = 0;
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$output[] = $datarow;
			$output[] = $datarowend;
		}
		showJSON('200', '', $output);
	}

	public function do_borrowerAmount_export() {
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
		$data = $daoBase -> getStatApplyBorrow(strtotime($datestart), strtotime($dateend));
		//执行运算
		$output = array();
		if ($data) {
			foreach ($data as $v) {
				$row = array();
				$row['createdate'] = $v['createdate'];
				if (\Core::arrayKeyExists($v['createdate'], $output)) {
					$oldrow = $output[$v['createdate']];
					$row['apply_borrow_amount'] = $oldrow['apply_borrow_amount'] + $v['apply_borrow_amount'];
					$row['apply_user_count'] = $oldrow['apply_user_count'] + 1;
					$row['real_borrow_amount'] = $v['is_has_loans'] ? $oldrow['real_borrow_amount'] + $v['borrow_amount'] : $oldrow['real_borrow_amount'];
					$row['fail_borrow_amount'] = ($v['deal_status'] == 3) ? $oldrow['fail_borrow_amount'] + $v['borrow_amount'] : $oldrow['fail_borrow_amount'];
					$row['audit_borrow_amount'] = $v['loan_id'] ? $oldrow['audit_borrow_amount'] + $v['borrow_amount'] : $oldrow['audit_borrow_amount'];
					$row['audit_user_count'] = $v['loan_id'] ? $oldrow['audit_user_count'] + 1 : $oldrow['audit_user_count'];
				} else {
					$row['apply_borrow_amount'] = $v['apply_borrow_amount'];
					$row['apply_user_count'] = 1;
					$row['real_borrow_amount'] = $v['is_has_loans'] ? $v['borrow_amount'] : 0;
					$row['fail_borrow_amount'] = ($v['deal_status'] == 3) ? $v['borrow_amount'] : 0;
					$row['audit_borrow_amount'] = $v['loan_id'] ? $v['borrow_amount'] : 0;
					$row['audit_user_count'] = $v['loan_id'] ? 1 : 0;
				}
				$output[$v['createdate']] = $row;
			}
		} else {
			$datarow['createdate'] = $datestart;
			$datarow['apply_borrow_amount'] = 0;
			$datarow['apply_user_count'] = 0;
			$datarow['real_borrow_amount'] = 0;
			$datarow['fail_borrow_amount'] = 0;
			$datarow['audit_borrow_amount'] = 0;
			$datarow['audit_user_count'] = 0;
			$datarowend = $datarow;
			$datarowend['createdate'] = $dateend;
			$output[] = $datarow;
			$output[] = $datarowend;
		}
		$this -> log('导出借款金额/人次统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('借款金额/人次统计(' . $datestart . ' - ' . $dateend . ')', $header, $output);
	}

	//已还统计
	public function do_repayment() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_repayment');
	}

	public function do_repayment_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay -> getStatHasPayment(strtotime($datestart), strtotime($dateend));
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

	public function do_repayment_export() {
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
		$data = $daoRepay -> getStatHasPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出已还统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('已还统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//待还统计
	public function do_noRepayment() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_noRepayment');
	}

	public function do_noRepayment_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$daoRepay = \Core::dao('loan_dealloadrepay');
		$data = $daoRepay -> getStatNoPayment(strtotime($datestart), strtotime($dateend));
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

	public function do_noRepayment_export() {
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
		$data = $daoRepay -> getStatNoPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出待还统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('待还统计(' . $datestart . ' - ' . $dateend . ')', $header, $data);
	}

	//逾期排名
	public function do_overdueDetail() {
		$this -> do_overdueDetail_agent();
	}

	//逾期排名 - 归属业务员
	public function do_overdueDetail_agent() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_agent');
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailAgent', $pagetabs);
	}

	public function do_overdueDetail_agent_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$orderBy = 'user_count desc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('agent_name', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			if($orderSortName=='agent_name'){
				$orderSortName='adminid';
			}
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$datas = array();
		//先查询业务员表
		$daoAgent = \Core::dao('user_agent');
		$agents = $daoAgent -> findAll(null, array(), null, 'agent_id,agent_name,real_name');
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailAgent(strtotime($datestart), strtotime($dateend),$orderBy);
		$json=array();
		foreach ($dataDetail as $k => $v) {
			$row['id'] = $k;
			if(\Core::arrayKeyExists($k, $agents)){
				$agentRow=$agents[$k];
				$row['cell'][] = $agentRow['real_name']?$agentRow['real_name']:$agentRow['agent_name'];
			}else{
				$row['cell'][]='未指定行长';
			}
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=1;
		$json['total']=count($dataDetail);
		echo @json_encode($json);
	}

	public function do_overdueDetail_agent_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		$orderBy = 'user_count desc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('agent_name', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			if($orderSortName=='agent_name'){
				$orderSortName='adminid';
			}
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$datas = array();
		//先查询业务员表
		$daoAgent = \Core::dao('user_agent');
		$agents = $daoAgent -> findAll(null, array(), null, 'agent_id,agent_name,real_name');
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailAgent(strtotime($datestart), strtotime($dateend),$orderBy);
		foreach ($dataDetail as $k => $v) {
			if(\Core::arrayKeyExists($k, $agents)){
				$agentRow=$agents[$k];
				$v['adminid'] = $agentRow['real_name']?$agentRow['real_name']:$agentRow['agent_name'];
			}else{
				$v['adminid']='未指定行长';
			}
			$dataDetail[$k]=$v;
		}
		$header = array();
		$header['业务员'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		//导出
		$this -> log('导出归属业务员统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('归属业务员统计(' . $datestart . ' - ' . $dateend . ')', $header, $dataDetail);
	}

	//逾期排名 - 归属行长
	public function do_overdueDetail_saleman() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_saleman');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailSaleman', $pagetabs);
	}
	
	public function do_overdueDetail_saleman_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='user_count';
		$orderSort='desc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailSaleman($page,$pagesize,$startStamp,$endStamp,$orderName,$orderSort);
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $v['saleman_id'];
			$row['cell'][]=$v['saleman_name'].($v['saleman_realname']?"(".$v['saleman_realname'].")":'');
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_saleman_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDetail_saleman'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDetail_saleman'), 'fail', 3, 'message');
		}
		$bStat = \Core::business('loan_stat');
		$bComm = \Core::business('common');
		$sql=$bStat->getStatOverdueDetailSalemanSql($startStamp,$endStamp);
		$header=array();
		$header['行长ID'] = 'integer';
		$header['行长名称'] = 'string';
		$header['行长真实姓名'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		$this -> log('导出行长逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		$bComm->exportExcel($sql,'行长逾期统计(' . $datestart . ' - ' . $dateend . ')',$header,adminUrl('stat_borrow','overdueDetail_saleman'));
	}

	//逾期排名 - 推荐人
	public function do_overdueDetail_referrer() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_referrer');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailReferrer', $pagetabs);
	}
	
	public function do_overdueDetail_referrer_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='user_count';
		$orderSort='desc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailReferrer($page,$pagesize,$startStamp,$endStamp,$orderName,$orderSort);
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $v['referrer_id'];
			if($v['referrer_id']==0){
				$row['cell'][]='未指定';
			}else{
				$row['cell'][]=$v['referrer_name'].($v['referrer_realname']?"(".$v['referrer_realname'].")":'');
			}
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_referrer_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDetail_referrer'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDetail_referrer'), 'fail', 3, 'message');
		}
		$bStat = \Core::business('loan_stat');
		$bComm = \Core::business('common');
		$sql=$bStat->getStatOverdueDetailReferrerSql($startStamp,$endStamp);
		$header=array();
		$header['推荐人ID'] = 'integer';
		$header['推荐人名称'] = 'string';
		$header['推荐人真实姓名'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		$this -> log('导出推荐人逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		$bComm->exportExcel($sql,'推荐人逾期统计(' . $datestart . ' - ' . $dateend . ')',$header,adminUrl('stat_borrow','overdueDetail_referrer'));
	}

	//逾期排名 - 初审人
	public function do_overdueDetail_checker() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_checker');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailChecker', $pagetabs);
	}
	
	public function do_overdueDetail_checker_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$orderBy = 'user_count desc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('audit_id', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$datas = array();
		//先查询管理员表
		$daoAdmin = \Core::dao('sys_admin_admin');
		$admins = $daoAdmin -> findAll(null, array(), null, 'admin_id,admin_name,admin_real_name');
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailChecker(strtotime($datestart), strtotime($dateend),$orderBy);
		$json=array();
		foreach ($dataDetail as $k => $v) {
			$row['id'] = $k;
			if(\Core::arrayKeyExists($k, $admins)){
				$adminRow=$admins[$k];
				$row['cell'][] = $adminRow['admin_real_name']?$adminRow['admin_real_name']:$adminRow['admin_name'];
			}else{
				$row['cell'][]=' ';
			}
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=1;
		$json['total']=count($dataDetail);
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_checker_export() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		$orderBy = 'user_count desc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('agent_name', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			if($orderSortName=='agent_name'){
				$orderSortName='adminid';
			}
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$datas = array();
		//先查询管理员表
		$daoAdmin = \Core::dao('sys_admin_admin');
		$admins = $daoAdmin -> findAll(null, array(), null, 'admin_id,admin_name,admin_real_name');
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailChecker(strtotime($datestart), strtotime($dateend),$orderBy);
		foreach ($dataDetail as $k => $v) {
			if(\Core::arrayKeyExists($k, $admins)){
				$adminRow=$admins[$k];
				$v['audit_id'] = $adminRow['admin_real_name']?$adminRow['admin_real_name']:$adminRow['admin_name'];
			}else{
				$v['audit_id']=' ';
			}
			$dataDetail[$k]=$v;
		}
		$header = array();
		$header['初审人'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		//导出
		$this -> log('导出初审人逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('初审人逾期统计(' . $datestart . ' - ' . $dateend . ')', $header, $dataDetail);
	}

	//逾期排名 - 月排行
	public function do_overdueDetail_month() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_month');
		\Core::view() -> load('stat_overdueDetailMonth', $pagetabs);
	}
	
	public function do_overdueDetail_month_json() {
		$orderBy = 'deal_month asc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('deal_month', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailMonth($orderBy);
		$json=array();
		foreach ($dataDetail as $k => $v) {
			$row['id'] = $k;
			$row['cell'][] = $v['deal_month'];
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=1;
		$json['total']=count($dataDetail);
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_month_export() {
		$orderBy = 'deal_month asc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('deal_month', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailMonth($orderBy);
		$header = array();
		$header['借款月'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		//导出
		$this -> log('导出按月逾期统计', 'export');
		exportExcel('按月逾期统计', $header, $dataDetail);
	}

	//逾期排名 - 日排行
	public function do_overdueDetail_day() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_day');
		\Core::view() -> load('stat_overdueDetailDay', $pagetabs);
	}
	
	public function do_overdueDetail_day_json() {
		$orderBy = 'deal_day asc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('deal_day', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailDay($orderBy);
		$json=array();
		foreach ($dataDetail as $k => $v) {
			$row['id'] = $k;
			$row['cell'][] = $v['deal_day'];
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=1;
		$json['total']=count($dataDetail);
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_day_export() {
		$orderBy = 'deal_day asc';
		$orderSortName=\Core::postGet('sortname');
		if (\Core::postGet('sortorder') && in_array($orderSortName, array('deal_day', 'user_count', 'deal_count', 'repay_count', 'has_repay_count', 'expired_days'))) {
			$orderBy = $orderSortName . " " . \Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailDay($orderBy);
		$header = array();
		$header['借款日'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		//导出
		$this -> log('导出按日逾期统计', 'export');
		exportExcel('按日逾期统计', $header, $dataDetail);
	}

	//逾期排名 - 地区
	public function do_overdueDetail_area() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_area');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailArea', $pagetabs);
	}
	
	public function do_overdueDetail_area_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='user_count';
		$orderSort='desc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailArea($page,$pagesize,$startStamp,$endStamp,$orderName,$orderSort);
		$i=0;
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $i;
			$row['cell'][]=$v['region_name']?$v['region_name']:'未完善资料';
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
			$i+=1;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_area_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDetail_area'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDetail_area'), 'fail', 3, 'message');
		}
		$bStat = \Core::business('loan_stat');
		$bComm = \Core::business('common');
		$sql=$bStat->getStatOverdueDetailAreaSql($startStamp,$endStamp);
		$header=array();
		$header['地区'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		$this -> log('导出按地区逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		$bComm->exportExcel($sql,'按地区逾期统计(' . $datestart . ' - ' . $dateend . ')',$header,adminUrl('stat_borrow','overdueDetail_area'));
	}

	//逾期排名 - 学校
	public function do_overdueDetail_college() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_college');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailCollege', $pagetabs);
	}
	
	public function do_overdueDetail_college_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='user_count';
		$orderSort='desc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailCollege($page,$pagesize,$startStamp,$endStamp,$orderName,$orderSort);
		$i=0;
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $i;
			$row['cell'][]=$v['college']?$v['college']:'未完善资料';
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
			$i+=1;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_college_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDetail_college'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDetail_college'), 'fail', 3, 'message');
		}
		$bStat = \Core::business('loan_stat');
		$bComm = \Core::business('common');
		$sql=$bStat->getStatOverdueDetailCollegeSql($startStamp,$endStamp);
		$header=array();
		$header['学院'] = 'string';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		$this -> log('导出按学院逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		$bComm->exportExcel($sql,'按学院逾期统计(' . $datestart . ' - ' . $dateend . ')',$header,adminUrl('stat_borrow','overdueDetail_college'));
	}

	//逾期排名 - 年龄
	public function do_overdueDetail_age() {
		$pagetabs = $this -> createTaps($this -> overDueTaps, 'overdueDetail_age');
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDetailAge', $pagetabs);
	}
	
	public function do_overdueDetail_age_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='age';
		$orderSort='asc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat = \Core::business('loan_stat');
		$dataDetail = $bStat -> getStatOverdueDetailAge($page,$pagesize,$startStamp,$endStamp,$orderName,$orderSort);
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $k;
			$row['cell'][]=$v['age'];
			$row['cell'][] = $v['user_count'];
			$row['cell'][] = $v['deal_count'];
			$row['cell'][] = $v['repay_count'];
			$row['cell'][] = $v['has_repay_count'];
			$row['cell'][] = $v['expired_days'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDetail_age_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDetail_age'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDetail_age'), 'fail', 3, 'message');
		}
		$bStat = \Core::business('loan_stat');
		$bComm = \Core::business('common');
		$sql=$bStat->getStatOverdueDetailAgeSql($startStamp,$endStamp);
		$header=array();
		$header['年龄'] = 'integer';
		$header['逾期总人数'] = 'integer';
		$header['逾期总笔数'] = 'integer';
		$header['逾期总期数'] = 'integer';
		$header['逾期已还期数'] = 'integer';
		$header['逾期总天数'] = 'integer';
		$this -> log('导出按年龄逾期统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		$bComm->exportExcel($sql,'按年龄逾期统计(' . $datestart . ' - ' . $dateend . ')',$header,adminUrl('stat_borrow','overdueDetail_age'));
	}

	//逾期分析
	public function do_overdueAnalyze() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueAnalyze');
	}
	
	public function do_overdueAnalyze_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='create_time';
		$orderSort='asc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$daoAnalyze = \Core::dao('loan_dealRepayLateAnalysis');
		$dataDetail = $daoAnalyze ->getFlexPage($page,$pagesize,'*',array('create_time >='=>$startStamp,'create_time <='=>$endStamp),array($orderName=>$orderSort));
		foreach ($dataDetail['rows'] as $k => $v) {
			$row['id'] = $k;
			$row['cell'][]=date('Y-m-d',$v['create_time']);
			$row['cell'][] = $v['level'];
			$row['cell'][] = '￥'.$v['sum_repay_manage_money'];
			$row['cell'][] = '￥'.$v['sum_repay_money'];
			$row['cell'][] = '￥'.$v['sum_self_money'];
			$row['cell'][] = $v['sum_count_deal'];
			$row['cell'][] = $v['sum_over_times'];
			$row['cell'][] = '￥'.$v['sum_over_money'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueAnalyze_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueAnalyze'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueAnalyze'), 'fail', 3, 'message');
		}
		$where=array();
		$where['create_time >=']=$startStamp;
		$where['create_time <=']=$endStamp;
		$daoAnalyze = \Core::dao('loan_dealRepayLateAnalysis');
		$curPage=\Core::getPost('curpage');
		if (!is_numeric($curPage)){
			$count=$daoAnalyze->getCount($where);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_borrow', 'overdueAnalyze'));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$dataDetail = $daoAnalyze ->getFlexPage($curPage,C('export_perpage'),"FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_date,level,sum_repay_manage_money,sum_repay_money,sum_self_money,sum_count_deal,sum_over_times,sum_over_money",$where,array('create_time'=>'asc'));
		$header=array();
		$header['统计时间'] = 'date';
		$header['逾期等级'] = 'string';
		$header['逾期管理费'] = 'price';
		$header['逾期本息'] = 'price';
		$header['逾期本金'] = 'price';
		$header['逾期笔数'] = 'integer';
		$header['逾期期数'] = 'integer';
		$header['剩余未还本金'] = 'price';
		$this -> log('导出逾期数据分析第'.$curPage.'页(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('逾期数据分析第'.$curPage.'页(' . $datestart . ' - ' . $dateend . ')', $header, $dataDetail);
	}

	//逾期波动
	public function do_overdueDay() {
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-7 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_overdueDay');
	}
	
	public function do_overdueDay_json() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			showJSON('100', '请选择日期范围');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			showJSON('101', '开始日期不能大于结束日期');
		}
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='date_time';
		$orderSort='desc';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$select="date_time,has_repay,COUNT(DISTINCT(deal_id)) count_deal,COUNT(id) over_times,SUM(repay_money)+SUM(manage_money) sum_repay_manage_money,SUM(repay_money) sum_repay_money,SUM(self_money) sum_self_money";
		$where=array();
		$multiWhere=array();
		$where['UNIX_TIMESTAMP(date_time) >=']=$startStamp;
		$where['UNIX_TIMESTAMP(date_time) <=']=$endStamp;
		$multiWhere[]=array(array('has_repay'=>1),'and (','');
		$multiWhere[]=array(array('level'=>0),'or',')');
		$daoAnalyze = \Core::dao('loan_dealRepayLateAnalysisDailyDetail');
		$dataDetail = $daoAnalyze ->getFlexPage($page,$pagesize,$select,$where,array($orderName=>$orderSort),"date_time,has_repay",array(),false,$multiWhere);
		$json=array();
		foreach ($dataDetail['rows'] as $k => $v) {
			$row=array();
			$row['id'] = $k;
			$row['cell'][]=$v['date_time'];
			if($v['has_repay']==0){
				$row['cell'][] = $v['count_deal'];
				$row['cell'][] = $v['over_times'];
				$row['cell'][] = '￥'.$v['sum_repay_manage_money'];
				$row['cell'][] = '￥'.$v['sum_repay_money'];
				$row['cell'][] = '￥'.$v['sum_self_money'];
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
			}else{
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = '';
				$row['cell'][] = $v['count_deal'];
				$row['cell'][] = $v['over_times'];
				$row['cell'][] = '￥'.$v['sum_repay_manage_money'];
				$row['cell'][] = '￥'.$v['sum_repay_money'];
				$row['cell'][] = '￥'.$v['sum_self_money'];
			}
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		$json['page']=$page;
		$json['total']=$dataDetail['total'];
		echo @json_encode($json);
	}
	
	public function do_overdueDay_export() {
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_borrow', 'overdueDay'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_borrow', 'overdueDay'), 'fail', 3, 'message');
		}
		$orderName='date_time';
		$orderSort='desc';
		$select="date_time,has_repay,COUNT(DISTINCT(deal_id)) count_deal,COUNT(id) over_times,SUM(repay_money)+SUM(manage_money) sum_repay_manage_money,SUM(repay_money) sum_repay_money,SUM(self_money) sum_self_money";
		$where=array();
		$multiWhere=array();
		$where['UNIX_TIMESTAMP(date_time) >=']=$startStamp;
		$where['UNIX_TIMESTAMP(date_time) <=']=$endStamp;
		$multiWhere[]=array(array('has_repay'=>1),'and (','');
		$multiWhere[]=array(array('level'=>0),'or',')');
		$daoAnalyze = \Core::dao('loan_dealRepayLateAnalysisDailyDetail');
		$curPage=\Core::getPost('curpage');
		if (!is_numeric($curPage)){
			$count=$daoAnalyze->getCount($where,$multiWhere);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_borrow', 'overdueDay'));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$dataDetail = $daoAnalyze ->getFlexPage($curPage,C('export_perpage'),$select,$where,array($orderName=>$orderSort),"date_time,has_repay",array(),false,$multiWhere);
		$header=array();
		$header['统计时间'] = 'date';
		$header['新增笔数'] = 'integer';
		$header['新增期数'] = 'integer';
		$header['新增管理费'] = 'price';
		$header['新增本息'] = 'price';
		$header['新增本金'] = 'price';
		$header['减少笔数'] = 'integer';
		$header['减少期数'] = 'integer';
		$header['减少管理费'] = 'price';
		$header['减少本息'] = 'price';
		$header['减少本金'] = 'price';
		
		//数据处理
		$exportData=array();
		foreach($dataDetail as $k=>$v){
			$row=array();
			$row[]=$v['date_time'];
			if($v['has_repay']==0){
				$row[] = $v['count_deal'];
				$row[] = $v['over_times'];
				$row[] = '￥'.$v['sum_repay_manage_money'];
				$row[] = '￥'.$v['sum_repay_money'];
				$row[] = '￥'.$v['sum_self_money'];
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
			}else{
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
				$row[] = '0';
				$row[] = $v['count_deal'];
				$row[] = $v['over_times'];
				$row[] = '￥'.$v['sum_repay_manage_money'];
				$row[] = '￥'.$v['sum_repay_money'];
				$row[] = '￥'.$v['sum_self_money'];
			}
			$exportData[]=$row;
		}
		
		$this -> log('导出逾期波动第'.$curPage.'页(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('逾期波动第'.$curPage.'页(' . $datestart . ' - ' . $dateend . ')', $header, $exportData);
	}

}
