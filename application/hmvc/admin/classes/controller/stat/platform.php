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
	
	//从统计库中获取数据
	public function do_check_json(){
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
		//排序
		$ordername='';
		$ordersort='desc';
		if (\Core::postGet('sortorder')) {
			$ordername=\Core::postGet('sortname');
			$ordersort=\Core::postGet('sortorder');
		}
		$daoAudit = \Core::dao('stat_dealaudit','stat');
		$datas = $daoAudit->getStatCheck($startStamp, $endStamp,$ordername,$ordersort);
		$total=count($datas);
		$json = array();
		$json['page'] = 1;
		$json['total'] = $total;
		//获取管理员姓名
		$ids=array_keys($datas);
		$daoAdmin=\Core::dao('sys_admin_admin');
		$admins=$daoAdmin->getAdmin($ids,'admin_id,admin_name,admin_real_name');
		//整理数据
		$s_total_deals=0;
		$s_success_deals=0;
		$s_success_percent=0;
		$s_first_check_deals=0;
		$s_first_success_deals=0;
		$s_first_success_percent=0;
		$s_renew_check_deals=0;
		$s_renew_success_deals=0;
		$s_renew_success_percent=0;
		$s_true_deals=0;
		$s_true_success_deals=0;
		
		foreach($datas as $k=>$v){
			$row = array();
			$id=$v['admin_id'];
			$adminName='';
			if(\Core::arrayKeyExists($id, $admins)){
				$adminRow=$admins[$id];
				$adminName=$adminRow['admin_real_name']?$adminRow['admin_real_name']:$adminRow['admin_name'];
			}else{
				$adminName=$id.'(已删除)';
			}
			$row['id'] = $v['admin_id'];
			$row['cell'][] = '<a href=\''.adminUrl('stat_platform','check_detail').'\'>'.$adminName.'</a>';
			$row['cell'][] = $v['total_deals'];
			$row['cell'][] = $v['success_deals'];
			$row['cell'][] = $v['success_percent']?($v['success_percent']*100).'%':'0%';
			$row['cell'][] = $v['first_check_deals'];
			$row['cell'][] = $v['first_success_deals'];
			$row['cell'][] = $v['first_success_percent']?($v['first_success_percent']*100).'%':'0%';
			$row['cell'][] = $v['renew_check_deals'];
			$row['cell'][] = $v['renew_success_deals'];
			$row['cell'][] = $v['renew_success_percent']?($v['renew_success_percent']*100).'%':'0%';
			$row['cell'][] = $v['true_deals'];
			$row['cell'][] = $v['true_success_deals'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
			
			$s_total_deals+=$v['total_deals'];
			$s_success_deals+=$v['success_deals'];
			$s_success_percent += $v['success_percent']?$v['success_percent']:0;
			$s_first_check_deals+= $v['first_check_deals'];
			$s_first_success_deals+= $v['first_success_deals'];
			$s_first_success_percent+= $v['first_success_percent']?$v['first_success_percent']:0;
			$s_renew_check_deals += $v['renew_check_deals'];
			$s_renew_success_deals+= $v['renew_success_deals'];
			$s_renew_success_percent+= $v['renew_success_percent']?$v['renew_success_percent']:0;
			$s_true_deals+= $v['true_deals'];
			$s_true_success_deals+= $v['true_success_deals'];
		}
		//合计数据
		$s_success_percent=(($s_success_percent*100)/$total).'%';
		$s_first_success_percent=(($s_first_success_percent*100)/$total).'%';
		$s_renew_success_percent=(($s_renew_success_percent*100)/$total).'%';
		$row=array();
		$row['id'] = 0;
		$row['cell'][] = '合计';
		$row['cell'][] = $s_total_deals;
		$row['cell'][] = $s_success_deals;
		$row['cell'][] = $s_success_percent;
		$row['cell'][] = $s_first_check_deals;
		$row['cell'][] = $s_first_success_deals;
		$row['cell'][] = $s_first_success_percent;
		$row['cell'][] = $s_renew_check_deals;
		$row['cell'][] = $s_renew_success_deals;
		$row['cell'][] = $s_renew_success_percent;
		$row['cell'][] = $s_true_deals;
		$row['cell'][] = $s_true_success_deals;
		$row['cell'][] = '';
		$json['rows'][] = $row;
		
		echo @json_encode($json);
	}
	
	public function do_check_export(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_platform', 'check_json'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_platform', 'check_json'), 'fail', 3, 'message');
		}
		//排序
		$ordername='';
		$ordersort='desc';
		if (\Core::postGet('sortorder')) {
			$ordername=\Core::postGet('sortname');
			$ordersort=\Core::postGet('sortorder');
		}
		$daoAudit = \Core::dao('stat_dealaudit','stat');
		$datas = $daoAudit->getStatCheck($startStamp, $endStamp,$ordername,$ordersort);
		$total=count($datas);
		//获取管理员姓名
		$ids=array_keys($datas);
		$daoAdmin=\Core::dao('sys_admin_admin');
		$admins=$daoAdmin->getAdmin($ids,'admin_id,admin_name,admin_real_name');
		//整理数据
		$s_total_deals=0;
		$s_success_deals=0;
		$s_success_percent=0;
		$s_first_check_deals=0;
		$s_first_success_deals=0;
		$s_first_success_percent=0;
		$s_renew_check_deals=0;
		$s_renew_success_deals=0;
		$s_renew_success_percent=0;
		$s_true_deals=0;
		$s_true_success_deals=0;
		
		foreach($datas as $k=>$v){
			$row = array();
			$id=$v['admin_id'];
			$adminName='';
			if(\Core::arrayKeyExists($id, $admins)){
				$adminRow=$admins[$id];
				$adminName=$adminRow['admin_real_name']?$adminRow['admin_real_name']:$adminRow['admin_name'];
			}else{
				$adminName=$id.'(已删除)';
			}
			$row[] = $adminName;
			$row[] = $v['total_deals'];
			$row[] = $v['success_deals'];
			$row[] = $v['success_percent']?($v['success_percent']*100).'%':'0%';
			$row[] = $v['first_check_deals'];
			$row[] = $v['first_success_deals'];
			$row[] = $v['first_success_percent']?($v['first_success_percent']*100).'%':'0%';
			$row[] = $v['renew_check_deals'];
			$row[] = $v['renew_success_deals'];
			$row[] = $v['renew_success_percent']?($v['renew_success_percent']*100).'%':'0%';
			$row[] = $v['true_deals'];
			$row[] = $v['true_success_deals'];
			$datas[$k] = $row;
			
			$s_total_deals+=$v['total_deals'];
			$s_success_deals+=$v['success_deals'];
			$s_success_percent += $v['success_percent']?$v['success_percent']:0;
			$s_first_check_deals+= $v['first_check_deals'];
			$s_first_success_deals+= $v['first_success_deals'];
			$s_first_success_percent+= $v['first_success_percent']?$v['first_success_percent']:0;
			$s_renew_check_deals += $v['renew_check_deals'];
			$s_renew_success_deals+= $v['renew_success_deals'];
			$s_renew_success_percent+= $v['renew_success_percent']?$v['renew_success_percent']:0;
			$s_true_deals+= $v['true_deals'];
			$s_true_success_deals+= $v['true_success_deals'];
		}
		//合计数据
		$s_success_percent=(($s_success_percent*100)/$total).'%';
		$s_first_success_percent=(($s_first_success_percent*100)/$total).'%';
		$s_renew_success_percent=(($s_renew_success_percent*100)/$total).'%';
		$row=array();
		$row[] = '合计';
		$row[] = $s_total_deals;
		$row[] = $s_success_deals;
		$row[] = $s_success_percent;
		$row[] = $s_first_check_deals;
		$row[] = $s_first_success_deals;
		$row[] = $s_first_success_percent;
		$row[] = $s_renew_check_deals;
		$row[] = $s_renew_success_deals;
		$row[] = $s_renew_success_percent;
		$row[] = $s_true_deals;
		$row[] = $s_true_success_deals;
		$datas[] = $row;
		
		//Excel头部
		$header = array();
		$header['姓名'] = 'string';
		$header['审核笔数'] = 'integer';
		$header['审核成功数'] = 'integer';
		$header['审核成功率'] = 'string';
		$header['首借审核数'] = 'integer';
		$header['首借审核成功数'] = 'integer';
		$header['首借审核成功率'] = 'string';
		$header['续借审核数'] = 'integer';
		$header['续借审核成功数'] = 'integer';
		$header['续借审核成功率'] = 'string';
		$header['复审总数'] = 'integer';
		$header['复审成功数'] = 'integer';
		//导出
		$this -> log('导出审核汇总(' . $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('审核汇总(' . $datestart . ' - ' . $dateend . ')', $header, $datas);
	}

	//审核汇总--人员汇总
	public function do_check_detail(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> load('stat_checkDetail');
	}
	
	//自动投标
	public function do_autoBid(){
		
	}
}