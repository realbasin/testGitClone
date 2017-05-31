<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_user_bonus extends controller_sysBase {
    public function before($method, $args) {
        \Language::read('common,layout');
    }

	public function do_index() {
		
	}

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
                $this -> log('更新优惠券类型【'.$bonus_type_name.'】', 'update');
                \Core::message('更新优惠券类型成功', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'suc', 3, 'message');
            } else {
                \Core::message('更新优惠券类型失败', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'fail', 3, 'message');
            }
        }

        $bonusType = \Core::dao('user_bonustype')->getBonusTypeById($type_id);
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

    //手动发放优惠券
    public function do_manual() {
        \Core::view()->load('user_bonusManual');
    }

    //优惠券使用情况
    public function do_use_log() {
        \Core::view()->load('user_bonusUseLog');
    }

    //优惠券类型列表
    public function do_use_log_json() {
        $pagesize = \Core::postGet('rp');
        $page = \Core::postGet('curpage');
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        $data = \Core::dao('user_bonustype') -> getFlexPage($page, $pagesize, '*', null, array('id'=>'desc'));

        $outputJson = array(
            'page' => $page,
            'total' => $data['total'],
        );
        foreach ($data['rows'] as $v) {
            $row = array();
            $opration = "<span class='btn'><em><i class='fa fa-edit'></i>".\Core::L('operate')." <i class='arrow'></i></em><ul>";
            $opration .= "<li><a href='#'>查看</a></li>";
            $opration .= "<li><a href='#'>发放</a></li>";
            $opration .= "<li><a href='javascript:type_edit(".$v['id'].")'>编辑</a></li>";
            $opration .= "<li><a href='#'>删除</a></li>";
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
            $row['cell'][] = $v['num'];
            $row['cell'][] = $v['used_num'];
            $row['cell'][] = '￥'.number_format($v['amount'], 2);
            $row['cell'][] = '￥'.number_format($v['userd_amount']/100, 2);
            $row['cell'][] = '';

            $outputJson['rows'][] = $row;
        }
        echo @json_encode($outputJson);
    }
}