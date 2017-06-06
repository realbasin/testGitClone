<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 贷款类型业务类
 * Class business_sys_dealloantype
 */
class  business_sys_dealloantype extends Business
{

    private $msg;

    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * 获取抵押物列表
     */
    public function getCollateralList()
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
    public function getLoanTypeList()
    {
        $result = array(
            ["id" => 0, "name" => "学生贷"],
            ["id" => 1, "name" => "信用贷"],
            ["id" => 2, "name" => "抵押贷"],
            ['id' => 3, 'name' => '普惠贷']
        );
        return $result;
    }

    private function getDealLoanType($data)
    {
        $dealLoanType = [
            'name' => $data['name'],
            'brief' => $data['brief'],
            'pid' => 0,
            'is_delete' => 0,
            'is_effect' => 1,
            'is_display' => 1,
            'is_autobid' => isset($data['is_autobid']) && $data['is_autobid'] ? 1 : 0,
            'is_use_ecv' => isset($data['is_use_ecv']) && $data['is_use_ecv'] ? 1 : 0,
            'is_use_bonus' => isset($data['is_use_bonus']) && $data['is_use_bonus'] ? 1 : 0,
            'is_referral_award' => isset($data['is_referral_award']) && $data['is_referral_award'] ? 1 : 0,
            'is_pay_off_limit' => isset($data['is_pay_off_limit']) && $data['is_pay_off_limit'] ? 1 : 0,
            'sort' => $data['sort'],
            'uname' => '',
            'applyto' => $data['applyto'],
            'condition' => $data['condition'],
            'credits' => '',
            'usetypes' => implode(',', $data['usetypes']),
            'collaterals' => isset($data['collaterals']) && $data['collaterals'] ? implode(',', $data['collaterals']) : '',
            'types' => isset($data['types']) && $data['types'] ? 1 : 0,
            'is_quota' => isset($data['is_quota']) && $data['is_quota'] ? 1 : 0,
            'content' => $data['content'],
            'is_extend_effect' => isset($data['is_extend_effect']) && $data['is_extend_effect'] ? 1 : 0,
            'is_user_level_effect' => isset($data['is_extend_effect']) && $data['is_extend_effect'] ? 1 : 0,
            'identity_auth' => $this->getIdentityAuth($data),
            'education_auth' => $this->getEducationAuth($data),
            'relation_info' => $this->getRelationInfo($data),
            'work_info' => $this->getWorkInfo($data),
            'tongdun_limit_score' => isset($data['tongdun_limit_score']) && $data['tongdun_limit_score'] ? $data['tongdun_limit_score'] : 0,
            'tongdun_limit_minage' => isset($data['tongdun_limit_minage']) && $data['tongdun_limit_minage'] ? $data['tongdun_limit_minage'] : 0,
            'tongdun_limit_maxage' => isset($data['tongdun_limit_maxage']) && $data['tongdun_limit_maxage'] ? $data['tongdun_limit_maxage'] : 0,
            'tongdun_limit_city' => isset($data['tongdun_limit_city']) && $data['tongdun_limit_city'] ? implode(',', $data['tongdun_limit_city']) : '',
            'tongdun_limit_province' => isset($data['tongdun_limit_province']) && $data['tongdun_limit_province'] ? implode(',', $data['tongdun_limit_province']) : '',
            'xuex_chk_status' => isset($data['xuex_chk_status']) && $data['xuex_chk_status'] ? implode(',', $data['tongdun_limit_minage']) : '0,1,2',
            'tongdun_three_month_idno_relevance' => isset($data['tongdun_three_month_idno_relevance']) && $data['tongdun_three_month_idno_relevance'] ? $data['tongdun_limit_minage'] : 0,
            'tongdun_seven_day_apply_num' => isset($data['tongdun_seven_day_apply_num']) && $data['tongdun_seven_day_apply_num'] ? $data['tongdun_seven_day_apply_num'] : 0,
            'tongdun_one_month_apply_num' => isset($data['tongdun_one_month_apply_num']) && $data['tongdun_one_month_apply_num'] ? $data['tongdun_one_month_apply_num'] : 0,
            'tongdun_three_month_apply_num' => isset($data['tongdun_three_month_apply_num']) && $data['tongdun_three_month_apply_num'] ? $data['tongdun_three_month_apply_num'] : 0,
            'zm_point_limit' => isset($data['zm_point_limit']) && $data['zm_point_limit'] ? $data['zm_point_limit'] : 0
        ];

        if (empty($dealLoanType['name']) || empty($dealLoanType['brief'])) {
            $this->msg = "参数错误";
            return null;
        }

        if (isset($_FILES['icon']) && !empty($_FILES['icon'])) {
            $fileUpload = new FileUpload('icon');
            if ($fileUpload->upload()) {
                $dealLoanType['icon'] = $fileUpload->move_uploaded_to . $fileUpload->new_file_name;
            }
        }

        return $dealLoanType;
    }

    private function getDealLoanTypeExtern($data)
    {
        $dealLoanTypeExtern = [
            'city_ids' => $data['city_ids'],
            'start_time' => strtotime($data['start_time']),
            'end_time' => strtotime($data['end_time']),
            'min_deadline' => $data['min_deadline'] ? $data['min_deadline'] : 0,
            'deadline' => $data['deadline'] ? $data['deadline'] : 0,
            'is_recommend' => isset($data['is_recommend']) && $data['is_recommend'] ? 1 : 0,
            'seo_title' => $data['seo_title'],
            'seo_keyword' => $data['seo_keyword'],
            'seo_description' => $data['seo_description'],
            'guarantees_amt' => $data['guarantees_amt'],
            'guarantor_amt' => $data['guarantor_amt'],
            'guarantor_pro_fit_amt' => $data['guarantor_pro_fit_amt'],
            'manage_fee' => $data['manage_fee'],
            'user_loan_manage_fee' => $data['user_loan_manage_fee'],
            'manage_impose_fee_day1' => $data['manage_impose_fee_day1'],
            'manage_impose_fee_day2' => $data['manage_impose_fee_day2'],
            'impose_fee_day1' => $data['impose_fee_day1'],
            'impose_fee_day2' => $data['impose_fee_day2'],
            'minimum' => $data['minimum'],
            'maximum' => $data['maximum'],
            'user_load_transfer_fee' => $data['user_load_transfer_fee'],
            'compensate_fee' => $data['compensate_fee'],
            'user_bid_rebate' => $data['user_bid_rebate'],
            'min_loan_money' => $data['min_loan_money'],
            'max_loan_money' => $data['max_loan_money'],
            'limit_loan_money' => $data['limit_loan_money'],
            'limit_bid_money' => $data['limit_bid_money'],
            'loan_limit_time' => $data['loan_limit_time'],
            'generation_position' => $data['generation_position'],
            'uloadtype' => $data['uloadtype'],
            'portion' => $data['portion'],
            'max_portion' => $data['max_portion']
        ];

        if (isset($_FILES['banner']) && !empty($_FILES['banner'])) {
            $fileUpload = new FileUpload('banner');
            if ($fileUpload->upload()) {
                $dealLoanType['banner'] = $fileUpload->move_uploaded_to . $fileUpload->new_file_name;
            }
        }

        return $dealLoanTypeExtern;
    }

    public function insert($data)
    {
        $dealLoanType = $this->getDealLoanType($data);
        if ($dealLoanType == null) {
            return false;
        }

        $dealLoanTypeExtern = null;
        if ($dealLoanType['is_extend_effect']) {
            $dealLoanTypeExtern = $this->getDealLoanTypeExtern($data);
        }

        $dealLoanTypeDao = \Core::dao('loan_dealloantype');
        $lastInsertId = $dealLoanTypeDao->insert($dealLoanType);
        if ($lastInsertId && $dealLoanTypeExtern) {
            $dealLoanTypeExtern['loan_type_id'] = $lastInsertId;
            $externDao = \Core::dao('dealloantypeextern');
            $externDao->insert($dealLoanTypeExtern);

            return true;
        } else {
            $this->msg = "数据写入失败";
            return false;
        }
    }

    public function update($data)
    {
        $dealLoanType = $this->getDealLoanType($data);
        if ($dealLoanType == null) {
            return false;
        }

        $dealLoanTypeExtern = null;
        if ($dealLoanType['is_extend_effect']) {
            $dealLoanTypeExtern = $this->getDealLoanTypeExtern($data);
        }

        $dealLoanTypeDao = \Core::dao('loan_dealloantype');
        $effectRowNum = $dealLoanTypeDao->update($dealLoanType, ['id' => $dealLoanType['id']]);
        if ($effectRowNum && $dealLoanTypeExtern) {
            $externDao = \Core::dao('dealloantypeextern');
            if ($externDao->exists($dealLoanType['id'])) {
                $externDao->update($dealLoanTypeExtern, ['loan_type_id' => $dealLoanType['id']]);
            } else {
                $externDao->insert($dealLoanTypeExtern);
            }

            return true;
        } else {
            $this->msg = "数据写入失败";
            return false;
        }
    }

    /**
     * 获取身份认证信息
     * @param $data
     * @return string
     */
    private function getIdentityAuth($data)
    {
        $result = [
            'id_is_effect' => isset($data['id_is_effect']) && $data['id_is_effect'] ? 1 : 0,
            'idcard_name' => isset($data['idcard_name']) && $data['idcard_name'] ? 1 : 0,
            'idcard_name_norequired' => isset($data['idcard_name_norequired']) && $data['idcard_name_norequired'] ? 1 : 0,
            'idcard_number' => isset($data['idcard_number']) && $data['idcard_number'] ? 1 : 0,
            'idcard_number_norequired' => isset($data['idcard_number_norequired']) && $data['idcard_number_norequired'] ? 1 : 0,
            'idcard_front' => isset($data['idcard_front']) && $data['idcard_front'] ? 1 : 0,
            'idcard_front_norequired' => isset($data['idcard_front_norequired']) && $data['idcard_front_norequired'] ? 1 : 0,
            'home_addr' => isset($data['home_addr']) && $data['home_addr'] ? 1 : 0,
            'home_addr_norequired' => isset($data['home_addr_norequired']) && $data['home_addr_norequired'] ? 1 : 0,
        ];

        return json_encode($result);
    }

    /**
     * 获取教育认证信息
     * @param $data
     * @return string
     */
    private function getEducationAuth($data)
    {
        $result = [
            'edu_is_effect' => isset($data['edu_is_effect']) && $data['edu_is_effect'] ? 1 : 0,
            'hs_info' => isset($data['hs_info']) && $data['hs_info'] ? 1 : 0,
            'hs_info_norequired' => isset($data['hs_info_norequired']) && $data['hs_info_norequired'] ? 1 : 0,
            'college_info' => isset($data['college_info']) && $data['college_info'] ? 1 : 0,
            'college_info_norequired' => isset($data['college_info_norequired']) && $data['college_info_norequired'] ? 1 : 0,
            'xx_info' => isset($data['xx_info']) && $data['xx_info'] ? 1 : 0,
            'xx_info_norequired' => isset($data['xx_info_norequired']) && $data['xx_info_norequired'] ? 1 : 0,
            'jw_info' => isset($data['jw_info']) && $data['jw_info'] ? 1 : 0,
            'jw_info_norequired' => isset($data['jw_info_norequired']) && $data['jw_info_norequired'] ? 1 : 0,
            'tb_info' => isset($data['tb_info']) && $data['tb_info'] ? 1 : 0,
            'tb_info_norequired' => isset($data['tb_info_norequired']) && $data['tb_info_norequired'] ? 1 : 0,
            'notice_info' => isset($data['notice_info']) && $data['notice_info'] ? 1 : 0,
            'notice_info_norequired' => isset($data['notice_info_norequired']) && $data['notice_info_norequired'] ? 1 : 0,
            'studentIdCard_info' => isset($data['studentIdCard_info']) && $data['studentIdCard_info'] ? 1 : 0,
            'studentIdCard_info_norequired' => isset($data['studentIdCard_info_norequired']) && $data['studentIdCard_info_norequired'] ? 1 : 0,
            'campus_card_info' => isset($data['campus_card_info']) && $data['campus_card_info'] ? 1 : 0,
            'campus_card_info_norequired' => isset($data['campus_card_info_norequired']) && $data['campus_card_info_norequired'] ? 1 : 0
        ];
        return json_encode($result);
    }

    /**
     * 获取联系人信息
     * @param $data
     * @return string
     */
    private function getRelationInfo($data)
    {
        $result = [
            'contact_is_effect' => isset($data['contact_is_effect']) && $data['contact_is_effect'] ? 1 : 0,
            'contact_qq' => isset($data['contact_qq']) && $data['contact_qq'] ? 1 : 0,
            'contact_qq_norequired' => isset($data['contact_qq_norequired']) && $data['contact_qq_norequired'] ? 1 : 0,
            'contact_wx' => isset($data['contact_wx']) && $data['contact_wx'] ? 1 : 0,
            'contact_wx_norequired' => isset($data['contact_wx_norequired']) && $data['contact_wx_norequired'] ? 1 : 0,
            'emergency_contact' => isset($data['emergency_contact']) && $data['emergency_contact'] ? 1 : 0
        ];

        $contactList = [];
        if (isset($data['contact_arr'])) {
            foreach ($data['contact_arr'] as $key => $contactName) {
                if (empty($contactName)) {
                    continue;
                }

                $contact = [
                    'contact' => $contactName,
                    'company' => isset($data['company_arr'][$key]) && $data['company_arr'][$key] ? 1 : 0,
                    'contact_norequired' => isset($data['contact_norequired_arr'][$key]) && $data['contact_norequired_arr'][$key] ? 1 : 0
                ];
                $contactList[] = $contact;
            }
        }
        $result['contact_info'] = $contactList;

        return json_encode($result);
    }

    /**
     * 获取工作信息
     * @param $data
     * @return string
     */
    private function getWorkInfo($data)
    {
        $result = [
            'work_is_effect' => isset($data['work_is_effect']) && $data['work_is_effect'] ? 1 : 0,
            'company_name' => isset($data['company_name']) && $data['company_name'] ? 1 : 0,
            'company_addr' => isset($data['company_addr']) && $data['company_addr'] ? 1 : 0,
            'company_station' => isset($data['company_station']) && $data['company_station'] ? 1 : 0,
            'company_telephone' => isset($data['company_telephone']) && $data['company_telephone'] ? 1 : 0,
            'industry' => isset($data['industry']) && $data['industry'] ? 1 : 0,
            'income_range' => isset($data['income_range']) && $data['income_range'] ? 1 : 0,
        ];

        return json_encode($result);
    }

}