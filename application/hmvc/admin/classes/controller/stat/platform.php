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
			$row['cell'][] = '<a href=\''.adminUrl('stat_platform','check_detail',array('admin_id'=>$v['admin_id'],'datestart'=>$datestart,'dateend'=>$dateend)).'\'>'.$adminName.'</a>';
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
			\Core::message('请选择日期范围', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
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
		$admin_id=\Core::getPost('admin_id');
		if(!$admin_id || !is_numeric($admin_id)){
			\Core::message('参数错误', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
		}
		//获取admin信息
		$daoAdmin=\Core::dao('sys_admin_admin');
		$admins=$daoAdmin->getAdmin($admin_id,'admin_id,admin_name,admin_real_name');
		$adminName='';
		if($admins){
			$adminRow=$admins[$admin_id];
			$adminName=$adminRow['admin_real_name']?$adminRow['admin_real_name']:$adminRow['admin_name'];
		}else{
			$adminName=$id.'(已删除)';
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view()->set('admin_id',$admin_id);
		\Core::view()->set('admin_name',$adminName);
		\Core::view() -> load('stat_checkDetail');
	}
	
	public function do_check_detail_json(){
		$admin_id=\Core::getPost('admin_id');
		if(!$admin_id || !is_numeric($admin_id)){
			showJSON('100','参数错误');
		}
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
		$orderName='';
		$orderSort='';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$daoAudit = \Core::dao('stat_dealaudit','stat');
		$datas = $daoAudit->getStatCheckDetail($page,$pagesize,$startStamp, $endStamp,$admin_id,$orderName,$orderSort);
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = $v['date_time'];
			$row['cell'][] = $v['totals'];
			$row['cell'][] = $v['success_totals'];
			$row['cell'][] = $v['success_percent']?($v['success_percent']*100).'%':'0%';
			$row['cell'][] = $v['first_totals'];
			$row['cell'][] = $v['first_success_totals'];
			$row['cell'][] = $v['first_success_percent']?($v['first_success_percent']*100).'%':'0%';
			$row['cell'][] = $v['renew_totals'];
			$row['cell'][] = $v['renew_success_totals'];
			$row['cell'][] = $v['renew_success_percent']?($v['renew_success_percent']*100).'%':'0%';
			$row['cell'][] = $v['true_totals'];
			$row['cell'][] = $v['true_success_totals'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
	
	//审核汇总--人员汇总 导出Excel
	public function do_check_detail_export(){
		$admin_id=\Core::getPost('admin_id');
		$admin_name=\Core::getPost('admin_name','');
		if(!$admin_id || !is_numeric($admin_id)){
			\Core::message('参数错误', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
		}
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			\Core::message('请选择日期范围', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
		}
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		if($startStamp>$endStamp){
			\Core::message('开始日期不能大于结束日期', adminUrl('stat_platform', 'check'), 'fail', 3, 'message');
		}
		$id = \Core::getPost("id");
		$where = array();
		$orderby = array();
		$ids=array();
		if (preg_match('/^[\d,]+$/', $id)) {
			$ids = explode(",", $id);
		}
		//排序
		$orderName='';
		$orderSort='';
		if (\Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		//获得记录条数，看是否需要分页下载
		$where=array();
		$where['unix_timestamp(date_time) >=']=$startStamp;
		$where['unix_timestamp(date_time) <=']=$endStamp;
		$where['admin_id']=$admin_id;
		if($ids){
			$where['id']=$ids;
		}
		$daoAudit = \Core::dao('stat_dealaudit','stat');
		$curPage=\Core::getPost('curpage');
		if (!is_numeric($curPage)){
			$count=$daoAudit->getCount($where);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_platform', 'check'));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
  		$datas = $daoAudit->getStatCheckDetail($curPage,C('export_perpage'),$startStamp, $endStamp,$admin_id,$orderName,$orderSort,$ids);
        //Excel头部
		$header = array();
		$header['ID'] = 'integer';
		$header['日期'] = 'string';
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
		$this -> log('导出审核人员汇总('.$admin_name.' '. $datestart . ' - ' . $dateend . ')', 'export');
		exportExcel('导出审核人员汇总('.$admin_name.' '. $datestart . ' - ' . $dateend . ')', $header, $datas['rows']);
	}
	
	//自动投标
	public function do_autoBid(){
		
	}
}