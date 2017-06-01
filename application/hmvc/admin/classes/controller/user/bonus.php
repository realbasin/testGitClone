<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_user_bonus extends controller_sysBase {
    public function before($method, $args) {
        \Language::read('common,layout');
    }

	public function do_index() {
		
	}

	//优惠券类型新增
	public function do_type_add() {
        if (chksubmit()) {
            $bonus_type_name = \Core::post('bonus_type_name');
            if (empty($bonus_type_name)) {
                \Core::message('优惠券类型名称不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $end_time = \Core::post('end_time');
            if (empty($end_time)) {
                \Core::message('发放/领取结束时间不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $use_end_time = \Core::post('use_end_time');
            $use_end_day = \Core::post('use_end_day');
            if (empty($use_end_time) && empty($use_end_day)) {
                \Core::message('结束使用时间不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $insertData = array(
                'bonus_type_name' => $bonus_type_name,
                'use_type' => \Core::post('use_type'),
                'is_limited' => \Core::post('is_limited'),
                'start_time' => toTimeSpan(\Core::post('start_time')),
                'end_time' => toTimeSpan($end_time),
                'use_start_time' => toTimeSpan(\Core::post('use_start_time')),
                'use_end_time_type' => \Core::post('use_end_time_type'),
                'use_end_time' => toTimeSpan($use_end_time),
                'use_end_day' => intval($use_end_day),
                'send_type' => \Core::post('send_type'),
                'is_effect' => intval(\Core::post('is_effect')),
                'create_time' => getGmtime(),
            );
            $num = \Core::dao('user_bonusType')->insert($insertData);
            if ($num > 0) {
                $this -> log('新增优惠券类型【'.$bonus_type_name.'】', 'add');
                \Core::message('新增优惠券类型成功', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'suc', 3, 'message');
            } else {
                \Core::message('新增优惠券类型失败', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
        }

        \Core::view()->set('func_name', __FUNCTION__);
        \Core::view()->set('bonusType', array());
        \Core::view()->set('sendTypeList', \Core::business('user_bonustype')->getSendType());
	    \Core::view()->load('user_bonusTypeEdit');
    }

    //优惠券类型编辑
    public function do_type_edit() {
        $type_id = intval(\Core::get('type_id',0));

        if (chksubmit()) {
            $bonus_type_name = \Core::post('bonus_type_name');
            if (empty($bonus_type_name)) {
                \Core::message('优惠券类型名称不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $end_time = \Core::post('end_time');
            if (empty($end_time)) {
                \Core::message('发放/领取结束时间不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $use_end_time = \Core::post('use_end_time');
            $use_end_day = \Core::post('use_end_day');
            if (empty($use_end_time) && empty($use_end_day)) {
                \Core::message('结束使用时间不能为空', \Core::getUrl('user_bonus', 'type_add', \Core::config() -> getAdminModule()), 'fail', 3, 'message');
            }
            $updateData = array(
                'bonus_type_name' => $bonus_type_name,
                'use_type' => \Core::post('use_type'),
                'is_limited' => \Core::post('is_limited'),
                'start_time' => toTimeSpan(\Core::post('start_time')),
                'end_time' => toTimeSpan($end_time),
                'use_start_time' => toTimeSpan(\Core::post('use_start_time')),
                'use_end_time_type' => \Core::post('use_end_time_type'),
                'use_end_time' => toTimeSpan($use_end_time),
                'use_end_day' => intval($use_end_day),
                'send_type' => \Core::post('send_type'),
                'is_effect' => intval(\Core::post('is_effect')),
                'update_time' => getGmtime(),
            );

            $num = \Core::dao('user_bonusType')->update($updateData, array('id'=>$type_id));

            if ($num > 0) {
                $this -> log('更新优惠券类型【ID:'.$type_id.' '.$bonus_type_name.'】', 'update');
                \Core::message('更新优惠券类型成功', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'suc', 3, 'message');
            } else {
                \Core::message('更新优惠券类型失败', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'fail', 3, 'message');
            }
        }

        $bonusType = \Core::dao('user_bonustype')->find(array('id'=>$type_id));
        $bonusType['start_time'] = !empty($bonusType['start_time']) ? toDate($bonusType['start_time']) : toDate($bonusType['create_time']);
        $bonusType['use_start_time'] = !empty($bonusType['use_start_time']) ? toDate($bonusType['use_start_time']) : toDate($bonusType['create_time']);

        \Core::view()->set('func_name', __FUNCTION__);
        \Core::view()->set('bonusType', $bonusType);
        \Core::view()->set('sendTypeList', \Core::business('user_bonustype')->getSendType());
        \Core::view()->load('user_bonusTypeEdit');
    }

	//优惠券类型
	public function do_all_type() {
        \Core::view()->load('user_bonusAllType');
    }

    //优惠券类型列表
    public function do_use_log_json() {
        $use_type = \Core::postGet('use_type', 1);
        $is_effect = intval(\Core::postGet('is_effect', -1));
        $pagesize = \Core::postGet('rp');
        $page = \Core::postGet('curpage');
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        $where = array('is_delete'=>0,'use_type'=>$use_type);
        $bonus_type_name = \Core::postGet('bonus_type_name','');
        if (!empty($bonus_type_name)) {
            $where['bonus_type_name like'] = '%'.$bonus_type_name.'%';
        }
        if ($is_effect > -1) {
            $where['is_effect'] = $is_effect;
        }
        $data = \Core::dao('user_bonustype') -> getFlexPage($page, $pagesize, '*', $where, array('id'=>'desc'));

        $outputJson = array(
            'page' => $page,
            'total' => $data['total'],
        );
        foreach ($data['rows'] as $v) {
            $row = array();
            $opration = "<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration .= "<li><a href='javascript:type_edit(".$v['id'].")'>编辑</a></li>";
            $opration .= "<li><a href='javascript:type_bonus(".$v['id'].")'>优惠券</a></li>";
            $opration .= "<li><a href='#'>查看</a></li>";
            $opration .= "<li><a href='javascript:flexDelete(".$v['id'].")'>删除</a></li>";
            $row['id'] = $v['id'];
            $row['cell'][] = $opration;
            $row['cell'][] = $v['id'];
            $row['cell'][] = $v['bonus_type_name'];
            $row['cell'][] = $v['use_type']==1 ? "理财端" : "借款端";
            $row['cell'][] = \Core::business('user_bonustype')->getSendType($v['send_type']);

            $send_time_start = date('Y/m/d', ($v['start_time']>0 ? $v['start_time'] : $v['create_time'])+C('time_zone')*3600);
            $row['cell'][] = $send_time_start." - ".date('Y/m/d', $v['end_time']+C('time_zone')*3600);

            $use_time_start = date('Y/m/d', ($v['use_start_time']>0 ? $v['use_start_time'] : $v['create_time'])+C('time_zone')*3600);
            $use_time_end = $v['use_end_time_type']==1 ? date('Y/m/d', $v['use_end_time']+C('time_zone')*3600) : '用户领取时间+'.$v['use_end_day'].'天';
            $row['cell'][] = $use_time_start." - ".$use_time_end;
            $row['cell'][] = $v['is_effect'] ? "是" : "<font color='#dc143c'>否</font>";
            $row['cell'][] = $v['num'];
            $row['cell'][] = $v['used_num'];
            $row['cell'][] = '￥'.number_format($v['amount'], 2);
            $row['cell'][] = '￥'.number_format($v['userd_amount']/100, 2);
            $row['cell'][] = '';

            $outputJson['rows'][] = $row;
        }
        echo @json_encode($outputJson);
    }

    //删除优惠券类型
    public function do_type_delete() {
        $id = \Core::get("id");
        if (!$id) {
            showJSON(0, \Core::L('parameter_error'));
        }
        $ids = explode(',', $id);
        foreach ($ids as $v) {
            \Core::dao('user_bonustype')->update(array('is_delete'=>1), array('id'=>$v));
        }
        $this -> log('删除优惠券类型[ID:' . $id . ']', 'delete');
        showJSON(200, \Core::L('delete,success'));
    }

    //优惠券列表页面
    public function do_type_bonus() {
        $type_id = \Core::get('type_id',0);
        $bonusType = \Core::dao('user_bonustype')->find(array('id'=>$type_id));

        $bonusRuleList = \Core::dao('user_bonusrule')->getBonusRuleByTypeId($type_id);

        \Core::view()->set('type_id', $type_id);
        \Core::view()->set('bonusType', $bonusType);
        \Core::view()->set('datalist', $bonusRuleList);
        \Core::view()->load('user_bonusRule');
    }

    //新增优惠券
    public function do_bonus_add() {
        $type_id = \Core::get('type_id',0);
        $bonusType = \Core::dao('user_bonustype')->find(array('id'=>$type_id));

        if (chksubmit()) {
            $insertData = array(
                'bonus_type_id' => $type_id,
                'money' => intval(\Core::postGet('money')),
                'limit_amount' =>  intval(\Core::postGet('limit_amount')),
                'num' =>  intval(\Core::postGet('num')),
                'use_deal_month' =>  implode(',', \Core::postGet('use_deal_month')),
                'use_deal_load' =>  intval(\Core::postGet('use_deal_load')),
                'is_effect' =>  intval(\Core::postGet('is_effect')),
                'create_time' =>  getGmtime(),
            );
            $insertId = \Core::dao('user_bonusrule')->insert($insertData, true);
            if ($insertId > 0) {
                $this -> log('新增【ID:'.$type_id.' '.$bonusType['bonus_type_name'].'】优惠券规则成功', 'add');
                \Core::message('新增优惠券规则成功', \Core::getUrl('user_bonus', 'bonus_add', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'suc', 3, 'message');
            }
            \Core::message('新增优惠券规则失败', \Core::getUrl('user_bonus', 'bonus_add', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'fail', 3, 'message');
        }

        \Core::view()->set('bonusType', $bonusType)->load('user_bonusAdd');
    }

    public function do_bonus_edit() {
        $rule_id = intval(\Core::get('rule_id'));
        $bonusRule = \Core::dao('user_bonusrule')->find(array('id'=>$rule_id));
        $bonusType = \Core::dao('user_bonustype')->find(array('id'=>$bonusRule['bonus_type_id']));

        if (chksubmit()) {
            $updateData = array(
                'use_deal_load' =>  intval(\Core::postGet('use_deal_load')),
                'is_effect' =>  intval(\Core::postGet('is_effect')),
            );
            $insertId = \Core::dao('user_bonusrule')->update($updateData, array('id'=>$rule_id));
            if ($insertId > 0) {
                $this -> log('修改【'.$bonusType['bonus_type_name'].'】优惠券[ID:'.$rule_id.']规则成功', 'add');
                \Core::message('修改优惠券成功', \Core::getUrl('user_bonus', 'bonus_edit', \Core::config() -> getAdminModule(), array('rule_id'=>$rule_id)), 'suc', 3, 'message');
            }
            \Core::message('修改优惠券失败', \Core::getUrl('user_bonus', 'bonus_edit', \Core::config() -> getAdminModule(), array('rule_id'=>$rule_id)), 'fail', 3, 'message');
        }

        \Core::view()->set('bonusRule', $bonusRule);
        \Core::view()->set('bonusType', $bonusType);
        \Core::view()->load('user_bonusEdit');
    }

    //删除优惠券
    public function do_bonus_delete() {
        $id = \Core::get("id");
        $bonus_type_name = trim(\Core::get('bonus_type_name'));
        if (!$id) {
            showJSON(0, \Core::L('parameter_error'));
        }
        $ids = explode(',', $id);
        foreach ($ids as $v) {
            \Core::dao('user_bonusrule')->update(array('is_delete'=>1), array('id'=>$v));
        }
        //$this -> log('删除优惠券类型【'.$bonus_type_name.'】中优惠券编号[ID:' . $id . ']', 'delete');
        showJSON(200, \Core::L('delete,success'));
    }

    //手动发放优惠券
    public function do_manual() {
        \Core::view()->load('user_bonusManual');
    }

    //优惠券使用情况
    public function do_use_log() {
        \Core::view()->load('user_bonusUseLog');
    }


}