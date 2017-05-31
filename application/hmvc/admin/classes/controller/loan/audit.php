<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class  controller_loan_audit extends controller_sysBase {

    public function before($method, $args) {
        \Language::read('loan');
    }

    public function do_index() {
        $this -> do_first_publish();
    }

    //首单待审核列表
    public function do_first_publish(){
        $loanBusiness=\Core::business('loan_loanenum');
        //贷款类型数据
        \Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
            ->set('loantype',$loanBusiness->enumLoanType())
            ->set('dealcate',$loanBusiness->enumDealCate())
            ->set('dealusetype',$loanBusiness->enumDealUseType())
            ->set('sorcode',$loanBusiness->enumSorCode())
            ->set('dealstatus',$loanBusiness->enumDealStatus())
            ->set('action','first_publish_json')
            ->set('way',1)
            ->set('title',\Core::L('first_publish'));
        \Core::view() -> load('loan_publish');
    }


    //获取首单待审核分页JSON数据

    public function do_first_publish_json(){
        //每页显示行数
        $pagesize = \Core::postGet('rp');
        //当前页
        $page = \Core::postGet('curpage');
        //需要获取的字段
        $fields = 'id,name,user_id,borrow_amount,rate,repay_time,use_type,loantype,update_time,sor_code,first_audit_admin_id,repay_time_type,FROM_UNIXTIME(create_time+8*3600) AS create_time';

        //排序
        $orderby = array();
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        //固定查询条件
        $where['is_delete'] = 0;
        $where['publish_wait'] = array(1,3);
        $where['b_status'] = 0;

        //简易查询条件
        if (\Core::get('query')) {
            $where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
        }
        //高级查询条件
        if(\Core::postGet('id')!=null && is_numeric(\Core::get('id'))){
            $where['id like']="%".\Core::get('id')."%";
        }
        if(\Core::postGet('name')!=null){
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
        if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
            $where['deal_status']=\Core::get('deal_status');
        }
        if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
            $where['is_has_received']=\Core::get('is_has_received');
        }
        if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
            $where['user_id like']="%".\Core::get('user_id')."%";
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
        foreach ($data['rows'] as $v) {
            $userIds[]=$v['user_id'];
            $adminFirstIds[]=$v['first_audit_admin_id'];
        }
        $userDao=\Core::dao('user_user');
        $adminDao=\Core::dao('sys_admin_admin');

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');
        $firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:loan_edit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $v['rate']."%";
            $row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
            $row['cell'][] = $loanBusiness->enumDealUseType($v['use_type']);
            $row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
            $row['cell'][] = $v['create_time'];
            $row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);
            $row['cell'][] = $v['first_audit_admin_id'] == 0 ? '待认领' : '初审中';
            $row['cell'][] = \Core::arrayKeyExists($v['first_audit_admin_id'], $firstAdminNames)?\Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name'):'<a href="javascript:;" onclick="get_owners('.$v['id'].');">认领</a>';
            $row['cell'][] = '';
            $json['rows'][] = $row;
        }
        //返回JSON
        return @json_encode($json);
    }

    //我的待审核列表
    public function do_my_publish()
    {
        $loanBusiness=\Core::business('loan_loanenum');
        //贷款类型数据
        \Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
            ->set('loantype',$loanBusiness->enumLoanType())
            ->set('dealcate',$loanBusiness->enumDealCate())
            ->set('dealusetype',$loanBusiness->enumDealUseType())
            ->set('sorcode',$loanBusiness->enumSorCode())
            ->set('dealstatus',$loanBusiness->enumDealStatus())
            ->set('title',\Core::L('my_publish'))
            ->set('way',2)
            ->set('action','my_publish_json');
        \Core::view() -> load('loan_mypublish');
    }
    //我的待审核列表分页的JSON数据
    public function do_my_publish_json()
    {
        //每页显示行数
        $pagesize = \Core::postGet('rp');
        //当前页
        $page = \Core::postGet('curpage');
        //需要获取的字段
        $fields = 'id,name,user_id,borrow_amount,rate,repay_time,use_type,loantype,FROM_UNIXTIME(create_time+8*3600) AS create_time,sor_code,first_audit_admin_id,repay_time_type';

        //排序
        $orderby = array();
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        //固定查询条件
        $where['first_audit_admin_id'] = $this->admininfo['id'];
        $where['is_delete'] = 0;
        $where['publish_wait'] = 1;

        //简易查询条件
        if (\Core::get('query')) {
            $where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
        }
        //高级查询条件
        if(\Core::postGet('id')!=null && is_numeric(\Core::get('id'))){
            $where['id like']="%".\Core::get('id')."%";
        }
        if(\Core::postGet('name')!=null){
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
        if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
            $where['deal_status']=\Core::get('deal_status');
        }
        if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
            $where['is_has_received']=\Core::get('is_has_received');
        }
        if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
            $where['user_id like']="%".\Core::get('user_id')."%";
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
        foreach ($data['rows'] as $v) {
            $userIds[]=$v['user_id'];
            $adminFirstIds[]=$v['first_audit_admin_id'];
        }
        $userDao=\Core::dao('user_user');

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:loan_edit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $v['rate']."%";
            $row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
            $row['cell'][] = $loanBusiness->enumDealUseType($v['use_type']);
            $row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
            $row['cell'][] = $v['create_time'];
            $row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);
            $row['cell'][] = $v['first_audit_admin_id'] == 0 ? '待认领' : '初审中';
            $row['cell'][] = '<a href="javascript:;" onclick="get_owners('.$v['id'].');">取消认领</a>';
            $row['cell'][] = '';
            $json['rows'][] = $row;
        }
        //返回JSON
        return @json_encode($json);
    }

    //续借待审核列表
    public function do_publish()
    {
        $loanBusiness=\Core::business('loan_loanenum');
        //贷款类型数据
        \Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
            ->set('loantype',$loanBusiness->enumLoanType())
            ->set('dealcate',$loanBusiness->enumDealCate())
            ->set('dealusetype',$loanBusiness->enumDealUseType())
            ->set('sorcode',$loanBusiness->enumSorCode())
            ->set('dealstatus',$loanBusiness->enumDealStatus())
            ->set('title',\Core::L('publish'))
            ->set('way',1)
            ->set('action','publish_json');
        \Core::view() -> load('loan_publish');
    }

    //获取续借待审核的JSON数据
    public function do_publish_json()
    {
        //每页显示行数
        $pagesize = \Core::postGet('rp');
        //当前页
        $page = \Core::postGet('curpage');
        //需要获取的字段
        $fields = 'id,name,user_id,borrow_amount,rate,repay_time,use_type,loantype,FROM_UNIXTIME(create_time+8*3600) as create_time,sor_code,first_audit_admin_id,repay_time_type';

        //排序
        $orderby = array();
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        //固定查询条件
        $where['is_delete'] = 0;
        $where['publish_wait'] = array(1,3);
        $where['b_status'] = 1;

        //简易查询条件
        if (\Core::get('query')) {
            $where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
        }
        //高级查询条件
        if(\Core::postGet('id')!=null && is_numeric(\Core::get('id'))){
            $where['id like']="%".\Core::get('id')."%";
        }
        if(\Core::postGet('name')!=null){
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
        if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
            $where['deal_status']=\Core::get('deal_status');
        }
        if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
            $where['is_has_received']=\Core::get('is_has_received');
        }
        if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
            $where['user_id like']="%".\Core::get('user_id')."%";
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
        foreach ($data['rows'] as $v) {
            $userIds[]=$v['user_id'];
            $adminFirstIds[]=$v['first_audit_admin_id'];
        }
        $userDao=\Core::dao('user_user');
        $adminDao=\Core::dao('sys_admin_admin');

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');
        $firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:publish_edit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a></li>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $v['rate']."%";
            $row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
            $row['cell'][] = $loanBusiness->enumDealUseType($v['use_type']);
            $row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
            $row['cell'][] = $v['create_time'];
            $row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);
            $row['cell'][] = $v['first_audit_admin_id'] == 0 ? '待认领' : '初审中';
            $row['cell'][] = \Core::arrayKeyExists($v['first_audit_admin_id'], $firstAdminNames)?\Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name'):'<a href="javascript:;" onclick="get_owners('.$v['id'].');">认领</a>';
            $row['cell'][] = '';
            $json['rows'][] = $row;
        }
        //返回JSON
        return @json_encode($json);
    }

    //复审核列表
    public function do_true_publish()
    {
        $loanBusiness=\Core::business('loan_loanenum');
        //贷款类型数据
        \Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
            ->set('loantype',$loanBusiness->enumLoanType())
            ->set('dealcate',$loanBusiness->enumDealCate())
            ->set('dealusetype',$loanBusiness->enumDealUseType())
            ->set('sorcode',$loanBusiness->enumSorCode())
            ->set('dealstatus',$loanBusiness->enumDealStatus())
            ->set('action','true_publish_json');
        \Core::view() -> load('loan_truepublish');
    }

    public function do_true_publish_json()
    {
        //每页显示行数
        $pagesize = \Core::postGet('rp');
        //当前页
        $page = \Core::postGet('curpage');
        //需要获取的字段
        $fields = 'id,name,user_id,borrow_amount,rate,repay_time,use_type,loantype,update_time,sor_code,first_audit_admin_id,repay_time_type';

        //排序
        $orderby = array();
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        //固定查询条件
        $where['is_delete'] = 0;
        $where['publish_wait'] = 2;
        $where['b_status'] = 1;

        //简易查询条件
        if (\Core::get('query')) {
            $where[\Core::postGet('qtype') . " like"] = "%" . \Core::postGet('query') . "%";
        }
        //高级查询条件
        if(\Core::postGet('id')!=null && is_numeric(\Core::get('id'))){
            $where['id like']="%".\Core::get('id')."%";
        }
        if(\Core::postGet('name')!=null){
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
        if(\Core::get('deal_status')!=null && \Core::get('deal_status')!='-1'){
            $where['deal_status']=\Core::get('deal_status');
        }
        if(\Core::get('is_has_received')!=null && \Core::get('is_has_received')!='-1'){
            $where['is_has_received']=\Core::get('is_has_received');
        }
        if(\Core::get('user_id')!=null && is_numeric(\Core::get('user_id'))){
            $where['user_id like']="%".\Core::get('user_id')."%";
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
        foreach ($data['rows'] as $v) {
            $userIds[]=$v['user_id'];
            $adminFirstIds[]=$v['first_audit_admin_id'];
        }
        $userDao=\Core::dao('user_user');
        $adminDao=\Core::dao('sys_admin_admin');
        $dealRepayDao=\Core::dao('loan_dealrepay');

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');
        $firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:loan_preview(".$v['id'].")'>预览</a></li>";
            $opration.="<li><a href='javascript:loan_edit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = $v['name'];
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $dealRepayDao->getOverdueTimes($v['user_id']);
            $row['cell'][] = $v['rate']."%";
            $row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
            $row['cell'][] = $loanBusiness->enumDealUseType($v['use_type']);
            $row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
            $row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);
            $row['cell'][] = $v['first_audit_admin_id'] == 0 ? '待认领' : '初审中';
            $row['cell'][] = \Core::arrayKeyExists($v['first_audit_admin_id'], $firstAdminNames)?\Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name'):'<a href="javascript:;" onclick="get_owners('.$v['id'].');">认领</a>';
            $row['cell'][] = '';
            $json['rows'][] = $row;
        }
        //返回JSON
        return @json_encode($json);
    }

    public function do_fail_publish()
    {
        $loanBusiness=\Core::business('loan_loanenum');
        //贷款类型数据
        \Core::view()->set('repaytimetype',$loanBusiness->enumRepayTimeType())
            ->set('loantype',$loanBusiness->enumLoanType())
            ->set('dealcate',$loanBusiness->enumDealCate())
            ->set('dealusetype',$loanBusiness->enumDealUseType())
            ->set('sorcode',$loanBusiness->enumSorCode())
            ->set('dealstatus',$loanBusiness->enumDealStatus())
            ->set('action','my_publish_json');
        \Core::view() -> load('loan_mypublish');
    }

    public function do_fail_publish_json()
    {

    }
    //认领操作
    public function do_publish_audit_owner()
    {

        $root['msg'] = '操作失败';
        $root['response_code'] = 0;
        $data = array();

        $id = intval(Core::post('id'));
        $ids = array();
        if ($id > 0) {
            $ids[] = $id;
        } else {
            $ids = trim(Core::post('ids'),',');
            $ids = explode($ids,',');
        }
        if ($ids == '' || !is_array($ids) || count($ids) == 0) {
            echo @json_encode($root);
            exit;
        }

        $deal_op_log = Core::dao('loan_loanoplog');
        $loanbase = Core::dao('loan_loanbase');
        $admin_id = $this->admininfo['id'];

        $where = array();
        $way = intval($_REQUEST['way']);
        if ($way == 1) { //认领
            $data['first_audit_admin_id'] = $admin_id;
            $data['claim_time'] = time(); //认领时间
            $where['first_audit_admin_id'] = 0;
        } elseif ($way == 2) {//取消认领
            $data['first_audit_admin_id'] = 0;
            $data['claim_time'] = 0;  //认领时间
            $where['first_audit_admin_id'] = $admin_id;
        } else {
            echo @json_encode($root);
            exit;
        }

        $where['id']=$ids;
        $update_result = $loanbase->update($data,$where);
        if($update_result) {
            $root['response_code'] = 1;
            if($way == 1) {
                $root['msg'] = '认领操作成功';
                $log_condition['id'] = $ids;
                $log_condition['first_audit_admin_id'] = $admin_id;
                $log_condition['claim_time'] = $data['claim_time'];
                $ids = $loanbase->findCol('id',$log_condition,true);
                foreach ($ids as $k => $id) {
                    $log_data[$k]['deal_id'] = $id;
                    $log_data[$k]['user_id'] = $loanbase->findCol('user_id',$id);
                    $log_data[$k]['op_id'] = 2;
                    $log_data[$k]['op_name'] = '认领操作';
                    $log_data[$k]['op_result'] = '成功';
                    $log_data[$k]['log'] = '认领申请成功，初审中';
                    $log_data[$k]['admin_id'] = $admin_id;
                    $log_data[$k]['create_time'] = time();
                    $log_data[$k]['ip'] = Core::clientIp();
                }
                $deal_op_log->insertBatch($log_data);
            } else {
                $root['msg'] = '取消认领操作成功';
                foreach ($ids as $k => $id) {
                    $log_data[$k]['deal_id'] = $id;
                    $log_data[$k]['user_id'] = $loanbase->findCol('user_id',$id);
                    $log_data[$k]['op_id'] = 3;
                    $log_data[$k]['op_name'] = '取消认领操作';
                    $log_data[$k]['op_result'] = '成功';
                    $log_data[$k]['log'] = '取消认领';
                    $log_data[$k]['admin_id'] = $admin_id;
                    $log_data[$k]['create_time'] = time();
                    $log_data[$k]['ip'] = Core::clientIp();
                }
                $deal_op_log->insertBatch($log_data);
            }

        }
        echo @json_encode($root);
    }

    //预览页面
    public function do_publish_preview()
    {
        $this->publish_edit();
        \Core::view()->load('loan_preview');
    }

    //初审阶段贷款类型修改
    public function do_edit_loan_type()
    {
        $this->publish_edit();
        \Core::view() -> load('loan_publish');
    }


    //初审操作页面
    public function do_first_publish_edit()
    {
        $this->publish_edit();
    }

    //初审操作
    public function do_first_publish_update()
    {
    }

    //复审操作页面
    public function do_true_publish_edit()
    {
        $this->publish_edit();
    }

    //复审操作
    public function do_true_publish_update()
    {

    }


    public function publish_edit()
    {
        if(chksubmit()) {
            \Core::dump('test');die();
            //提交保存

        }else {
            $loan_id = \Core::get('loan_id',0);
            $loanBusiness=\Core::business('loan_loanenum');
            //根据借款id，获取贷款基本信息
            $basefields = 'id,deal_sn,name,user_id,type_id,loantype,borrow_amount,repay_time,rate,is_referral_award,use_type,repay_time_type,use_type';
            $loanbase = \Core::dao('loan_loanbase')->getloanbase($loan_id,$basefields);
            //获取会员名称
            $user_id = $loanbase['user_id'];
            $user = \Core::dao('user_user')->getUser($user_id,'id,user_name,real_name,pid');
            $username = \Core::arrayKeyExists($user_id, $user)?\Core::arrayGet(\Core::arrayGet($user, $user_id),'user_name').'('.\Core::arrayGet(\Core::arrayGet($user, $user_id),'real_name').')':'';
            if($username != '') {
                $username = '<a href="#&user_id='.$user_id.'">'.$username.'</a>';
            }
            //获取借款拓展字段
            $loanextDao =  \Core::dao('loan_loanext');
            $contractid = $loanextDao->getContract($loan_id);
            $amtConfig = $loanextDao->getAmtconfig($loan_id);
            $l_guarantees_amt = \Core::arrayKeyExists('l_guarantees_amt',$amtConfig)?\Core::arrayGet(\Core::arrayGet($amtConfig, 'l_guarantees_amt')):'';
            $guarantees_amt = \Core::arrayKeyExists('guarantees_amt',$amtConfig)?\Core::arrayGet(\Core::arrayGet($amtConfig, 'guarantees_amt')):0;
            if(!$l_guarantees_amt) {
                $l_guarantees_amt = number_format($loanbase['borrow_amount'] * $guarantees_amt / 100,2);
            }
            //commconfig
            $commonConfig = $loanextDao->getCommonconfig($loan_id);
            //获取合同范本
            $contract =  \Core::dao('loan_contract')->getContractList('id,title');
            //根据借款id，获取标基本信息
            $bidfields = 'loan_id,min_loan_money,max_loan_money,deal_status,start_time,end_time,uloadtype,portion,max_portion,use_ecv,risk_rank,risk_security';
            $loanbid = \Core::dao('loan_loanbid')->getOneLoanById($loan_id,$bidfields);
            \Core::view()->set('loantype',$loanBusiness->enumLoanType())
                ->set('dealcate',$loanBusiness->enumDealCate())
                ->set('dealusetype',$loanBusiness->enumDealUseType())
                ->set('dealloantype',$loanBusiness->enumDealLoanType())
                ->set('plathtml',$loanBusiness->userPlatRegVerified($loan_id,$user_id))
                ->set('loanbase',$loanbase)
                ->set('loanbid',$loanbid)
                ->set('username',$username)
                ->set('contract',$contract)
                ->set('contractid',$contractid)
                ->set('l_guarantees_amt',$l_guarantees_amt)
                ->set('commonConfig',$commonConfig)
                ->set('user_detail',$loanBusiness->userDetail($user_id))
                ->set('sorcode',$loanBusiness->enumSorCode());
        }
    }
}