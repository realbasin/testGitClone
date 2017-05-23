<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_loan_loan extends controller_sysBase {
	
	public function before($method, $args) {
		\Language::read('loan');
	}

	public function do_index() {
		$this -> do_all();
	}
	
	//全部贷款
	public function do_all(){
		$loanBusiness=\Core::business('loan_loanenum');
		//贷款类型数据
		\Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
		->set('loantype',$loanBusiness->enumLoanType())
		->set('dealcate',$loanBusiness->enumDealCate())
		->set('dealusetype',$loanBusiness->enumDealUseType())
		->set('sorcode',$loanBusiness->enumSorCode())
		->set('dealstatus',$loanBusiness->enumDealStatus());
		\Core::view() -> load('loan_loanlist');
	}
	
	//新增贷款
	public function do_add(){
		$loanBusiness=\Core::business('loan_loanenum');
		\Core::view()->set('loantype',$loanBusiness->enumLoanType())
		->set('dealcate',$loanBusiness->enumDealCate())
		->set('dealusetype',$loanBusiness->enumDealUseType())
		->set('dealloantype',$loanBusiness->enumDealLoanType())
		->set('sorcode',$loanBusiness->enumSorCode());
		\Core::view() -> load('loan_loanadd');
	}
	//还款计划
	public function do_repay_plan(){
		// 用户余额
		$loan_id = \Core::get('loan_id',0);
		$userDao = \Core::dao('user_user');
		$user = \Core::dao('loan_loanbase')->getLoan($loan_id,'id,user_id');
		$user_id = $user[$loan_id]['user_id'];
		$user_money = $userDao->getUser($user_id,'id,AES_DECRYPT(money_encrypt,'."'__FANWEP2P__'".') AS money');
		$money = $user_money[$user_id]['money']?$user_money[$user_id]['money']:0.00;
		//TODO 需还总额 $data['l_key']=$money
		//$loanBusiness = \Core::business('loan_loanenum');
		//$loan_data = $loanBusiness->enumLoanRepay($loan_id);
		//\Core::dump($loan_data);die();
		\Core::view()->set('loan_id',$loan_id)->set('usermoney',$money)->load('loan_repayplan');
	}
	//手动还款
	public function do_manual_repay(){
		$data = array();
		$data['code'] = 000;
		$loan_id = \Core::get('id',0);
		if(!$loan_id) {
			$data['message'] = \Core::L('fail');
			echo @json_encode($data);
			exit;
		}
		$l_key = \Core::get('lkey',-1);
		$user = \Core::dao('loan_loanbase')->getLoan($loan_id,'id,user_id');
		if(!$user){
			$data['message'] = \Core::L('no_loan');
			echo @json_encode($data);
			exit;
		}
		$user_id = $user[$loan_id]['user_id'];
		//提前还款（批量所有（未还）期）
		/*if($l_key < 0) {

		}else {
			//正常还款（单期）
		}*/
		//执行还款
		$status = \Core::business('loan_loanenum')->repayLoanBills($loan_id,$l_key,$user_id);
		if($status == 0) {
			$data['code'] = 200;
			echo @json_encode($data);
		}else {
			$data['message'] = $status['show_err'];
			echo @json_encode($data);
		}

	}
	//投标详情
	public function do_detail(){
		$loan_id = \Core::get('loan_id',0);
		$loanbidDao = \Core::dao('loan_loanbid');
		$loanbaseDao = \Core::dao('loan_loanbase');
		$loan_bid_info = $loanbidDao->getLoan($loan_id,'loan_id,start_time,load_money,loan_time,repay_start_time,bad_time,deal_status,buy_count,is_has_loans,end_time,is_has_received');
		$loan_base_info = $loanbaseDao->getLoan($loan_id,'id,name,borrow_amount,repay_time_type');
		$loan = array_merge($loan_base_info[$loan_id],$loan_bid_info[$loan_id]);
		//\Core::dump($loan);
		if($loan['repay_time_type'] == 1) {
			$loan['repay_time_type'] = '按月还款';
		}else {
			$loan['repay_time_type'] = '按天还款';
		}
		$loan_time = $loan['start_time']+$loan['end_time'];
		if(($loan_time - 1)<time()){
			$loan['is_over_time'] = 1;
		}else {
			$loan['is_over_time'] = 0;
		}
		$loan['need_money'] = number_format($loan['borrow_amount'] - $loan['load_money'],2);
		\Core::view()->set('loan',$loan)->load('loan_detail');
	}
	//贷款审核日志
	public function do_audit_log(){
		\Core::view()->set('loan_id',\Core::get('loan_id',0))->load('loan_auditlog');
	}
	//审核日志列表（全部）
	public function do_op_log(){
		\Core::view()->set('loan_id',\Core::get('loan_id',0))->load('loan_oplog');
	}
	//获取全部贷款分页JSON数据
	public function do_all_json(){
		//每页显示行数
		$pagesize = \Core::postGet('rp');
		//当前页
		$page = \Core::postGet('curpage');
		//需要获取的字段
		$fields = 'id,name,user_id,borrow_amount,rate,repay_time,loantype,loan_status,sor_code,first_audit_admin_id,repay_time_type,second_audit_admin_id';
		//查询条件
		$where = array();
		$bidwhere = array();
		//排序
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//固定查询条件
		$where['is_delete']=0;
		$where['publish_wait']=0;
		//简易查询条件
		if (\Core::postGet('query')) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//高级查询条件
		if(\Core::get('id')!=null && is_numeric(\Core::get('id'))){
			$where['id like']="%".\Core::get('id')."%";
		}
		if(\Core::get('name')!=null){
			$where['name like']="%".\Core::get('name')."%";
		}
		if(\Core::get('borrow_amount')!=null && is_numeric(\Core::get('borrow_amount'))){
			$where['borrow_amount like']="%".\Core::get('borrow_amount')."%";
		}
		if(\Core::get('rate')!=null && is_numeric(\Core::get('rate'))){
			$where['rate like']="%".\Core::get('rate')."%";
		}
		if(\Core::get('repay_time')!=null && is_numeric(\Core::get('repay_time'))){
			$where['repay_time like']="%".\Core::get('repay_time')."%";
		}
		if(\Core::get('repay_time_type')!=null && \Core::get('repay_time_type')!='-1'){
			$where['repay_time_type']=\Core::get('repay_time_type');
		}
		if(\Core::get('loantype')!=null && \Core::get('loantype')!='-1'){
			$where['loantype']=\Core::get('loantype');
		}
		if(\Core::get('cate_id')!=null && \Core::get('cate_id')!='-1'){
			$where['cate_id']=\Core::get('cate_id');
		}
		if(\Core::get('use_type')!=null && \Core::get('use_type')!='-1'){
			$where['use_type']=\Core::get('use_type');
		}
		if(\Core::get('sor_code')!=null && \Core::get('sor_code')!='-1'){
			$where['sor_code']=\Core::get('sor_code');
		}
		//贷款状态，转到loan_bid表
		if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
			$bidwhere['deal_status']=\Core::get('deal_status');
			$loanId = \Core::dao('loan_loanbid')->getIds($bidwhere);
			$where['id '] = $loanId;
		}
		//已迁移到loan_bid表 修改为根据流标状态获取贷款id，获取在该id数组中的贷款
		if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
			$bidwhere['is_has_received'] =\Core::get('is_has_received');
			$loanId = \Core::dao('loan_loanbid')->getIds($bidwhere);
			$where['id '] = $loanId;
		}
		if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
			$where['user_id like']="%".\Core::get('user_id')."%";
		}
		$userDao=\Core::dao('user_user');
		//贷款人姓名模糊查询
		if(\Core::get('user_name')!=null && (\Core::get('user_mobile') == null && !is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(real_name_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;
		}
		//TODO贷款人手机查询
		if(\Core::get('user_name') == null && (\Core::get('user_mobile')!=null && is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;

		}
		//手机号和姓名组合
		if(\Core::get('user_name') != null && (\Core::get('user_mobile')!=null && is_numeric(\Core::get('user_mobile')))) {
			$searchIdsWhere["AES_DECRYPT(real_name_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_name')."%";
			$searchIdsWhere["AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') like"] = "%".\Core::get('user_mobile')."%";
			$searchuserIds = $userDao->getUsersIdsByMobileAndName($searchIdsWhere,'id',$pagesize);
			$where['user_id '] = $searchuserIds;

		}
		//简易排序条件
		if (\Core::postGet('sortorder')) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}
		
		$data = \Core::dao('loan_loanbase') -> getFlexPage($page, $pagesize, $fields, $where, $orderby,'id');
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		
		$loanBusiness=\Core::business('loan_loanenum');
		
		//查询用户名称与管理员名称
		$userIds=array();
		$adminFirstIds=array();
		$adminSecondIds=array();
		$loanIds = array();
		$userPids = array();
		$pidNames = array();
		if(!($data['rows'])) {
			echo @json_encode($json);
			exit;
		}
		foreach ($data['rows'] as $v) {
			$userIds[]=$v['user_id'];
			$adminFirstIds[]=$v['first_audit_admin_id'];
			$adminSecondIds[]=$v['second_audit_admin_id'];
			$loanIds[] = $v['id'];
		}

		$adminDao=\Core::dao('sys_admin_admin');
		$loanbidDao = \Core::dao('loan_loanbid');
		$userNames=$userDao->getUser($userIds,'id,user_name,real_name,pid');
		$firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');
		$secondAdminNames=$adminDao->getAdmin($adminSecondIds,'admin_id,admin_name,admin_real_name,admin_mobile');
		$loanInfos = $loanbidDao->getLoan($loanIds,'loan_id,is_has_loans,is_has_received,buy_count,deal_status');

		foreach ($userNames as $v) {
			$userPids[] = $v['pid'];
		}
		$pidNames=$userDao->getUser($userPids,'id,user_name,real_name');

		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";

			if($v['loan_status']>=1)
			{
				$opration.="<li><a href='javascript:loan_repay_plan(".$v['id'].")'>还款计划</a></li>";
				$opration.="<li><a href='javascript:loan_detail(".$v['id'].")'>投标详情</a></li>";
			}
			$opration.="<li><a href='javascript:loan_preview(".$v['id'].")'>预览</a></li>";
			$opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
			$opration.="</ul></span>";
			$row['cell'][] = $opration;
			$row['cell'][] = $v['id'];
			$row['cell'][] = "<a href='javascript:loan_show(".$v['id'].")'>".$v['name']."</a>";
			$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
			$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($pidNames, $userNames[$v['user_id']]['pid']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($pidNames, $userNames[$v['user_id']]['pid']),'real_name').')':'';

			$row['cell'][] = "￥".$v['borrow_amount'];
			$row['cell'][] = $v['rate']."%";
			$row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
			$row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
			$row['cell'][] = $loanBusiness->enumDealStatus(\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'deal_status'):1);
			$row['cell'][] = (\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'is_has_loans'):0)?\Core::L('yes'):\Core::L('no');
			$row['cell'][] = (\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'is_has_received'):0)?\Core::L('yes'):\Core::L('no');
			$row['cell'][] = \Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'buy_count'):0;
			$row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);

			$row['cell'][] = \Core::arrayKeyExists($v['first_audit_admin_id'], $firstAdminNames)?\Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name'):($v['first_audit_admin_id']=='-1'?'自动审核':'');
			$row['cell'][] = \Core::arrayKeyExists($v['second_audit_admin_id'], $secondAdminNames)?\Core::arrayGet(\Core::arrayGet($secondAdminNames, $v['second_audit_admin_id'],''),'admin_real_name'):'';
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
	//获取还款计划JSON数据
	public function do_all_repay_plan_json(){

		$repayPlanDao = \Core::dao('loan_dealrepay');
		$data = $repayPlanDao->getRepayPlan(\Core::get('loan_id'),'*');

		//处理返回结果
		$json = array();
		$json['total'] = 0;
		if(!($data)) {
			echo @json_encode($json);
			exit;
		}
		//获取普通配置中的罚息利率等配置 loan_ext表的config_common字段
		$loanextDao = \Core::dao('loan_loanext');
		$config_common = $loanextDao->getConfig('config_common');
		$loadrepayDao = \Core::dao('loan_loadrepay');
		$loanenumBusiness = \Core::business('loan_loanenum');
		foreach ($data as $v) {
			$row = array();
			$overdue_day = 0;
			//判断是否逾期，计算应还金额等
			//是否还款
			if($v['has_repay'] == 1) {
				//已还总额
				$isrepay = $v['true_repay_money'];
				//待还总额
				$repay_all_money = '0.00';
				//待还本息
				$repay_money = '0.00';
				//管理费
				$manage_money = $v['true_manage_money'];
				//逾期/违约金
				$impose_money = $v['impose_money'];
				//逾期/违约金管理费
				$manage_impose_money = $v['manage_impose_money'];
				//还款情况
				$status = $v['status'] + 1;
				$repaydate = date('Y-m-d H:i:s',$v['true_repay_time']);
			}elseif($v['has_repay'] == 0) {
				//未还款状态
				//已还总额
				$isrepay = '0.00';
				//判断是否罚息
				$time = time();
				if($time > ($v['repay_time'] + 24 * 3600 - 1) && $v['repay_money'] > 0){
					$status = 3;
					//计算逾期时间,设置还款状态
					$overdue_day = ceil((strtotime(date('Y-m-d',$time)) - $v['repay_time']) / (3600 * 24));
					//根据日期判断是否严重逾期 获取费率
					//费率修改为拓展表loan_ext中普通配置字段config_common中获取
					if ($overdue_day >= C('YZ_IMPSE_DAY')) {
						$status = 4;
						$impose_fee = trim($config_common['impose_fee_day2']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day2']);
					}else {
						$impose_fee = trim($config_common['impose_fee_day1']);
						$manage_impose_fee = trim($config_common['manage_impose_fee_day1']);
					}
					$impose_fee = floatval($impose_fee);
					$manage_impose_fee = floatval($manage_impose_fee);
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = number_format($repay_money * $impose_fee * $overdue_day / 100,2);
					//罚管理费
					$manage_impose_money = number_format($repay_money * $manage_impose_fee * $overdue_day / 100,2);
					$impose_all_money = $impose_money + $manage_impose_money;
					$repay_all_money = $repay_money + $manage_money + $impose_all_money;
					$repaydate = '';
				}else {
					//未逾期
					$status = $v['status'];
					$repay_money = $v['repay_money'];
					$manage_money = $v['manage_money'];
					//罚息
					$impose_money = 0;
					//罚管理费
					$manage_impose_money = 0;
					$repay_all_money = $repay_money + $manage_money;
					$repaydate = '';
				}
			}
			$opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
			if($v['has_repay'] == 0) {
				$opration.="<li><a href='javascript:manual_repay(".$v['deal_id'].",".$v['l_key'].",".$repay_all_money.")'>手动还款</a></li>";
			}
			//TODO 网站资金代还判断 deal_load_repay表is_site_repay字段
			$where = array();
			$where['deal_id'] = $v['deal_id'];
			$where['l_key'] = $v['l_key'];
			if($loadrepayDao->getIsSiteRepay($where) == 0){
				$opration.="<li><a href='javascript:site_repay(".$v['deal_id'].")'>网站资金代还款</a></li>";
			}
			$opration.="<li><a href='javascript:repay_plan_export_load(".$v['deal_id'].")'>导出还款计划列表</a></li>";
			$opration.="</ul></span>";
			$row['cell'][] = $opration;
			$l_key = $v['l_key'] + 1;
			$row['cell'][] = '第'  .$l_key.'期';
			$row['cell'][] = $v['repay_date'];
			//已还总额
			$row['cell'][] = '￥'.$isrepay;
			//待还总额
			$row['cell'][] = '￥'.$repay_all_money;
			//待还本息
			$row['cell'][] = '￥'.$repay_money;
			//管理费
			$row['cell'][] = '￥'.$manage_money;
			//逾期/违约金
			$row['cell'][] = '￥'.$impose_money;
			//逾期/违约金管理费
			$row['cell'][] = '￥'.$manage_impose_money;
			//还款状态
			$row['cell'][] = $loanenumBusiness->enumLoanRepayType($status);
			//还款时间
			$row['cell'][] = $repaydate;
			//逾期天数
			$row['cell'][] = $overdue_day?$overdue_day:0;
			$row['cell'][] = '<a href="#">查看</a>';
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		$json['total'] =count($data);
		unset($data);
		echo @json_encode($json);

	}
	//获取贷款审核日志分页JSON数据
	public function do_all_op_log_json(){
		//每页显示行数
		$pagesize = \Core::postGet('rp');
		//当前页
		$page = \Core::postGet('curpage');
		//需要获取的字段
		$fields = 'id,deal_id,user_id,op_id,op_name,op_result,log,admin_id,create_time,ip';
		//查询条件
		$where = array();
		$bidwhere = array();
		//排序
		$orderby = array();
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		//简易查询条件
		if (\Core::postGet('query')) {
			$where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
		}
		//高级查询条件
		if(\Core::get('deal_id')!=null && is_numeric(\Core::get('deal_id'))){
			$where['deal_id ']=\Core::get('deal_id');
		}
		if(\Core::get('op_type')!=-1 && is_numeric(\Core::get('op_type'))){

			$where['op_id ']=\Core::get('op_type');
		}
		if(\Core::get('time')!=null){
			$time = \Core::get('time');
			if($time == 'today') {
				$start_time = strtotime(date('Y-m-d'));
				$end_time = strtotime(date('Y-m-d'));
			}

		}
		$userDao=\Core::dao('user_user');
		//简易排序条件
		if (\Core::postGet('sortorder')) {
			$orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
		}

		$data = \Core::dao('loan_loanoplog') -> getFlexPage($page, $pagesize, $fields, $where, $orderby,'id');
		//处理返回结果
		$json = array();
		$json['page'] = $page;
		$json['total'] = $data['total'];
		//查询用户名称与管理员名称
		$userIds=array();
		$adminFirstIds=array();
		$adminSecondIds=array();
		if(!($data['rows'])) {
			echo @json_encode($json);
			exit;
		}
		foreach ($data['rows'] as $v) {
			$userIds[]=$v['user_id'];
			$adminIds[]=$v['admin_id'];
		}
		$adminDao=\Core::dao('sys_admin_admin');
		$userNames=$userDao->getUser($userIds,'id,user_name,real_name,pid');
		$AdminNames=$adminDao->getAdmin($adminIds,'admin_id,admin_name,admin_real_name,admin_mobile');

		foreach ($data['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = $v['deal_id'];
			$row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
			$row['cell'][] = $v['op_name'];
			$row['cell'][] = $v['log'];
			$row['cell'][] = $v['op_result'];
			$row['cell'][] = \Core::arrayKeyExists($v['admin_id'], $AdminNames)?\Core::arrayGet(\Core::arrayGet($AdminNames, $v['admin_id']),'admin_name').'('.\Core::arrayGet(\Core::arrayGet($AdminNames, $v['admin_id']),'admin_real_name').')':'';
			$row['cell'][] = $v['create_time']?date('Y-m-d H:i:s',$v['create_time']):'';
			$row['cell'][] = $v['ip'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
}
