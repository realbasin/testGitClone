<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_publish extends Business
{
    public function business()
    {

    }

    public function updateDealOpLog($data,$type=1,$result=0)
    {
        $loanoplog = Core::dao('loan_loanoplog');
        if ($result != 0) {
            $ids = explode(',', $data);
            if ($result == 1) {
                $op_result = '成功';
            } elseif ($result == -1) {
                $op_result = '失败';
            }
            foreach ($ids as $k=>$id) {
                $log_data[$k]['op_result'] = $op_result;
                $log_data[$k]['id'] = $id;
            }
            $loanoplog->updateBatch($log_data, 'id');
            return;
        }
        if ($type == 1) {//初审操作修改
            $op_name = '初审操作';
        } elseif ($type == 4) {
            $op_name = '彻底删除操作';
            $log_data['log'] = serialize($data);
        } elseif ($type == 5) {
            $data['is_delete'] = 1;
            $op_name = '删除操作';
        } elseif ($type == 6) {
            $data['is_delete'] = 0;
            $op_name = '恢复操作';
        } elseif ($type == 7) {
            $op_name = '复审操作';
        }
        $log_data['admin_id'] = intval($data['admin_id']);
        $log_data['deal_id'] = intval($data['id']);
        $log_data['user_id'] = intval($data['user_id']);
        $log_data['create_time'] = time();
        $log_data['op_id'] = $type;
        $log_data['op_name'] = $op_name;
        $log_data['ip'] = \Core::clientIp();

        if ($type == 4) {
            $log_id = $loanoplog->insert($log_data);
            return $log_id;
        }

        $col_name_arr = array(
            array(array('col' => 'city_id', 'name' => '所在城市', 'model' => 'DealCityLink', 'linktype' => 'model', 'linkmodel' => 'DealCity', 'linkcol' => 'name')),
            array(array('col' => 'icon', 'name' => '缩略图', 'model' => 'loan_loanbase')),
            array(
                array('col' => 'is_delete', 'name' => '资料状态', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '用户建立', 1 => '删除', 2 => '用户保存', 3 => '初审失败')),
                array('col' => 'delete_real_msg', 'name' => '初审失败原因', 'model' => 'Deal'),
                array('col' => 'publish_wait', 'name' => '发布状态', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '复审通过', 1 => '待发布', 2 => '初审通过', 3 => '复审失败')),
                array('col' => 'publish_memo', 'name' => '复审失败原因', 'model' => 'Deal'),
                array('col' => 'fund_type', 'name' => '资金源类型', 'model' => 'Deal', 'linktype' => 'model', 'linkmodel' => 'DealFundType', 'linkcol' => 'type_name'),
                array('col' => 'start_time', 'name' => '开始招标时间', 'model' => 'Deal', 'calctype' => "time"),
                array('col' => 'borrow_amount', 'name' => '借款金额', 'model' => 'Deal'),
                array('col' => 'rate', 'name' => '年化利率 ', 'model' => 'Deal'),
                array('col' => 'l_guarantees_amt', 'name' => '风险保证金', 'model' => 'Deal')),
            array(
                array('groupcol' => 'repay_time', 'name' => '借款期限', 'model' => 'Deal'),
                array('groupcol' => 'repay_time_type', 'name' => '借款期限', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '天', 1 => '月'))),
            array(
                array('col' => 'name', 'name' => '贷款名称', 'model' => 'Deal'),
                array('col' => 'sub_name', 'name' => '简短名称', 'model' => 'Deal'),
                array('col' => 'loantype', 'name' => '还款方式', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '等额本息', 1 => '付息还本', 2 => '到期还本息', 3 => '本金均摊，利息固定'))),
            array(array('col' => 'use_ecv', 'name' => '是否可使用红包', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '不可使用', 1 => '可使用'))),
            array(array('col' => 'description', 'name' => '借款描述', 'model' => 'Deal')),
            array(array('col' => 'risk_rank', 'name' => '风险等级', 'model' => 'Deal', 'linktype' => 'array', 'linkarray' => array(0 => '低', 1 => '中', 2 => '高'))),
            array(array('col' => 'risk_security', 'name' => '风险控制', 'model' => 'Deal')),
            array(array('col' => 'attachment', 'name' => '合同附件', 'model' => 'Deal')),
            array(array('col' => 'tattachment', 'name' => '转让合同附件', 'model' => 'Deal')),
            array(array('col' => 'use_type', 'name' => '借款用途', 'model' => 'Deal', 'linktype' => 'model', 'linkmodel' => 'DealUseType', 'linkcol' => 'name')),
            array(array('col' => 'sort', 'name' => '排序', 'model' => 'Deal')),
            array(array('col' => 'first_audit_admin_id', 'name' => '初审人', 'model' => 'Deal', 'linktype' => 'model', 'linkmodel' => 'Admin', 'linkcol' => 'real_name')),
        );
        $log_ids = 0;
        foreach ($col_name_arr as $k=>$col) {

            $want_log = 0;
            $old_val = '';
            $new_val = '';
            foreach ($col as $col1) {
                if (\Core::arrayGet($data, $col1['groupcol']) || \Core::arrayGet($data, $col1['col'])) {//有值才处理
                    if (empty(\Core::arrayGet($data, $col1['groupcol']))) {
                        $col_old_val = \Core::dao($col1['model'])->findCol($col1['col'], array('id' => intval($data['id'])));
                        if ($col_old_val != $data[$col1['col']]) { //有变化
                            $want_log = 1;
                            if (empty(\Core::arrayGet($col1, 'linktype'))) { //取本值
                                $data_val = empty(\Core::arrayGet($data, $col1['col'])) ? '无' : \Core::arrayGet($data, $col1['col']);

                            } else {
                                if ($col1['linktype'] == 'array') {
                                    $col_old_val = \Core::arrayGet($col1['linkarray'], intval($col_old_val));
                                    $data_val = \Core::arrayGet($col1['linkarray'], intval($data[$col1['col']]));
                                }
                                if ($col1['linktype'] == 'model') {
                                    $col_old_val = \Core::dao($col1['linkmodel'])->findCol($col1['linkcol'], array('id' => intval($col_old_val)));
                                    $data_val = \Core::dao($col1['linkmodel'])->findCol($col1['linkcol'], array('id' => intval($data[$col1['col']])));

                                }
                            }
                            if ($col1['calctype'] == 'time') {
                                $col_old_val = date('Y-m-d H:i:s', $col_old_val);
                                $data_val = date('Y-m-d H:i:s', $data_val);
                            }
                            $col_old_val = (empty($col_old_val) || $col_old_val == '1970-01-01 00:00:00') ? '' : $col_old_val;
                            $data_val = (empty($data_val) && $col1['col'] == 'first_audit_admin_id') ? '全部' : $data_val;
                            if (($col1['col'] == 'is_delete' && $type == 1) || $col1['col'] == 'publish_wait')
                                $log_data['log'] .= $data_val . '；';
                            elseif ($col1['col'] == 'auditing_status' && $want_log == 0)
                                $log_data['log'] .= $col1['name'] . '：【' . $data_val . '】；';
                            else {
                                if ($col1['col'] == 'rate') {
                                    $col_old_val .= '%';
                                    $data_val .= '%';
                                }
                                $log_data['log'] .= '修改【' . $col1['name'] . '】：' . (empty($col_old_val) ? '' : '从【' . $col_old_val . '】改') . '为【' . $data_val . '】；';
                            }
                        }
                    } else {//组装栏位
                        $col_old_val = \Core::dao($col1['model'])->findCol($col1['groupcol'], array('id' => intval($data['id'])));
                        if ($col_old_val != $data[$col1['groupcol']]) { //有变化
                            $want_log = 2;
                        }
                        if (empty(\Core::arrayGet($col1, 'linktype'))) { //取本值
                            $data_val = $data[$col1['groupcol']];
                            $data_val = empty(\Core::arrayGet($data, $col1['groupcol'])) ? '无' : $data_val;
                        } else {
                            if ($col1['linktype'] == 'array') {
                                $col_old_val = \Core::arrayGet($col1['linkarray'], $col_old_val);
                                $data_val = \Core::arrayGet($col1['linkarray'],
                                    $data[$col1['groupcol']]);
                            }
                            if ($col1['linktype'] == 'model') {
                                $col_old_val = \Core::dao($col1['linkmodel'])->findCol($col1['linkcol'], array('id' => intval($col_old_val)));
                                $data_val = \Core::dao($col1['linkmodel'])->findCol($col1['linkcol'], array('id' => intval($data[$col1['col']])));
                            }
                        }
                        if (\Core::arrayGet($col1, 'calctype') == 'time') {
                            $col_old_val = date('Y-m-d H:i:s', $col_old_val);
                            $data_val = date('Y-m-d H:i:s', $data_val);
                        }
                        $col_old_val = ($col_old_val == '1970-01-01 00:00:00') ? '' : $col_old_val;
                        $old_val .= $col_old_val;
                        $new_val .= $data_val;
                    }

                }
            }
            if ($want_log >= 1) {
                if ($want_log == 2) 
                    $log_data['log'] = '修改【' . $col1['name'] . '】：从【' . $old_val . '】改为【' . $new_val . '】；';
                $log_id = $loanoplog->insert($log_data);
                if ($log_id) {
                    $log_ids .= ','.$log_id;
                } else {
                    return false;
                }
            }
            $log_data['log'] = '';
        }
        
        return $log_ids;
    }


    public function true_publish_update()
    {


    }

}