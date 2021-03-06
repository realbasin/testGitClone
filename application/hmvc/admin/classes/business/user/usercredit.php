<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_user_usercredit extends Business {
    public function business() {

    }

    //查询用户认证资料是否已审核
    public function passed($user_id)
    {
        $userDao = \Core::dao('user_user');
        $usercreditfileDao = \Core::dao('user_usercreditfile');
        $dealloantypeDao = \Core::dao('loan_dealloantype');
        $usercredittypeDao = \Core::dao('user_usercredittype');
        $user_info=$userDao->getUser($user_id,'*');
        $field_array = array(
            "credit_identificationscanning" => "idcardpassed",
            "credit_contact" => "workpassed",
            "credit_credit" => "creditpassed",
            "credit_incomeduty" => "incomepassed",
            "credit_house" => "housepassed",
            "credit_car" => "carpassed",
            "credit_marriage" => "marrypassed",
            "credit_titles" => "skillpassed",
            "credit_videoauth" => "videopassed",
            "credit_mobilereceipt" => "mobiletruepassed",
            "credit_residence" => "residencepassed",
            "credit_seal" => "sealpassed",
        );

        $t_credit_file = $usercreditfileDao->getByUserId($user_id);
        $credit_file = array();
        foreach ($t_credit_file as $k => $v) {
            $file_list = array();
            if ($v['file'])
                $file_list = unserialize($v['file']);

            if (is_array($file_list))
                $v['file_list'] = $file_list;

            $credit_file[$v['type']] = $v;
        }


        $loantype = intval(\Core::getPost('loantype'));
        $needs_credits = array();
        if ($loantype > 0) {
            $loantypeinfo = $dealloantypeDao->getDealLoanTypes('*',array('id'=>$loantype));
            if ($loantypeinfo['credits'] != "") {
                $needs_credits = unserialize($loantypeinfo['credits']);
            }
        }

        $credit_type = $usercredittypeDao->getCreditType();
        $credit_list = array();
        foreach ($credit_type['list'] as $k => $v) {

            if ($v['must'] == 1 || $loantype == 0 || (count($needs_credits) > 0 && in_array($v['type'], $needs_credits))) {
                $credit_list[$v['type']] = $credit_type['list'][$v['type']];
                $credit_list[$v['type']]['credit'] = \Core::arrayGet($credit_file,$v['type']);

                //User表里面的数据
                if (\Core::arrayGet($user_info,\Core::arrayGet($field_array,$v['type']))) {
                    $credit_list[$v['type']]['credit']['passed'] = $user_info[$field_array[$v['type']]];
                }
            }
        }

        $passed = 1;
        foreach ($credit_list as $k => $v) {
            if ($v['credit']['passed'] != 1)
                $passed = 0;
        }
        if ($passed)
            return "已审核通过";
        else
            return "待审核";
    }
}