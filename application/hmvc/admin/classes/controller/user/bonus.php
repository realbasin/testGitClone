<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  controller_user_bonus extends controller_sysBase {
    public function before($method, $args) {
        \Language::read('common,layout');
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
                'start_time' => strtotime(\Core::post('start_time')),
                'end_time' => strtotime($end_time),
                'use_start_time' => strtotime(\Core::post('use_start_time')),
                'use_end_time_type' => \Core::post('use_end_time_type'),
                'use_end_time' => strtotime($use_end_time),
                'use_end_day' => intval($use_end_day),
                'send_type' => \Core::post('send_type'),
                'is_effect' => intval(\Core::post('is_effect')),
                'create_time' => time(),
            );
            $insertId = \Core::dao('user_bonustype')->insert($insertData);
            if ($insertId > 0) {
                $this -> log('新增优惠券类型【ID:'.$insertId.' '.$bonus_type_name.'】', 'add');
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
                'start_time' => strtotime(\Core::post('start_time')),
                'end_time' => strtotime($end_time),
                'use_start_time' => strtotime(\Core::post('use_start_time')),
                'use_end_time_type' => \Core::post('use_end_time_type'),
                'use_end_time' => strtotime($use_end_time),
                'use_end_day' => intval($use_end_day),
                'send_type' => \Core::post('send_type'),
                'is_effect' => intval(\Core::post('is_effect')),
                'update_time' => time(),
            );

            $num = \Core::dao('user_bonustype')->update($updateData, array('id'=>$type_id));

            if ($num > 0) {
                $this -> log('更新优惠券类型【ID:'.$type_id.' '.$bonus_type_name.'】', 'update');
                \Core::message('更新优惠券类型成功', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'suc', 3, 'message');
            } else {
                \Core::message('更新优惠券类型失败', \Core::getUrl('user_bonus', 'type_edit', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'fail', 3, 'message');
            }
        }

        $bonusType = \Core::dao('user_bonustype')->find(array('id'=>$type_id));
        $bonusType['start_time'] = !empty($bonusType['start_time']) ? date($bonusType['start_time']) : date($bonusType['create_time']);
        $bonusType['use_start_time'] = !empty($bonusType['use_start_time']) ? date($bonusType['use_start_time']) : date($bonusType['create_time']);

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
    public function do_all_type_json() {
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
            $opration .= "<li><a href='javascript:use_log(".$v['id'].",".$v['use_type'].")'>查看</a></li>";
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
        $bonusTypeName = \Core::dao('user_bonustype')->findCol('bonus_type_name', $type_id);

        $bonusRuleList = \Core::dao('user_bonusrule')->getBonusRuleByTypeId($type_id);

        \Core::view()->set('type_id', $type_id);
        \Core::view()->set('bonusTypeName', $bonusTypeName);
        \Core::view()->set('datalist', $bonusRuleList);
        \Core::view()->load('user_bonusRule');
    }

    //新增优惠券
    public function do_bonus_add() {
        $type_id = \Core::get('type_id',0);
        $bonusType = \Core::dao('user_bonustype')->find($type_id);

        if (chksubmit()) {
            $insertData = array(
                'bonus_type_id' => $type_id,
                'money' => intval(\Core::postGet('money')),
                'limit_amount' =>  intval(\Core::postGet('limit_amount')),
                'num' =>  intval(\Core::postGet('num')),
                'use_deal_month' =>  implode(',', \Core::postGet('use_deal_month')),
                'use_deal_load' =>  intval(\Core::postGet('use_deal_load')),
                'is_effect' =>  intval(\Core::postGet('is_effect')),
                'create_time' =>  time(),
            );
            $insertId = \Core::dao('user_bonusrule')->insert($insertData);
            if ($insertId > 0) {
                $this -> log('新增【ID:'.$type_id.' '.$bonusType['bonus_type_name'].'】优惠券规则成功', 'add');
                \Core::message('新增优惠券规则成功', \Core::getUrl('user_bonus', 'bonus_add', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'suc', 3, 'message');
            }
            \Core::message('新增优惠券规则失败', \Core::getUrl('user_bonus', 'bonus_add', \Core::config() -> getAdminModule(), array('type_id'=>$type_id)), 'fail', 3, 'message');
        }

        \Core::view()->set('bonusType', $bonusType)->load('user_bonusAdd');
    }

    //编辑优惠券
    public function do_bonus_edit() {
        $rule_id = intval(\Core::get('rule_id'));
        $bonusRule = \Core::dao('user_bonusrule')->find($rule_id);
        $bonusTypeName = \Core::dao('user_bonustype')->findCol('bonus_type_name', $bonusRule['bonus_type_id']);

        if (chksubmit()) {
            $updateData = array(
                'use_deal_load' =>  intval(\Core::postGet('use_deal_load')),
                'is_effect' =>  intval(\Core::postGet('is_effect')),
            );
            $insertId = \Core::dao('user_bonusrule')->update($updateData, array('id'=>$rule_id));
            if ($insertId > 0) {
                $this -> log('修改【'.$bonusTypeName.'】优惠券[ID:'.$rule_id.']规则成功', 'add');
                \Core::message('修改优惠券成功', \Core::getUrl('user_bonus', 'bonus_edit', \Core::config() -> getAdminModule(), array('rule_id'=>$rule_id)), 'suc', 3, 'message');
            }
            \Core::message('修改优惠券失败', \Core::getUrl('user_bonus', 'bonus_edit', \Core::config() -> getAdminModule(), array('rule_id'=>$rule_id)), 'fail', 3, 'message');
        }

        \Core::view()->set('bonusRule', $bonusRule);
        \Core::view()->set('bonusTypeName', $bonusTypeName);
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
        $this -> log('删除优惠券类型【'.$bonus_type_name.'】中优惠券编号[ID:' . $id . ']', 'delete');
        showJSON(200, \Core::L('delete,success'));
    }

    //手动发放优惠券
    public function do_manual() {
        \Core::view()->load('user_bonusManual');
    }

    //优惠券使用情况
    public function do_use_log() {
        $use_type = \Core::postGet('use_type', 1);
        $type_id = \Core::postGet('type_id', 0);
        if ($type_id > 0) {
            $bonus_type_name = \Core::dao('user_bonustype')->findCol('bonus_type_name', $type_id);
        } else {
            $bonus_type_name = '';
        }

        \Core::view()->set('use_type', $use_type)->set('type_id', $type_id)->set('bonus_type_name', $bonus_type_name);
        \Core::view()->load('user_bonusUseLog');
    }

    //优惠券使用情况列表
    public function do_use_log_json() {
        $pagesize = \Core::postGet('rp');
        $page = \Core::postGet('curpage');
        if (!$page || !is_numeric($page))
            $page = 1;
        if (!$pagesize || !is_numeric($pagesize))
            $pagesize = 15;

        $where = array();
        $where['use_type'] = \Core::postGet('use_type', 1);
        $where['bonus_type_id'] = \Core::postGet('type_id', 0);
        $where['bonus_type_name'] = \Core::postGet('bonus_type_name', '');
        $where['start_time'] = \Core::postGet('drawed_time_start', ''); //领取时间（开始）
        $where['end_time'] = \Core::postGet('drawed_time_end', '');    //领取时间（结束）
        $where['bonus_sn'] = \Core::postGet('bonus_sn', '');    //优惠券号
        $where['user_id'] = \Core::postGet('user_id', '');
        $where['user_name'] = \Core::postGet('user_name', '');
        $where['mobile'] = \Core::postGet('mobile', '');
        $where['used_time_start'] = \Core::postGet('used_time_start', ''); //使用时间（开始）
        $where['used_time_end'] = \Core::postGet('used_time_end', '');    //使用时间（结束）
        $where['use_status'] = \Core::postGet('use_status', 0);    //使用情况：0-全部；1-未使用；2-已使用；3-已过期
        $where['rule_effect'] = \Core::postGet('rule_effect', 0);    //规则启用：0-全部；1-启用；2-禁用
        $where['rule_delete'] = \Core::postGet('rule_delete', 0);    //规则删除：0-全部；1-未删除；2-已删除
        $where['issue_type'] = \Core::postGet('issue_type', 0);    //领取方式：-1-全部；0-系统派发；1-手动发放

        $responseData = \Core::business('user_bonususer')->getBonusUserLogListByCondition($where, $page, $pagesize);

        if (!empty($responseData)) {
            $outputJson = array(
                'page' => $page,
                'total' => $responseData['total'],
            );

            foreach ($responseData['rows'] as $v) {
                $row = array();
                $row['cell'][] = $v['id'];
                $row['cell'][] = $v['bonus_sn'];
                $row['cell'][] = $v['bonus_type_name'];
                $row['cell'][] = $v['use_type'] == 1 ? "理财端" : "借款端";
                $user_info = \Core::dao('user_user')->getUserInfo('user_name,mobile', array('id'=>$v['user_id']));
                $row['cell'][] = !empty($user_info) ? $user_info->value('user_name') : '';
                $row['cell'][] = !empty($user_info) ? $user_info->value('mobile') : '';
                $row['cell'][] = $v['money'];
                $row['cell'][] = $v['limit_amount'];
                $row['cell'][] = date('Y-m-d H:i:s', $v['drawed_time']);
                $row['cell'][] = $v['issue_type'] ? '手动发放' : '系统派发';
                $row['cell'][] = !empty($v['used_time']) ? date('Y-m-d H:i:s', $v['used_time']) : '';
                //使用情况
                switch ($v['module']) {
                    case 'deal':
                        $row['cell'][] = '<a href="#'.$v['module_pk_Id'].'" target="_blank">查看申请</a>';
                        break;
                    case 'deal_load':
                        $row['cell'][] = '<a href="#'.$v['module_pk_Id'].'" target="_blank">查看投资标</a>';
                        break;
                    case 'deal_load_transfer':
                        $row['cell'][] = '<a href="#'.$v['module_pk_Id'].'" target="_blank">查看债权标</a>';
                        break;
                    default:
                        if (empty($v['used_time'])) {
                            if ($v['used_end_time'] > time()) {
                                $row['cell'][] = '未使用';
                            } else {
                                $row['cell'][] = '已过期';
                            }
                        } else {
                            $row['cell'][] = '数据异常';
                        }
                }
                $row['cell'][] = '';

                $outputJson['rows'][] = $row;
            }
        } else {
            $outputJson = array('page'=>$page,'total'=>0,'rows'=>array());
        }
        echo @json_encode($outputJson);
    }
}