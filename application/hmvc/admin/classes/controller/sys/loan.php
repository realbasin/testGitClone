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
            $operateStr .= "<li><a href='javascript:type_edit(" . $v['id'] . ")'>编辑</a></li>";
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

    public function do_type_add()
    {
        if (chksubmit()) {
            $data = \Core::post();

            $dealLoanTypeBusiness = \Core::business('sys_dealloantype');
            $flag = $dealLoanTypeBusiness->insert($data);
            if ($flag) {
                \Core::redirect(\Core::getUrl('sys_loan', 'type_list', \Core::config()->getAdminModule()));
            } else {
                \Core::message($dealLoanTypeBusiness->getMsg());
            }
        } else {
            //获取借款类型列表
            $dealUserTypeList = \Core::dao('loan_dealusetype')->getAllDealUseType();
            \Core::view()->set('dealUserTypeList', $dealUserTypeList);

            $maxSort = \Core::dao('loan_dealloantype')->getMaxSort() + 1;
            \Core::view()->set('maxSort', $maxSort);

            $dealLoanTypeBusiness = \Core::business('sys_dealloantype');
            $collateralList = $dealLoanTypeBusiness->getCollateralList();
            \Core::view()->set('collateralList', $collateralList);

            $loanTypeList = $dealLoanTypeBusiness->getLoanTypeList();
            \Core::view()->set('loanTypeList', $loanTypeList);
            \Core::view()->load("sys_loanTypeAdd");
        }
    }

    public function do_type_edit()
    {
        if (chksubmit()) {
            $data = \Core::post();

            $dealLoanTypeBusiness = \Core::business('sys_dealloantype');
            $flag = $dealLoanTypeBusiness->update($data);
            if ($flag) {
                \Core::redirect(\Core::getUrl('sys_loan', 'type_list', \Core::config()->getAdminModule()));
            } else {
                \Core::message($dealLoanTypeBusiness->getMsg());
            }
        } else {
            $id = \Core::get('id');
            $dealLoanType = \Core::dao('loan_dealloantype')->getDealLoanType($id);
            \Core::view()->set('dealLoanType', $dealLoanType);

            $dealLoanTypeBusiness = \Core::business('sys_dealloantype');
            $dealLoanTypeExtern = \Core::dao('dealloantypeextern')->getRowByTypeId($dealLoanType['id']);
            if (empty($dealLoanTypeExtern)) {
                $dealLoanTypeExtern = $dealLoanTypeBusiness->getEmptyDealLoanTypeExtern();
            }
            \Core::view()->set('dealLoanTypeExtern', $dealLoanTypeExtern);

            //获取贷款类型信用等级
            $userLevelList = \Core::dao('dealloantypeuserlevel')->getAllLevel($dealLoanType['id']);
            \Core::view()->set('userLevelList', $userLevelList);

            //获取借款类型列表
            $dealUserTypeList = \Core::dao('loan_dealusetype')->getAllDealUseType();
            \Core::view()->set('dealUserTypeList', $dealUserTypeList);

            $regionConfDao = \Core::dao('regionconf');
            $provinceList = $regionConfDao->getProvinceList();
            \Core::view()->set('provinceList', $provinceList);

            //获取省份城市列表
            $provinceCityList = $regionConfDao->getProvinceCityList();
            \Core::view()->set('provinceCity', json_encode($provinceCityList));

            $maxSort = \Core::dao('loan_dealloantype')->getMaxSort() + 1;
            \Core::view()->set('maxSort', $maxSort);

            $collateralList = $dealLoanTypeBusiness->getCollateralList();
            \Core::view()->set('collateralList', $collateralList);

            $loanTypeList = $dealLoanTypeBusiness->getLoanTypeList();
            \Core::view()->set('loanTypeList', $loanTypeList);

            \Core::view()->load("sys_loanTypeEdit");
        }
    }

    public function do_loan_type_user_level_edit()
    {
        if (chksubmit()) {
            \Core::dao('dealloantypeuserlevel')->updateData(\Core::post());

            $loanTypeId = \Core::post('loan_type_id');
            \Core::redirect(\Core::getUrl('sys_loan', 'type_edit', \Core::config()->getAdminModule(), ['id' => $loanTypeId]));
        } else {
            $id = \Core::get('id');
            $userLevel = \Core::dao('dealloantypeuserlevel')->getRowById($id);
            \Core::view()->set('userLevel', $userLevel);
            \Core::view()->load("sys_loanTypeUserLevelEdit");
        }
    }

}