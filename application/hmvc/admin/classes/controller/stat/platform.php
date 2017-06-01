<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 *平台统计
 */
class  controller_stat_platform extends controller_sysBase {
	public function before() {
		
	}
	
	//充值统计
	public function do_recharge(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_recharge');
	}
	
	public function do_recharge_json(){
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
		if((($endStamp-$startStamp)/86400+1)>C('stat_date_range_max')){
			showJSON('102', '日期范围不能大于'.C('stat_date_range_max').'天');
		}
		$daoPayment = \Core::dao('loan_paymentnotice');
		$datas = $daoPayment->getStatRecharge($startStamp, $endStamp);
		if(!$datas){
			$row=array();
			$row['paydate']=$datestart;
			$row['paytotal']=0;
			$datas[]=$row;
			$row['paydate']=$dateend;
			$datas[]=$row;
		}
		showJSON('200', '', $datas);
	}
	
	public function do_recharge_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['成功充值金额'] = 'price';

		$daoPayment = \Core::dao('loan_paymentnotice');
		$datas = $daoPayment->getStatRecharge(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出充值金额汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('充值金额汇总(' . $datestart . ' - ' . $dateend . ')', $header, $datas);
	}
	
	//提现统计
	public function do_withdraw(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_withdraw');
	}
	
	public function do_withdraw_json(){
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
		if((($endStamp-$startStamp)/86400+1)>C('stat_date_range_max')){
			showJSON('102', '日期范围不能大于'.C('stat_date_range_max').'天');
		}
		$daoCarry = \Core::dao('loan_usercarry');
		$datas = $daoCarry->getStatWithdraw($startStamp, $endStamp);
		if(!$datas){
			$row=array();
			$row['createdate']=$datestart;
			$row['moneytotal']=0;
			$row['moneytotalsuc']=0;
			$row['usertimes']=0;
			$datas[]=$row;
			$row['createdate']=$dateend;
			$datas[]=$row;
		}
		showJSON('200', '', $datas);
	}
	
	public function do_withdraw_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['申请提现总额'] = 'price';
		$header['成功提现总额'] = 'price';
		$header['申请人次'] = 'integer';

		$daoCarry = \Core::dao('loan_usercarry');
		$datas = $daoCarry->getStatWithdraw(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出提现汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('提现汇总(' . $datestart . ' - ' . $dateend . ')', $header, $datas);
	}
	
	//用户注册人数统计
	public function do_userRegist(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_userRegist');
	}
	
	public function do_userRegist_json(){
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
		if((($endStamp-$startStamp)/86400+1)>C('stat_date_range_max')){
			showJSON('102', '日期范围不能大于'.C('stat_date_range_max').'天');
		}
		$daoUser = \Core::dao('user_user');
		$datas = $daoUser->getStatUserRegist($startStamp, $endStamp);
		if(!$datas){
			$row=array();
			$row['createdate']=$datestart;
			$row['usercount']=0;
			$datas[]=$row;
			$row['createdate']=$dateend;
			$datas[]=$row;
		}
		showJSON('200', '', $datas);
	}
	
	public function do_userRegist_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['注册人数'] = 'integer';

		$daoUser = \Core::dao('user_user');
		$datas = $daoUser->getStatUserRegist(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出用户注册统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('用户注册统计(' . $datestart . ' - ' . $dateend . ')', $header, $datas);
	}
	
	//垫付统计
	public function do_platformPayment(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_platformPayment');
	}
	
	public function do_platformPayment_json(){
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
		if((($endStamp-$startStamp)/86400+1)>C('stat_date_range_max')){
			showJSON('102', '日期范围不能大于'.C('stat_date_range_max').'天');
		}

		$daoPayment = \Core::dao('loan_generationrepay');
		$datas = $daoPayment->getStatPlatformPayment($startStamp, $endStamp);
		if(!$datas){
			$row=array();
			$row['createdate']=$datestart;
			$row['paymenttotal']=0;
			$row['feetotal']=0;
			$row['imposetotal']=0;
			$row['managetotal']=0;
			$datas[]=$row;
			$row['createdate']=$dateend;
			$datas[]=$row;
		}
		showJSON('200', '', $datas);
	}
	
	public function do_platformPayment_export(){
		$datestart = \Core::get('datestart');
		$dateend = \Core::get('dateend');
		$datestart = $datestart ? $datestart : date('Y-m-d', strtotime('-30 day'));
		$dateend = $dateend ? $dateend : date('Y-m-d', time());
		//Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['代还本息总额'] = 'price';
		$header['代还管理费总额'] = 'price';
		$header['代还罚息总额'] = 'price';
		$header['代还逾期管理费总额'] = 'price';

		$daoPayment = \Core::dao('loan_generationrepay');
		$datas = $daoPayment->getStatPlatformPayment(strtotime($datestart), strtotime($dateend));
		//导出
		$this -> log('导出网站垫付统计(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('网站垫付统计(' . $datestart . ' - ' . $dateend . ')', $header, $datas);
	}
	
	//审核汇总
	public function do_check(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_check');
	}
	
	public function do_check_json(){
		
	}
	
	public function do_check_export(){
		
	}
	
	//自动投标
	public function do_autoBid(){
		
	}
}