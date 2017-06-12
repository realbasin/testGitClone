<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 行长统计
 */
class controller_stat_distributor extends controller_sysBase {
	public function before() {
		
	}
	
	//校园行长
	public function do_schoolDistributor(){
		//获取业务员列表
		$bAgent=\Core::business('agent_agentEnum');
		$agents=$bAgent->enumAgent();
		\Core::view()->set('agents',$agents) -> load('stat_schoolDistributor');
	}
	
	//获取行长列表
	public function do_schoolDistributor_json(){
		$user_name=\Core::getPost('user_name');
		$real_name=\Core::getPost('real_name');
		$mobile=\Core::getPost('mobile');
		$admin_id=\Core::getPost('admin_id');
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='id';
		$orderSort='desc';
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$where=array();
		$select='';
		if($user_name){
			$where['user_name like']="%".$user_name."%";
		}
		if($real_name){
			$where["AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') like"]="%".$real_name."%";
		}
		if($mobile){
			$where["AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."') like"]="%".$mobile."%";
		}
		if($admin_id>-1){
			$where['admin_id']=$admin_id;
		}
		$where['user_type']=C('USER_MARK_SALESMAN');
		$where['rpid']=0;
		$where['is_delete']=0;
		$select="
		id,
		user_name,
		AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,
		AES_DECRYPT(email_encrypt,'".AES_DECRYPT_KEY."') as email,
		AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."') as mobile,
		admin_id,
		login_time,
		is_effect";
		$bAgent=\Core::business('agent_agentEnum');
		$daoUser=\Core::dao('user_user');
		$datas=$daoUser->getFlexPage($page,$pagesize,$select,$where,array($orderName=>$orderSort));
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = "<a class='btn blue' onclick='flexEditInfo({$v['id']})'><i class='fa fa-edit'></i> " . \Core::L('edit') . "</a>";
			$row['cell'][] = $v['id'];
			$row['cell'][] = $v['user_name'];
			$row['cell'][] = $v['real_name'];
			$row['cell'][] = $v['email'];
			$row['cell'][] = $v['mobile'];
			$row['cell'][] = $bAgent->enumAgent($v['admin_id']);
			$row['cell'][] = $v['login_time']==0?'':date('Y-m-d H:i:s',$v['login_time']);
			$row['cell'][] = $v['is_effect']?'是':'否';
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
	
	//导出行长列表
	public function do_schoolDistributor_export(){
		$id=\Core::getPost("id");
		$user_name=\Core::getPost('user_name');
		$real_name=\Core::getPost('real_name');
		$mobile=\Core::getPost('mobile');
		$admin_id=\Core::getPost('admin_id');
		
		$curPage=\Core::getPost('curpage');
		
		$idArr=array();
		$where=array();
		$select='';
		if($id){
			$idArr=explode(",", $id);
			$where["id"]=$idArr;
		}else{
			if($user_name){
				$where['user_name like']="%".$user_name."%";
				}
			if($real_name){
				$where["AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') like"]="%".$real_name."%";
			}
			if($mobile){
				$where["AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."') like"]="%".$mobile."%";
			}
			if($admin_id>-1){
				$where['admin_id']=$admin_id;
			}
			$where['user_type']=C('USER_MARK_SALESMAN');
			$where['rpid']=0;
		}
		
		$orderName='id';
		$orderSort='desc';
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		
		$select="
		id,
		user_name,
		AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,
		AES_DECRYPT(email_encrypt,'".AES_DECRYPT_KEY."') as email,
		AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."') as mobile,
		admin_id,
		login_time,
		is_effect";
		
		$bAgent=\Core::business('agent_agentEnum');
		$daoUser=\Core::dao('user_user');
		
		if (!is_numeric($curPage)){
			$count=$daoUser->getCount($where);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_distributor', 'schoolDistributor'));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$datas=$daoUser->getFlexPage($curPage,C('export_perpage'),$select,$where,array($orderName=>$orderSort));
		
		$header=array();
		$export=array();
		
		$header['id']='integer';
		$header['名称']='string';
		$header['真实姓名']='string';
		$header['电子邮件']='string';
		$header['手机号码']='string';
		$header['归属']='string';
		$header['最后登录时间']='datetime';
		$header['是否启用']='string';
		
		foreach ($datas['rows'] as $v) {
			$row = array();
			$row[] = $v['id'];
			$row[] = $v['user_name'];
			$row[] = $v['real_name'];
			$row[] = $v['email'];
			$row[] = $v['mobile'];
			$row[] = $bAgent->enumAgent($v['admin_id']);
			$row[] = $v['login_time']==0?'':date('Y-m-d H:i:s',$v['login_time']);
			$row[] = $v['is_effect']?'是':'否';
			$export[] = $row;
		}
		
		$this -> log('导出行长列表(第'.$curPage.'页)', 'export');
		exportExcel('行长列表(第'.$curPage.'页)', $header, $export);
	}
	
	//增加行长
	public function do_schoolDistributor_add(){
		if (chksubmit()) {
			$user_name=trim(\Core::post('user_name'));
			$email=\Core::post('email');
			$mobile=\Core::post('mobile');
			$user_pwd=\Core::post('user_pwd');
			$is_effect=\Core::post('is_effect');
			$admin_id=\Core::post('admin_id');
			$real_name=\Core::post('real_name');
			$idno=\Core::post('idno');
			if(!$user_name){
				\Core::message('请填写用户名称', '', 'fail', 3, 'message');
			}
			if(!$user_pwd || !preg_match('/.{6,16}/', $user_pwd)){
				\Core::message('用户密码必须是6-16位', '', 'fail', 3, 'message');
			}
			if(!$real_name){
				\Core::message('请填写真实姓名', '', 'fail', 3, 'message');
			}
			if(!$idno){
				\Core::message('请填写身份证号码', '', 'fail', 3, 'message');
			}
			if(!$admin_id){
				\Core::message('请选择归属', '', 'fail', 3, 'message');
			}
			$insert=array();
			$insert['user_name']="'".$user_name."'";
			$insert['user_pwd']="'".md5($user_pwd)."'";
			$insert['user_type']=C('USER_MARK_SALESMAN');
			$insert['admin_id']=$admin_id;
			$insert['real_name_encrypt']="AES_ENCRYPT('".$real_name."','".AES_DECRYPT_KEY."')";
			$insert['idno_encrypt']="AES_ENCRYPT('".$idno."','".AES_DECRYPT_KEY."')";
			if($email){
				$insert['email_encrypt']="AES_ENCRYPT('".$email."','".AES_DECRYPT_KEY."')";
			}
			if($mobile){
				$insert['mobile_encrypt']="AES_ENCRYPT('".$mobile."','".AES_DECRYPT_KEY."')";
			}
			if($is_effect){
				$insert['is_effect']=1;
			}else{
				$insert['is_effect']=0;
			}
			$bStat=\Core::business('loan_stat');
			if($bStat->insertSchoolDistributor($insert)){
				$this -> log('新增行长(用户名：'.$user_name.')', 'add');
				\Core::message('增加行长成功', adminUrl('stat_distributor','schoolDistributor_add'), 'suc', 3, 'message');
			}else{
				\Core::message('增加行长失败', '', 'fail', 3, 'message');
			}
		}
		$bAgent=\Core::business('agent_agentEnum');
		$agents=$bAgent->enumAgent();
		\Core::view()->set('agents',$agents) -> load('stat_schoolDistributorAdd');
	}
	
	//编辑行长
	public function do_schoolDistributor_editInfo(){
		if (chksubmit()) {
			$id=\Core::post("id");
			$email=\Core::post('email');
			$mobile=\Core::post('mobile');
			$user_pwd=\Core::post('user_pwd');
			$is_effect=\Core::post('is_effect');
			$admin_id=\Core::post('admin_id');
			$real_name=\Core::post('real_name');
			$idno=\Core::post('idno');
			if(!$real_name){
				\Core::message('请填写真实姓名', '', 'fail', 3, 'message');
			}
			if(!$idno){
				\Core::message('请填写身份证号码', '', 'fail', 3, 'message');
			}
			if(!$admin_id){
				\Core::message('请选择归属', '', 'fail', 3, 'message');
			}
			if(!$id || !is_numeric($id)){
				\Core::message('错误的用户ID参数', '', 'fail', 3, 'message');
			}
			$update=array();
			
			$update['admin_id']=$admin_id;
			$update['real_name_encrypt']="AES_ENCRYPT('".$real_name."','".AES_DECRYPT_KEY."')";
			$update['idno_encrypt']="AES_ENCRYPT('".$idno."','".AES_DECRYPT_KEY."')";
			if($user_pwd){
				$update['user_pwd']="'".md5($user_pwd)."'";
			}
			if($email){
				$update['email_encrypt']="AES_ENCRYPT('".$email."','".AES_DECRYPT_KEY."')";
			}else{
				$update['email_encrypt']="''";
			}
			if($mobile){
				$update['mobile_encrypt']="AES_ENCRYPT('".$mobile."','".AES_DECRYPT_KEY."')";
			}else{
				$update['mobile_encrypt']="''";
			}
			if($is_effect){
				$update['is_effect']=1;
			}else{
				$update['is_effect']=0;
			}
			$bStat=\Core::business('loan_stat');
			if($bStat->editSchoolDistributor($id,$update)){
				$this -> log('编辑行长(用户ID：'.$id.')', 'edit');
				\Core::message('编辑行长成功', adminUrl('stat_distributor','schoolDistributor_editInfo',array('id'=>$id)), 'suc', 3, 'message');
			}else{
				\Core::message('编辑行长失败，可能数据没有任何更新或者其它原因', '', 'fail', 3, 'message');
			}
		}
		$id=\Core::getPost('id');
		if(!$id || !is_numeric($id)){
			\Core::message('请选择需要编辑的行长', '', 'fail', 0, 'message');
		}
		//获取行长信息
		$select="
		id,
		user_name,
		AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name,
		AES_DECRYPT(email_encrypt,'".AES_DECRYPT_KEY."') as email,
		AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."') as mobile,
		AES_DECRYPT(idno_encrypt,'".AES_DECRYPT_KEY."') as idno,
		admin_id,
		is_effect";
		$daoUser=\Core::dao('user_user');
		$user=$daoUser->getUser($id,$select);
		if(!$user){
			\Core::message('获取用户数据失败', '', 'fail', 0, 'message');
		}
		$bAgent=\Core::business('agent_agentEnum');
		$agents=$bAgent->enumAgent();
		\Core::view()->set('agents',$agents);
		\Core::view()->load('stat_schoolDistributorEditInfo',$user[$id]);
	}
	
	//批量修改行长归属
	public function do_schoolDistributor_edit(){
		if (chksubmit()) {
			$admin_id=\Core::post('admin_id');
			$ids=\Core::post('ids');
			if(!$admin_id || !is_numeric($admin_id)){
				\Core::message('请选择需要更改归属的行长', '', 'fail', 0, 'message');
			}
			if(!$ids){
				\Core::message('没有任何需要修改归属的行长信息', '', 'fail', 0, 'message',false);
			}
			$idsArr=explode(",", $ids);
			$daoUser=\Core::dao('user_user');
			$update=array('admin_id'=>$admin_id);
			$where=array('id'=>$idsArr);
			if($daoUser->update($update,$where)){
				$this -> log('批量编辑行长归属(用户ID：'.$ids.')', 'edit');
				\Core::message('批量编辑行长归属成功', '', 'suc', 3, 'message');
			}else{
				\Core::message('编辑行长归属失败，请重试', '', 'fail', 3, 'message');
			}
		}
		$id=\Core::getPost('id');
		if(!$id){
			\Core::message('请选择需要更改归属的行长', '', 'fail', 0, 'message',false);
		}
		$idArr=explode(",", $id);
		//查询用户的用户名
		$daoUser=\Core::dao('user_user');
		$users=$daoUser->getUserList("user_name,AES_DECRYPT(real_name_encrypt,'".AES_DECRYPT_KEY."') as real_name",array('id'=>$idArr));
		if(!$users){
			\Core::message('获取用户信息失败', '', 'fail', 0, 'message',false);
		}
		//获取业务员列表
		$bAgent=\Core::business('agent_agentEnum');
		$agents=$bAgent->enumAgent();
		//设置变量
		\Core::view()->set('ids',$id);
		\Core::view()->set('users',$users);
		\Core::view()->set('agents',$agents);
		\Core::view()->load('stat_schoolDistributorEdit');
	}
	
	//验证系统中是否存在相同的用户名
	public function do_schoolDistributor_userName_Verify(){
		$name = \Core::post('name');
		$param = \Core::post('param');
		if ($name && $param) {
			if (!\Core::dao('user_user') -> find(array($name=>$param))) {
				echo @json_encode(array('info' => \Core::L('verify_success'), 'status' => 'y'));
				exit ;
			}
		}
		echo @json_encode(array('info' => \Core::L('verify_fail'), 'status' => 'n'));
		exit ;
	}
	
	//验证系统中是否存在相同的手机号码
	public function do_schoolDistributor_mobile_Verify(){
		$name = \Core::post('name');
		$param = \Core::post('param');
		$user_id=\Core::getPost('user_id');
		if ($name && $param) {
			$where=array();
			$where["AES_DECRYPT(mobile_encrypt,'".AES_DECRYPT_KEY."')"]=$param;
			if($user_id){
				$where['id <>']=$user_id;
			}
			if (!\Core::dao('user_user') -> find($where)) {
				echo @json_encode(array('info' => \Core::L('verify_success'), 'status' => 'y'));
				exit ;
			}
		}
		echo @json_encode(array('info' => \Core::L('verify_fail'), 'status' => 'n'));
		exit ;
	}
	
	//验证系统中是否存在相同的身份证号码
	public function do_schoolDistributor_idno_Verify(){
		$name = \Core::post('name');
		$param = \Core::post('param');
		$user_id=\Core::getPost('user_id');
		if ($name && $param) {
			$where=array();
			$where["AES_DECRYPT(idno_encrypt,'".AES_DECRYPT_KEY."')"]=$param;
			if($user_id){
				$where['id <>']=$user_id;
			}
			if (!\Core::dao('user_user') -> find($where)) {
				echo @json_encode(array('info' => \Core::L('verify_success'), 'status' => 'y'));
				exit ;
			}
		}
		echo @json_encode(array('info' => \Core::L('verify_fail'), 'status' => 'n'));
		exit ;
	}
	
	//行长业绩
	public function do_schoolDistributorPerformance(){
		//获取业务员列表
		$bAgent=\Core::business('agent_agentEnum');
		$agents=$bAgent->enumAgent();
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view()->set('agents',$agents) -> load('stat_schoolDistributorPerformance');
	}
	
	//列表
	public function do_schoolDistributorPerformance_json(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		$user_name=\Core::getPost('user_name');
		$real_name=\Core::getPost('real_name');
		$mobile=\Core::getPost('mobile');
		$admin_id=\Core::getPost('admin_id');
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$orderName='id';
		$orderSort='desc';
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$orderName=\Core::postGet('sortname');
			$orderSort=\Core::postGet('sortorder');
		}
		$bStat=\Core::business('loan_stat');
		$bAgent=\Core::business('agent_agentEnum');
		$datas=$bStat->getStatSchoolDistributorPerformance($page,$pagesize,$user_name,$real_name,$mobile,$admin_id,$startStamp,$endStamp,$orderName,$orderSort);
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
			$opration.="<li><a href='javascript:list_day(".$v['id'].");'>日业绩</a></li>";
			$opration.="<li><a href='javascript:list_month(".$v['id'].");'>月业绩</a></li>";
			$opration.="<li><a href='javascript:list_sub(".$v['id'].");'>下级名单</a></li>";
			$opration.="</ul></span>";
			$row['cell'][] = $opration;
			$row['cell'][] = $v['id'];
			$row['cell'][] = $v['user_name'];
			$row['cell'][] = $v['real_name'];
			$row['cell'][] = $v['admin_id']?$bAgent->enumAgent($v['admin_id']):'';
			$row['cell'][] = $v['affiliates_count'];
			$row['cell'][] = $v['total_amount'];
			$row['cell'][] = $v['first_amount'];
			$row['cell'][] = $v['more_amount'];
			$row['cell'][] = $v['first_repay'];
			$row['cell'][] = $v['more_repay'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		//返回JSON
		echo @json_encode($json);
	}
	
	//导出
	public function do_schoolDistributorPerformance_export(){
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		$user_name=\Core::getPost('user_name');
		$real_name=\Core::getPost('real_name');
		$mobile=\Core::getPost('mobile');
		$admin_id=\Core::getPost('admin_id');
		
		$businessStat=\Core::business('loan_stat');
		$businessComm=\Core::business('common');
		$bAgent=\Core::business('agent_agentEnum');
		
		$curPage=\Core::getPost('curpage');
		
		$sql=$businessStat->getStatSchoolDistributorPerformanceSql($user_name,$real_name,$mobile,$admin_id,$startStamp,$endStamp);
		
		if (!is_numeric($curPage)){
			$count=$businessComm->getCount($sql);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_distributor', 'schoolDistributorPerformance'));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$datas=$businessStat->getStatSchoolDistributorPerformance($curPage,C('export_perpage'),$user_name,$real_name,$mobile,$admin_id,$startStamp,$endStamp);
        //Excel头部
		$header = array();
		$header['ID'] = 'integer';
		$header['用户名'] = 'string';
		$header['真实姓名'] = 'string';
		$header['归属'] = 'string';
		$header['总客户数量'] = 'integer';
		$header['总放款金额'] = 'price';
		$header['首借放款额'] = 'price';
		$header['续借放款额'] = 'price';
		$header['首借还款额'] = 'price';
		$header['续借还款额'] = 'price';
		
		$export=array();
		foreach($datas['rows'] as $v){
			$row=array();
			$row[] = $v['id'];
			$row[] = $v['user_name'];
			$row[] = $v['real_name'];
			$row[] = $v['admin_id']?$bAgent->enumAgent($v['admin_id']):'';
			$row[] = $v['affiliates_count'];
			$row[] = $v['total_amount'];
			$row[] = $v['first_amount'];
			$row[] = $v['more_amount'];
			$row[] = $v['first_repay'];
			$row[] = $v['more_repay'];
			$export[]=$row;
		}
		//导出
		$this -> log('导出行长业绩汇总(第'.$curPage.'页)', 'export');
		exportExcel('行长业绩汇总(第'.$curPage.'页)', $header, $export);
	}
	
	//日业绩
	public function do_schoolDistributorPerformance_day(){
		$id=\Core::getPost("id");
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		//查询是否存在该用户
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		$daoUser=\Core::dao('user_user');
		$userName=$daoUser->getUserNameById($id);
		if(!$userName){
			\Core::message('系统不存在id为'.$id.'的用户信息', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> set("id",$id);
		\Core::view() -> set("userName",$userName);
		\Core::view() -> load('stat_schoolDistributorPerformanceDay');
	}
	
	//日业绩
	public function do_schoolDistributorPerformance_day_json(){
		$id=\Core::getPost("id");
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$order=array('id'=>'desc');
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$order=array(\Core::postGet('sortname')=>\Core::postGet('sortorder'));
		}
		
		$fields="*";
		$where=array('user_id'=>$id,'UNIX_TIMESTAMP(sta_date) >='=>$startStamp,'UNIX_TIMESTAMP(sta_date) <='=>$endStamp);
		
		$daoStat=\Core::dao('loan_bankerStatistics');
		$datas=$daoStat->getFlexPage($page,$pagesize,$fields,$where,$order);
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = $v['sta_date'];
			$row['cell'][] = $v['user_new'];
			$row['cell'][] = $v['user_borrow'];
			$row['cell'][] = priceFormat($v['borrow_amount']);
			$row['cell'][] = priceFormat($v['repay_amount']);
			$row['cell'][] = priceFormat($v['repay_fisrt']);
			$row['cell'][] = priceFormat($v['repay_more']);
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		echo @json_encode($json);
	}

	public function do_schoolDistributorPerformance_day_export(){
		$id=\Core::getPost("id");
		$userName=\Core::getPost("user_name",'');
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', '', 'fail', 3, 'message');
		}
		
		$fields="sta_date,user_new,user_borrow,borrow_amount,repay_amount,repay_fisrt,repay_more";
		$where=array('user_id'=>$id,'UNIX_TIMESTAMP(sta_date) >='=>$startStamp,'UNIX_TIMESTAMP(sta_date) <='=>$endStamp);
		
		$daoStat=\Core::dao('loan_bankerStatistics');
		$curPage=\Core::getPost('curpage');
		
		if (!is_numeric($curPage)){
			$count=$daoStat->getCount($where);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_distributor', 'schoolDistributorPerformance_day',array('id'=>$id)));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$datas=$daoStat->getFlexPage($curPage,C('export_perpage'),$fields,$where);
        //Excel头部
		$header = array();
		$header['日期'] = 'date';
		$header['新增客户'] = 'integer';
		$header['借款客户'] = 'integer';
		$header['借款总额'] = 'price';
		$header['还款总额'] = 'price';
		$header['首借还款'] = 'price';
		$header['续借还款'] = 'price';

		//导出
		$this -> log('导出行长'.$userName.'日业绩汇总'.$datestart.'-'.$dateend.'(第'.$curPage.'页)', 'export');
		exportExcel('行长'.$userName.'日业绩汇总'.$datestart.'-'.$dateend.'(第'.$curPage.'页)', $header, $datas['rows']);
	}
	
	//月业绩
	public function do_schoolDistributorPerformance_month(){
		$id=\Core::getPost("id");
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		if (!$datestart || !$dateend) {
			$datestart = 0;
			$dateend = 0;
		}
		//查询是否存在该用户
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		$daoUser=\Core::dao('user_user');
		$userName=$daoUser->getUserNameById($id);
		if(!$userName){
			\Core::message('系统不存在id为'.$id.'的用户信息', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		\Core::view() -> set('datestart', $datestart);
		\Core::view() -> set('dateend', $dateend);
		\Core::view() -> set("id",$id);
		\Core::view() -> set("userName",$userName);
		\Core::view() -> load('stat_schoolDistributorPerformanceMonth');
	}
	
	public function do_schoolDistributorPerformance_month_json(){
		$id=\Core::getPost("id");
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$order=array('id'=>'desc');
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$order=array(\Core::postGet('sortname')=>\Core::postGet('sortorder'));
		}
		
		$fields="
		DATE_FORMAT(sta_date,'%Y-%m') as sta_month,
		sum(user_new) as sum_user_new,
		sum(user_borrow) as sum_user_borrow,
		sum(borrow_amount) as sum_borrow_amount,
		sum(repay_amount) as sum_repay_amount,
		sum(repay_fisrt) as sum_repay_fisrt,
		sum(repay_more) as sum_repay_more
		";
		$where=array('user_id'=>$id,'UNIX_TIMESTAMP(sta_date) >='=>$startStamp,'UNIX_TIMESTAMP(sta_date) <='=>$endStamp);
		
		$daoStat=\Core::dao('loan_bankerStatistics');
		$datas=$daoStat->getFlexPage($page,$pagesize,$fields,$where,$order,"DATE_FORMAT(sta_date,'%Y-%m')");
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $k=>$v) {
			$row = array();
			$row['id'] = $k;
			$row['cell'][] = $v['sta_month'];
			$row['cell'][] = $v['sum_user_new'];
			$row['cell'][] = $v['sum_user_borrow'];
			$row['cell'][] = priceFormat($v['sum_borrow_amount']);
			$row['cell'][] = priceFormat($v['sum_repay_amount']);
			$row['cell'][] = priceFormat($v['sum_repay_fisrt']);
			$row['cell'][] = priceFormat($v['sum_repay_more']);
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		echo @json_encode($json);
	}
	
	public function do_schoolDistributorPerformance_month_export(){
		$id=\Core::getPost("id");
		$userName=\Core::getPost("user_name",'');
		$datestart = \Core::postGet('datestart');
		$dateend = \Core::postGet('dateend');
		$startStamp=strtotime($datestart);
		$endStamp=strtotime($dateend);
		
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', '', 'fail', 3, 'message');
		}
		
		$fields="
		DATE_FORMAT(sta_date,'%Y-%m') as sta_month,
		sum(user_new) as sum_user_new,
		sum(user_borrow) as sum_user_borrow,
		sum(borrow_amount) as sum_borrow_amount,
		sum(repay_amount) as sum_repay_amount,
		sum(repay_fisrt) as sum_repay_fisrt,
		sum(repay_more) as sum_repay_more
		";
		$where=array('user_id'=>$id,'UNIX_TIMESTAMP(sta_date) >='=>$startStamp,'UNIX_TIMESTAMP(sta_date) <='=>$endStamp);
		
		$daoStat=\Core::dao('loan_bankerStatistics');
		$curPage=\Core::getPost('curpage');
		
		if (!is_numeric($curPage)){
			$count=$daoStat->getCount($where,null,"DATE_FORMAT(sta_date,'%Y-%m')");
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_distributor', 'schoolDistributorPerformance_month',array('id'=>$id)));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$datas=$daoStat->getFlexPage($curPage,C('export_perpage'),$fields,$where,array(),"DATE_FORMAT(sta_date,'%Y-%m')");
        //Excel头部
		$header = array();
		$header['月份'] = 'string';
		$header['新增客户'] = 'integer';
		$header['借款客户'] = 'integer';
		$header['借款总额'] = 'price';
		$header['还款总额'] = 'price';
		$header['首借还款'] = 'price';
		$header['续借还款'] = 'price';

		//导出
		$this -> log('导出行长'.$userName.'月业绩汇总'.$datestart.'-'.$dateend.'(第'.$curPage.'页)', 'export');
		exportExcel('行长'.$userName.'月业绩汇总'.$datestart.'-'.$dateend.'(第'.$curPage.'页)', $header, $datas['rows']);
	}
	
	//下级用户
	public function do_schoolDistributorPerformance_subordinate(){
		$id=\Core::getPost("id");
		//查询是否存在该用户
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		$daoUser=\Core::dao('user_user');
		$userName=$daoUser->getUserNameById($id);
		if(!$userName){
			\Core::message('系统不存在id为'.$id.'的用户信息', adminUrl('stat_distributor','schoolDistributorPerformance'), 'fail', 3, 'message');
		}
		\Core::view() -> set("id",$id);
		\Core::view() -> set("userName",$userName);
		\Core::view()->load('stat_schoolDistributorPerformanceSubordinate');
	}
	
	public function do_schoolDistributorPerformance_subordinate_json(){
		$id=\Core::getPost("id");
		
		$pagesize = \Core::postGet('rp');
		$page = \Core::postGet('curpage');
		if (!$page || !is_numeric($page))
			$page = 1;
		if (!$pagesize || !is_numeric($pagesize))
			$pagesize = 15;
		$order=array('id'=>'desc');
		if (\Core::postGet('sortname') && \Core::postGet('sortorder')) {
			$order=array(\Core::postGet('sortname')=>\Core::postGet('sortorder'));
		}
		
		$fields="
		id,
		user_name,
		AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') as real_name
		";
		$where=array('pid'=>$id);
		
		$daoUser=\Core::dao('user_user');
		$datas=$daoUser->getFlexPage($page,$pagesize,$fields,$where,$order);
		$json = array();
		$json['page'] = $page;
		$json['total'] = $datas['total'];
		foreach ($datas['rows'] as $k=>$v) {
			$row = array();
			$row['id'] = $v['id'];
			$row['cell'][] = $v['id'];
			$row['cell'][] = $v['user_name'];
			$row['cell'][] = $v['real_name'];
			$row['cell'][] = '';
			$json['rows'][] = $row;
		}
		echo @json_encode($json);
	}
	
	public function do_schoolDistributorPerformance_subordinate_export(){
		$id=\Core::getPost("id");
		$userName=\Core::getPost("user_name",'');
		
		if(!$id || !is_numeric($id)){
			\Core::message('参数错误', '', 'fail', 3, 'message');
		}
		
		$fields="
		id,
		user_name,
		AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') as real_name
		";
		$where=array('pid'=>$id);
		
		$daoUser=\Core::dao('user_user');
		$curPage=\Core::getPost('curpage');
		
		if (!is_numeric($curPage)){
			$count=$daoUser->getCount($where);
			//超过最大数据，需要分页，跳转到分页页面
			if($count>C('export_perpage')){
				$page = ceil($count/C('export_perpage'));
                for ($i=1;$i<=$page;$i++){
                    $limit1 = ($i-1)*C('export_perpage') + 1;
                    $limit2 = $i*C('export_perpage') > $count ? $count : $i*C('export_perpage');
                    $array[$i] = $limit1.' ~ '.$limit2 ;
                }
                Core::view()->set('list',$array);
                Core::view()->set('murl',adminUrl('stat_distributor', 'schoolDistributorPerformance_subordinate',array('id'=>$id)));
                Core::view()->load('export.excel');
				exit;
			}
		}
		$curPage=$curPage?$curPage:1;
		$datas=$daoUser->getFlexPage($curPage,C('export_perpage'),$fields,$where);
        //Excel头部
		$header = array();
		$header['id'] = 'integer';
		$header['用户名称'] = 'string';
		$header['真实姓名'] = 'string';

		//导出
		$this -> log('导出行长'.$userName.'下级列表(第'.$curPage.'页)', 'export');
		exportExcel('行长'.$userName.'下级列表(第'.$curPage.'页)', $header, $datas['rows']);
	}
}