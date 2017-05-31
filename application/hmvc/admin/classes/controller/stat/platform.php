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
		
	}
	
	//用户统计
	public function do_user(){
		
	}
	
	//垫付统计
	public function do_platformPayment(){
		
	}
	
	//审核汇总
	public function do_check(){
		
	}
	
	//自动投标
	public function do_autoBid(){
		
	}
}