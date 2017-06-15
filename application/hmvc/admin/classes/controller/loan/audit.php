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
            ->set('dealstatus',$loanBusiness->enumDealStatus());
        \Core::view() -> load('loan_firstpublish');
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
        //推荐人
        if(\Core::get('p_user_name')!=null) {
            $where['user_id '] = $userDao->getUsersIdsByPuser(\Core::get('p_user_name'));
        }

        //简易排序条件
        if (\Core::postGet('sortorder')) {
            $orderby[\Core::postGet('sortname')] = \Core::postGet('sortorder');
        }

        //未认领
        if (intval(\Core::get('unclaimed')) == 1) {
            $where['first_audit_admin_id <'] = 1;
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
        $adminDao=\Core::dao('sys_admin_admin');

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');
        $firstAdminNames=$adminDao->getAdmin($adminFirstIds,'admin_id,admin_name,admin_real_name,admin_mobile');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:loan_audit(".$v['id'].")'>审核操作</a></li>";
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
            ->set('dealstatus',$loanBusiness->enumDealStatus());
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
        //推荐人
        if(\Core::get('p_user_name')!=null) {
            $where['user_id '] = $userDao->getUsersIdsByPuser(\Core::get('p_user_name'));
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

        $userNames=$userDao->getUser($userIds,'id,user_name,real_name');

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $opration="<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration.="<li><a href='javascript:loan_audit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $loanBusiness->enumDealTimes($v['user_id']);
            $row['cell'][] = $loanBusiness->enumOverRepayTimes($v['user_id']);
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
            ->set('dealstatus',$loanBusiness->enumDealStatus());
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
        //推荐人
        if(\Core::get('p_user_name')!=null) {
            $where['user_id '] = $userDao->getUsersIdsByPuser(\Core::get('p_user_name'));
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
            $opration.="<li><a href='javascript:loan_audit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a></li>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $loanBusiness->enumDealTimes($v['user_id']);
            $row['cell'][] = $loanBusiness->enumOverRepayTimes($v['user_id']);
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
            ->set('dealstatus',$loanBusiness->enumDealStatus());
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
        //推荐人
        if(\Core::get('p_user_name')!=null) {
            $where['user_id '] = $userDao->getUsersIdsByPuser(\Core::get('p_user_name'));
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
            $opration.="<li><a href='javascript:loan_audit(".$v['id'].")'>审核操作</a></li>";
            $opration.="<li><a href='javascript:loan_audit_log(".$v['id'].")'>审核日志</a></li>";
            $opration.="</ul></span>";
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = "<a href='javascript:loan_preview(".$v['id'].")'>".$v['name']."</a></li>";
            $row['cell'][] = \Core::arrayKeyExists($v['user_id'], $userNames)?\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'user_name').'('.\Core::arrayGet(\Core::arrayGet($userNames, $v['user_id']),'real_name').')':'';
            $row['cell'][] = "￥".$v['borrow_amount'];
            $row['cell'][] = $loanBusiness->enumDealTimes($v['user_id']);
            $row['cell'][] = $loanBusiness->enumOverRepayTimes($v['user_id']);
            $row['cell'][] = $v['rate']."%";
            $row['cell'][] = $v['repay_time'].$loanBusiness->enumRepayTimeType($v['repay_time_type']);
            $row['cell'][] = $loanBusiness->enumDealUseType($v['use_type']);
            $row['cell'][] = $loanBusiness->enumLoanType($v['loantype']);
            $row['cell'][] = $loanBusiness->enumSorCode($v['sor_code']);
            $row['cell'][] = $v['first_audit_admin_id'] == 0 ? '待认领' : '初审中';
            $row['cell'][] = \Core::arrayGet(\Core::arrayGet($firstAdminNames, $v['first_audit_admin_id'],''),'admin_real_name');
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
            ->set('dealstatus',$loanBusiness->enumDealStatus());
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
    public function do_preview()
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
        \Core::view() -> load('loan_firstPublishEdit');
    }

    //初审操作
    public function do_first_publish_update()
    {
        $loanbaseDao = \Core::dao('loan_loanbase');
        $loanextDao = \Core::dao('loan_loanext');
        $dealsatuslogBusiness = \Core::business('loan_dealstatuslog');

        $loan_id = intval(\Core::post('loan_id'));
        $user_id = intval(\Core::post('user_id'));
        $first_yn = intval(\Core::get('first_yn'));
        
        $loanbase['borrow_amount'] = trim(\Core::post('borrow_amount'));
        $loanbase['loantype'] = intval(\Core::post('loantype'));
        $loanbase['description'] = trim(\Core::post('description'));
        $loanbase['use_type'] = intval(\Core::post('use_type'));
        $loanbase['is_delete'] = intval(\Core::post('is_delete'));
        $loanbase['risk_rank'] = intval(\Core::post('risk_rank'));
        $loanbase['risk_security'] = trim(\Core::post('risk_security'));
        $loanbase['publish_wait'] = intval(\Core::post('publish_wait'));
        $loanbase['first_audit_admin_id'] = $this->admininfo['id'];
        $loanbase['name'] = trim(\Core::post('name'));
        $loanbase['sub_name'] = trim(\Core::post('sub_name'));
        $loanbase['is_referral_award'] = intval(\Core::post('is_referral_award'));
        $loanbase['repay_time'] = intval(\Core::post('repay_time'));
        $loanbase['repay_time_type'] = intval(\Core::post('epay_time_type'));
        $loanbase['rate'] = trim(\Core::post('rate'));
        $loanbase['delete_msg'] = trim(\Core::post('delete_msg'));
        $loanbase['delete_real_msg'] = trim(\Core::post('delete_real_msg'));
        //$loanbase['sort'] = trim(\Core::post('sort'));
        $loanbase['type_id'] = intval(\Core::post('type_id'));

        $loanext['contract_id'] = intval(\Core::post('contract_id'));
        $loanext['scontract_id'] = intval(\Core::post('scontract_id'));
        $loanext['tcontract_id'] = intval(\Core::post('tcontract_id'));

        $update_time = intval(\Core::post('update_time'));

        $url = adminUrl('loan_audit','first_publish_edit',array('loan_id'=>$loan_id));

        $loan = $loanbaseDao->getloanbase($loan_id,'update_time,type_id,publish_wait,name'); //获取更新前的数据

        //todo 数据验证
        if ($update_time != $loan['update_time']) {
            \Core::message('当前借款资料在提交的时候发现已经被其他同事变更,请重新点击操作!',$url,'fail',3,'message');
        }
        $loanbase['update_time'] = time();
        if ($loanbase['is_delete'] == 3) { //初审失败
            $loanbase['publish_wait'] = 1;
            $loanbase['first_audit_time'] = 0;  //初审通过时间重置为0
            $loanbase['first_failure_time'] = time();  //初审失败时间
        } else {
            $loanbase['publish_wait'] = 2;
            $loanbase['delete_msg'] = ''; //初审通过，设为空
            $loanbase['first_audit_time'] = $loanbase['update_time'];  //初审通过时间为当前时间
        }

        $userDao = \Core::dao('user_user');
        $idno = $userDao->findCol('AES_DECRYPT(idno_encrypt,\'__FANWEP2P__\')',$user_id);
        if (!empty($idno)) {
            $user = array();
            $user['byear'] = substr($idno, 6, 4);
            $user['bmonth'] = substr($idno, 10, 2);
            $user['bday'] = substr($idno, 12, 2);
            $user['sex'] = (intval(substr($idno, 16, 1)) % 2) > 0 ? 1 : 0;
            $userDao->update($user,$user_id);
        }
        if(!$loanbase['type_id']) {
            $loanbase['type_id'] = $loan['type_id'];
        }


        // 贷款所在城市修改(根据学信网获取的院校信息匹配院校数据库所在城市绑定贷款所在城市)
        try {
            \Core::db()->begin();
            $region_link = \Core::dao('loan_dealregionlink')->findCol('id', array('deal_id' => $loan_id));
            if (empty($region_link) && $loanbase['publish_wait'] == 2) {
                $user_extend = \Core::dao('user_userextend')->findCol('value', array('user_id' => $user_id, 'field_id' => 24));
                if (!empty($user_extend)) {
                    $school_data = \Core::dao('user_school')->getSchoolData($user_extend);
                    if (!empty($school_data['province_id']) && !empty($school_data['city_id'])) {
                        $deal_city_link['deal_id'] = $loan_id;
                        $deal_city_link['region_pid'] = $school_data['province_id'];
                        $deal_city_link['region_id'] = $school_data['city_id'];
                        $result = \Core::dao('loan_dealregionlink')->insert($deal_city_link);
                        if(!$result) {
                            throw new Exception('1');
                        }
                    }
                }
            }
            $loanext['mortgage_infos'] = $this->mortgage_info();
            $loanext['mortgage_contract'] = $this->mortgage_info("contract");
            //$loanext['view_info'] = $this->mortgage_info("view_info");//认证资料修改
            $loan_type_list = \Core::dao('loan_dealloantype')->getDealLoanTypeList($loanbase['type_id']);
            $loan_type = $loan_type_list[$loanbase['type_id']];

            //重新获取返利配置
            $loanbase['is_referral_award'] = $loan_type['is_referral_award'];

            //记录更改相关数据

            $data = $loanbase;
            $data['id'] = $loan_id;
            $data['user_id'] = $user_id;
            $data['admin_id'] = $this->admininfo['id'];
            $log_id = \Core::business('loan_publish')->updateDealOpLog($data, 1);
            if(!$log_id) {
                throw new Exception('2');
            }

            //更新数据
            $result = $loanbaseDao->update($loanbase, $loan_id);
            if(!$result) {
                throw new Exception('3');
            }

            if ($loanextDao->findCol('loan_id', $loan_id)) {
                $result = $loanextDao->update($loanext, $loan_id);
            } else {
                $loanext['loan_id'] = $loan_id;
                $result = $loanextDao->insert($loanext,false);
            }
            if(!$result) {
                throw new Exception('4');
            }
            if ($loanbase['is_delete'] == 3) { //初审失败
                $result = $dealsatuslogBusiness->saveDealStatusMsg($user_id, $loan_id, 6);
            } else if ($loanbase['publish_wait'] == 2) { //初审通过
                if ($loan['publish_wait'] == 3) { //复审失败后再通过
                    $result = $dealsatuslogBusiness->saveDealStatusMsg($user_id, $loan_id, 5);
                } else {
                    $result = $dealsatuslogBusiness->saveDealStatusMsg($user_id, $loan_id, 4);
                }
            }
            if(!$result) {
                throw new Exception('5');
            }

            $result = $this->saveLog("编号：" . $data['id'] . "，" . $loan['name'] . "初审更新成功", 1);
            if(!$result) {
                throw new Exception('6');
            }
            //mlog('test.'.intval($_REQUEST['first_yn']));
            $result = \Core::business('loan_publish')->updateDealOpLog($log_id, 1, 1);
            if(!$result) {
                throw new Exception('7');
            }
            \Core::db()->commit();
            if ($first_yn == 1) {
                $url = adminUrl('loan_audit','first_publish');
            } elseif($first_yn == 2) {
                $url = adminUrl('loan_audit','my_publish');
            } else {
                $url = adminUrl('loan_audit','publish');
            }
            \Core::message('初审更新成功',$url,'suc',3,'message');
        } catch(Exception $e) {
            \Core::db()->rollback();
            $url = adminUrl('loan_audit','first_publish_edit',array('loan_id'=>$loan_id,'first_yn'=>$first_yn));
            \Core::message('系统错误，请重新进行操作'.$e->getMessage(),$url,'fail',3,'message');
        }

        if ($loanbase['is_delete'] == 3) {

            //TODO 失败短信通知
        }

    }
    

    //复审操作页面
    public function do_true_publish_edit()
    {
        $this->publish_edit();
        $loan_id = intval(\Core::get('loan_id'));
        $type_id = \Core::dao('loan_loanbase')->findCol('type_id',$loan_id);
        $loan_type_list = \Core::dao('loan_dealloantype')->getDealLoanTypeList($type_id);
        $loan_type = $loan_type_list[$type_id];
        $loanBusiness=\Core::business('loan_loanenum');
        \Core::view()->set('deal_fund_types',$loanBusiness->enumDealFundType())
        ->set('is_autobid_type',$loan_type['is_autobid']);

        \Core::view() -> load('loan_truePublishEdit');

    }

    //复审操作
    public function do_true_publish_update()
    {
        $loan_id = intval(\Core::post('loan_id'));
        $loanbaseDao = \Core::dao('loan_loanbase');
        $loanbidDao = \Core::dao('loan_loanbid');
        $loan = $loanbaseDao->getloanbase($loan_id,'name,user_id,borrow_amount,type_id,update_time'); //获取更新前的数据
        $user_id = $loan['user_id'];
        $deal_loan_type_types = 0;  //贷款类型：0学生贷,1信用贷,2抵押贷,3普惠贷
        $log_info = $loan['name'];
        $update_time = $loan['update_time'];
        $loanbase['publish_wait'] = intval(\Core::post['publish']);
        $url = adminUrl('loan_audit','true_publish_edit',array('loan_id'=>$loan_id));

        if(intval($update_time) != intval(\Core::post('update_time'))) {
            \Core::message('当前借款资料在提交的时候发现已经被其他同事变更,请重新点击操作!',$url,'fail',3,'message');
        }
        $loanbase['update_time'] = time();

        $loanbid['fund_type'] = intval(\Core::post('fund_type'));
        if($loanbid['fund_type'] <= 0) {
            \Core::message('请选择资金源类型',$url,'fail',3,'message');
        }
        $loanbid['start_time'] = trim(\Core::post('start_time')) == '' ? 0 : strtotime(\Core::post('start_time'));
        if ($loanbid['start_time'] == 0 && $loanbase['publish_wait'] == 0) {
            \Core::message('请选择开始时间',$url,'fail',3,'message');
        }
        if ($loanbase['publish_wait'] == 3) {
            $loanbase['publish_memo'] = trim(\Core::post('publish_msg'));
            $loanbase['first_audit_time'] = 0; //初审通过时间为0
            $loanbase['second_failure_time'] = time(); //复审失败时间
        } else {
            $loanbase['second_audit_time'] = $loanbase['update_time']; //复审通过时间
        }
        $loan_type_list = \Core::dao('loan_dealloantype')->getDealLoanTypeList($loan['type_id']);
        $loan_type = $loan_type_list[$loanbase['type_id']];
        $deal_loan_type_types =  $loan_type['types'];

        //重新计算风险保证金
        if($loan['borrow_amount']>0) {
            if ($loan_type_list[$loan['type_id']]['is_extend_effect']) {
                $data['l_guarantees_amt'] = $loan['borrow_amount'] * $loan_type['guarantees_amt'] / 100;
            }
        }

        $is_autobid = \Core::post('is_autobid');
        if(isset($is_autobid)) {
            $loanbid['is_autobid'] = intval(\Core::post('is_autobid'));
        }

        $data = $loanbase;
        $data['id'] = $loan_id;
        $data['admin_id'] = $this->admininfo['id'];
        $data['user_id'] = $user_id;
        //todo 事务
        $log_id = \Core::business('loan_publish')->updateDealOpLog($data,7);
        //更新数据
        $loanbaseDao->save($loanbase,$loan_id);
        $loanbidDao->save($loanbid,$loan_id);
        //todo 多人多窗口 操作提示

        $dealsatuslogBusiness = \Core::business('loan_dealstatuslog');
        if ($loanbase['publish_wait'] == 0) { //复审通过
            $dealsatuslogBusiness->saveDealStatusMsg($user_id,$loan_id,7);
            //成功提示,触发自动投标
            $loanBusiness=\Core::business('loan_loanenum');
            $loanBusiness->synDealStatus($loan_id,true);
            
            //todo 发送投标到期的检测队列
            //todo 推送产品id到希财网
            //sys_deal_match 分词查询组件

            
        } else {
            $dealsatuslogBusiness->saveDealStatusMsg($user_id,$loan_id,8);
        }
        //保存日志
        $this->saveLog("编号：" . $data['id'] . "，" . $loan['name'] . "复审更新成功", 1);
        \Core::business('loan_publish')->updateDealOpLog($log_id,7,1);

        $url = adminUrl('admin_audit','true_publish');
        \Core::message('复审更新成功',$url,'suc',3,'message');
        
    }


    public function publish_edit()
    {
        if(chksubmit()) {
            \Core::dump('test');die();
            //提交保存

        }else {
            $loan_id = \Core::get('loan_id',0);
            $first_yn = \Core::get('first_yn',1);
            $loanBusiness=\Core::business('loan_loanenum');
            $usercreditBusiness = \Core::business('user_usercredit');

            //根据借款id，获取贷款基本信息
            $basefields = 'id,deal_sn,name,sub_name,cate_id,user_id,type_id,loantype,borrow_amount,repay_time,rate,is_referral_award,use_type,repay_time_type,use_type,risk_rank,risk_security,update_time,publish_memo';
            $loanbase = \Core::dao('loan_loanbase')->getloanbase($loan_id,$basefields);
            //获取会员名称
            $user_id = Core::arrayGet($loanbase,'user_id');
            $passed = $usercreditBusiness->passed($user_id);
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
            if (!empty($contract)) {
                
            }
            
            //根据借款id，获取标基本信息
            $bidfields = 'loan_id,min_loan_money,max_loan_money,deal_status,start_time,end_time,uloadtype,portion,max_portion,use_ecv';
            $loanbid = \Core::dao('loan_loanbid')->getOneLoanById($loan_id,$bidfields);
            \Core::view()->set('loantype',$loanBusiness->enumLoanType())
                ->set('dealcate',$loanBusiness->enumDealCate())
                ->set('dealusetype',$loanBusiness->enumDealUseType())
                ->set('dealloantype',$loanBusiness->enumDealLoanType())
                ->set('plathtml',$loanBusiness->userPlatRegVerified($loan_id,$user_id))
                ->set('loanbase',$loanbase)
                ->set('passed',$passed)
                ->set('loanbid',$loanbid)
                ->set('username',$username)
                ->set('contract',$contract)
                ->set('contractid',$contractid)
                ->set('l_guarantees_amt',$l_guarantees_amt)
                ->set('commonConfig',$commonConfig)
                ->set('user_detail',$loanBusiness->userDetail($user_id))
                ->set('first_yn',$first_yn)
                ->set('sorcode',$loanBusiness->enumSorCode());
        }
    }

    private function mortgage_info($type = "infos")
    {
        $mortgage_infos = array();
        $cdn_img_host = get_image_cdn_host();
        for ($i = 1; $i <= 20; $i++) {
            if (strim(\Core::post('mortgage_' . $type . '_img_' . $i)) != "") {
                $vv['name'] = strim(\Core::psot('mortgage_' . $type . '_name_' . $i));
                $img = strim(\Core::arrayGet('mortgage_' . $type . '_img_' . $i));
                $vv['img'] = str_replace("http://" . $cdn_img_host, "", $img);
                $mortgage_infos[] = $vv;
            }
        }

        return serialize($mortgage_infos);
    }
}