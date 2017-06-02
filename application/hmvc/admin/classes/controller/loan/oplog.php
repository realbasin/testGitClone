<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_loan_oplog extends Controller {
	public function do_index() {
		$this -> do_op_log();
	}
	//贷款审核日志
	public function do_audit_log(){
		\Core::view()->set('loan_id',\Core::get('loan_id',0))->load('loan_auditlog');
	}
	//审核日志列表（全部）
	public function do_op_log(){
		\Core::view()->load('loan_oplog');
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
		if(\Core::get('loan_id')!=null && is_numeric(\Core::get('loan_id'))){
			$where['deal_id ']=\Core::get('loan_id');
		}
		if(\Core::get('op_type')!=-1 && is_numeric(\Core::get('op_type'))){
			$where['op_id ']=\Core::get('op_type');
		}
		if(\Core::get('datestart')!=null || \Core::get('dateend')!=null){

			$where['create_time >'] = strtotime(\Core::get('datestart'));
			$where['create_time <'] = strtotime(\Core::get('dateend').' 23:59:59');
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