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
			$row['cell'][] = $loanBusiness->enumDealStatus(\Core::arrayKeyExists($v['id'],$loanInfos)?\Core::arrayGet(\Core::arrayGet($loanInfos, $v['id']),'deal_status'):0);
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
}
