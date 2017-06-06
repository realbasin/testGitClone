<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 系统设置-贷款设置
 * Class controller_sys_loan
 */
class  controller_sys_loan extends controller_sysBase
{

    public function before($method, $args)
    {
        \Language::read('setting');
    }

    /**
     * 贷款类型设置
     */
    public function do_type_list()
    {
        \Core::view()->load("sys_loanTypeList");
    }

    /**
     * 贷款类型设置，获取Json数据
     */
    public function do_type_list_json()
    {
        //每页显示行数
        $pageSize = \Core::postGet('rp');
        //当前页
        $page = \Core::postGet('curpage');
        if (!$page || !is_numeric($page))
            $page = 1;

        if (!$pageSize || !is_numeric($pageSize))
            $pageSize = 15;

        //简易排序条件
        $orderBy = [];
        if (\Core::postGet('sortorder')) {
            $orderBy[\Core::postGet('sortname')] = \Core::postGet('sortorder');
        }

        $data = \Core::dao('loan_dealloantype')->getFlexPage($page, $pageSize, '*', [], $orderBy);
        if (!($data['rows'])) {
            echo @json_encode([]);
            exit;
        }

        //处理返回结果
        $json = ['page' => $page, 'total' => $data['total']];

        foreach ($data['rows'] as $v) {
            $row = array();
            $row['id'] = $v['id'];
            $operateStr = "<span class='btn'><em><i class='fa fa-edit'></i>" . \Core::L('operate') . " <i class='arrow'></i></em><ul>";
            $operateStr .= "<li><a href='javascript:loan_detail(" . $v['id'] . ")'>编辑</a></li>";
            $operateStr .= "</ul></span>";
            $row['cell'][] = $operateStr;
            $row['cell'][] = $v['id'];
            $row['cell'][] = $v['name'];
            $row['cell'][] = $v['is_quota'] ? '是' : '否';
            $row['cell'][] = $v['is_effect'] ? '是' : '否';
            $row['cell'][] = $v['sort'];
            $row['cell'][] = '';
            $json['rows'][] = $row;
        }

        //返回JSON
        echo @json_encode($json);
    }

    /**
     * 获取抵押物列表
     */
    private function getCollateralList()
    {
        $collateralList = [
            ["id" => 1, "name" => "住房(信)"],
            ["id" => 2, "name" => "住房(押)"],
        ];

        return $collateralList;
    }

    /**
     * 获取借款类别列表
     * @return array
     */
    private function getLoanTypeList()
    {
        $result = array(
            ["id" => 0, "name" => "学生贷"],
            ["id" => 1, "name" => "信用贷"],
            ["id" => 2, "name" => "抵押贷"],
            ['id' => 3, 'name' => '普惠贷']
        );
        return $result;
    }

    public function do_type_add()
    {
        if (chksubmit()) {
            print_r($_POST);
        } else {
            //获取借款类型列表
            $dealUserTypeList = \Core::dao('loan_dealusetype')->getAllDealUseType();
            \Core::view()->set('dealUserTypeList', $dealUserTypeList);

            $maxSort = \Core::dao('loan_dealloantype')->getMaxSort() + 1;
            \Core::view()->set('maxSort', $maxSort);

            $collateralList = $this->getCollateralList();
            \Core::view()->set('collateralList', $collateralList);

            $loanTypeList = $this->getLoanTypeList();
            \Core::view()->set('loanTypeList', $loanTypeList);
            \Core::view()->load("sys_loanTypeAdd");
        }
    }

}