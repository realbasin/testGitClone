<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_User extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'user_name'//会员名
        , 'short_name'//缩略名
        , 'brief'//担保方介绍
        , 'header'//头部
        , 'company_brief'//company_brief
        , 'history'//发展史
        , 'content'//内容
        , 'sort'//排序
        , 'acct_type'//担保账户类型(0:机构，1:个人)
        , 'user_pwd'//会员密码
        , 'invest_gesturepassword'//理财端打开app手势密码
        , 'create_time'//注册时间
        , 'update_time'//修改时间
        , 'login_ip'//最后登录IP
        , 'group_id'//会员组ID
        , 'is_effect'//是否被禁用（未验证）
        , 'is_delete'//删除
        , 'email'//会员邮件
        , 'idno'//身份证号
        , 'idcardpassed'//身份证是否审核通过
        , 'idcardpassed_time'//通过时间
        , 'real_name'//真实姓名
        , 'mobile'//会员手机号
        , 'mobilepassed'//手机是否验证通过
        , 'professionalpassed'//职称是否审核通过
        , 'score'//积分
        , 'money'//money
        , 'quota'//额度
        , 'credit_quota'//授信额度
        , 'lock_money'//冻结资金
        , 'verify'//验证码
        , 'code'//登录用的标识码
        , 'pid'//推荐人ID
        , 'referer_memo'//邀请备注
        , 'login_time'//最后登录时间
        , 'referral_count'// 返利数量
        , 'referral_time'//返利期限
        , 'password_verify'//取回密码的验证码
        , 'integrate_id'//会员整合的用户ID（如uc中的会员ID）
        , 'sina_id'//新浪同步的会员ID
        , 'renren_id'//预留
        , 'kaixin_id'//预留
        , 'sohu_id'//预留
        , 'bind_verify'//绑定验证码
        , 'verify_create_time'//绑定验证码发送时间
        , 'tencent_id'//腾讯微博ID
        , 'referer'//会员来路
        , 'login_pay_time'//弃用
        , 'focus_count'//关注别人的数量
        , 'focused_count'//粉丝数
        , 'n_province_id'//户籍-省
        , 'n_city_id'//户籍-市
        , 'province_id'//户口-省
        , 'city_id'//户口-市
        , 'region_lv1'//现居住地-中国
        , 'region_lv2'//现居住地-省
        , 'region_lv3'//现居住地-市
        , 'region_lv4'//现居住地-区
        , 'sex'//性别 0女 1 男
        , 'step'// 新手已完成步骤
        , 'byear'//出生年
        , 'bmonth'//出生月
        , 'bday'//出生日
        , 'graduation'//学历
        , 'graduatedyear'//入学年份
        , 'university'//毕业院校
        , 'edu_validcode'//学历认证码
        , 'has_send_video'//是否已经上传视频
        , 'marriage'//婚姻状况
        , 'haschild'//有子女 0无 1有
        , 'hashouse'//是否有房 0无 1有
        , 'houseloan'//是否又房贷
        , 'hascar'//是否有车
        , 'carloan'//是否又车贷
        , 'car_brand'//汽车品牌
        , 'car_year'//购车时间
        , 'car_number'//汽车数量
        , 'address'//住址
        , 'phone'//电话
        , 'postcode'//邮编
        , 'locate_time'//用户最后登陆时间
        , 'xpoint'//用户最后登陆x座标
        , 'ypoint'//用户最后登陆y座标
        , 'topic_count'//主题数
        , 'fav_count'//喜欢数
        , 'faved_count'//被喜欢数
        , 'insite_count'//弃用
        , 'outsite_count'//弃用
        , 'level_id'//等级ID
        , 'point'//经验值
        , 'sina_app_key'//新浪的同步验证key
        , 'sina_app_secret'//新浪的同步验证密码
        , 'is_syn_sina'//是否同步发微博到新浪
        , 'tencent_app_key'//腾讯的同步验证key
        , 'tencent_app_secret'//腾讯的同步验证密码
        , 'is_syn_tencent'//是否同步发微博到腾讯
        , 't_access_token'//腾讯微博授权码
        , 't_openkey'//腾讯微博的openkey
        , 't_openid'//腾讯微博OPENID
        , 'sina_token'//新浪的授权码
        , 'is_borrow_out'//是否是投标者
        , 'is_borrow_in'//是否融资人
        , 'creditpassed'//信用认证是否通过
        , 'creditpassed_time'//信用认证通过时间
        , 'workpassed'//工作认证是否通过
        , 'workpassed_time'//工作认证通过时间
        , 'incomepassed'//收入认证是否通过
        , 'incomepassed_time'//收入认证通过时间
        , 'housepassed'//房产认证是否通过
        , 'housepassed_time'//房产认证通过时间
        , 'carpassed'//汽车认证是否通过
        , 'carpassed_time'//汽车认证通过时间
        , 'marrypassed'//结婚认证是否通过
        , 'marrypassed_time'//结婚认证通过时间
        , 'edupassed'//教育认证是否通过
        , 'edupassed_time'//教育认证通过时间
        , 'skillpassed'//技术职称是否通过
        , 'skillpassed_time'//技术职称认证通过时间
        , 'videopassed'//视频认证是否通
        , 'videopassed_time'//视频认证通过时间
        , 'mobiletruepassed'//手机实名认证是否通过
        , 'mobiletruepassed_time'//手机实名认证认证通过时间
        , 'residencepassed'//residencepassed
        , 'residencepassed_time'//residencepassed_time
        , 'alipay_id'//alipay_id
        , 'qq_id'//qq_id
        , 'info_down'//资料下载地址
        , 'sealpassed'//电子印章是否通过
        , 'paypassword'//支付密码
        , 'n_paypassword'//6位数字支付密码
        , 'apns_code'//推送设备号
        , 'emailpassed'//emailpassed
        , 'tmp_email'//tmp_email
        , 'view_info'//view_info
        , 'ips_acct_no'//pIpsAcctNo 30 IPS托管平台账 户号
        , 'referral_rate'//返利抽成比
        , 'user_type'//用户类型: 0普通用户,1企业用户,2企业用户子类型,3校园行长,4普惠用户
        , 'create_date'//记录注册日期，方便统计使用
        , 'register_ip'//注册IP
        , 'admin_id'//所属管理员
        , 'first_audit_admin_id'//初审人id
        , 'claim_time'//认领时间
        , 'customer_id'//所属客服
        , 'is_black'//是否黑名单
        , 'is_esign'//e签宝是否认证:0-待认证；1-已认证；2-认证失败
        , 'is_opened_fuiou'//开通富友托管:0-否;1-是
        , 'is_trans_fuiou'//余额转账：0-未转；1-已转
        , 'is_warn'//是否警告名单 1是，2否
        , 'is_show_warn'//是否展示警告名单 1是，2否
        , 'is_second_apply'//审核失败后能否再次申请：0-不能；1-能
        , 'is_norepaid_apply'//未还清能否续借：0-不能；1-能
        , 'is_multy_borrow'//是否允许多借:0否,1是
        , 'is_warn_update_time'//警告名单修改时间
        , 'warn_x'//警告X月
        , 'is_xiaoshu_black'//是否小树黑名单 1是，2否
        , 'is_show_xiaoshu_black'//是否展示小树黑名单 1是，2否
        , 'is_xiaoshu_black_update_time'//小树黑名单修改时间
        , 'xiaoshu_black_x'//小树黑名单X月
        , 'vip_id'//VIP等级id
        , 'vip_state'//VIP状态 0关闭 1开启
        , 'nmc_amount'//不可提现金额
        , 'ips_mer_code'//由IPS颁发的商户号 acct_type = 0
        , 'enterpriseName'//企业名称
        , 'bankLicense'//开户银行许可证
        , 'orgNo'//组织机构代码
        , 'businessLicense'//营业执照编号
        , 'taxNo'//税务登记号
        , 'u_year'//入学年份
        , 'u_special'//专业
        , 'u_alipay'//支付宝账号
        , 'email_encrypt'//邮箱
        , 'real_name_encrypt'//真实姓名
        , 'idno_encrypt'//身份证号
        , 'mobile_encrypt'//手机号
        , 'money_encrypt'//账户余额
        , 'wx_openid'//微信openid
        , 'zm_openid'//芝麻信用openid
        , 'zm_points'//zm_points
        , 'zm_ivs'//zm_ivs
        , 'zm_watchlist'//zm_watchlist
        , 'total_invite_borrow_money'//累计被邀请人员的借款金额;
        , 'total_invite_invest_money'//累计被邀请人员的投资金额;
        , 'vip_end_time'//VIP结束时间
        , 'xuex_account'//学信网账号
        , 'xuex_pwd'//学信网密码
        , 'xuex_chk_status'//验证学校网账号、密码正确否(0,未验证,1,正确,2,错误)
        , 'redupassed'//在校认证是否通过
        , 'redupassed_time'//在校认证通过时间
        , 'contacts'//联系人
        , 'contactspassed'//联系人认证是否通过
        , 'contactspassed_time'//联系人认证通过时间
        , 'is_campus_leader'//是否校园行长
        , 'head_pic_file'//用户头像
        , 'enrollment_time'//入学时间
        , 'graduate_time'//毕业时间
        , 'faculty'//二级学院
        , 'rpid'//城市合伙人ID
        , 'mobile_credit_submited'//运营商认证信息是否已提交
        , 'mobile_credit_passed'//手机是否通过运营商认证
        , 'finally_credit_passed'//用户整体认证: -1末填写,0待审核,1审核通过,2审核失败
        , 'user_mark'//用户标记: 0未确定,1借款用户,2理财用户
        , 'has_loan'//是否参与了投资或借款,如果是,则不能再进行身份转换
        , 'bonus_tips'//是否查看优惠券提示信息，0:没查看，1:已查看
        , 'work_info'//工作信息
        , 'education_info'//教务信息
        , 'channel_s'//用户渠道来源
        , 'platform_code'//platform_code
        , 'platform_user_id'//广告平台用户id
        , 'register_port'//注册端
        , 'sor_code'//来源号
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'user';
    }

    /**
     * 获取校园行长用户列表
     */
    public function getBankerList()
    {
        $where = array('user_type' => 3, 'is_delete' => 0);
        return $this->getDb()->select('*')->from($this->getTable())->where($where)->execute()->rows();
    }

    public function usersNew($ids, $start, $end)
    {
        if (!is_array($ids) || count($ids) == 0)
            return 0;

        $where['id'] = $ids;

        if ($start != '') {
            $where['create_time >='] = $start;
        }
        if ($end != '') {
            $where['create_time <'] = $end;
        }

        return $this->getDb()->select('count(id) CNT')->from($this->getTable())->where($where)->execute()->value('CNT');
    }

    public function getUserById($id)
    {
        if ($id < 1)
            return null;

        $where = array('id' => $id, 'is_delete' => 0);

        $fields = "*,AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "') mobile_encrypt,
        AES_DECRYPT(email_encrypt,'" . AES_DECRYPT_KEY . "') email_encrypt,
        AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') real_name_encrypt,
        AES_DECRYPT(money_encrypt,'" . AES_DECRYPT_KEY . "') money_encrypt,
        AES_DECRYPT(idno_encrypt,'" . AES_DECRYPT_KEY . "') idno_encrypt";
        return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->row();
    }

    /**
     * 获取用户加密信息
     * @param $user_id
     * @return array|mixed|null
     */
    public function getUserEncryptInfoById($user_id)
    {
        if ($user_id < 1)
            return null;

        $where = ['id' => $user_id, 'is_delete' => 0];

        $fields = "AES_DECRYPT(mobile_encrypt,'" . AES_DECRYPT_KEY . "') mobile,
        AES_DECRYPT(email_encrypt,'" . AES_DECRYPT_KEY . "') email,
        AES_DECRYPT(real_name_encrypt,'" . AES_DECRYPT_KEY . "') real_name,
        AES_DECRYPT(money_encrypt,'" . AES_DECRYPT_KEY . "') money,
        AES_DECRYPT(idno_encrypt,'" . AES_DECRYPT_KEY . "') idno";
        return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->row();
    }

    public function getUsersHasPidCount()
    {
        return $this->getDb()
            ->select('COUNT(*) AS num')
            ->from($this->getTable())
            ->where(['pid>' => 0, 'is_delete' => 0])
            ->execute()
            ->value('num');
    }

    public function getUsersHasPid($start, $size)
    {
        $userList = $this->getDb()
            ->select('id,pid')
            ->from($this->getTable())
            ->where(['pid>' => 0, 'is_delete' => 0])
            ->limit($start, $size)
            ->execute()
            ->rows();

        $result = [];
        foreach ($userList as $item) {
            $result[] = ['id' => $item['id'], 'pid' => $item['pid']];
        }

        return $result;
    }

    /**
     * 返回指定时间注册的用户
     * @param int $starttime 起始时间
     * @param int $endtime 结束时间
     * @param int $user_mark -1全部用户,0未确定,1借款用户,2理财用户
     * @return array;
     */
    public function getUserIdsByTimes($starttime, $endtime, $user_mark = -1)
    {
        if ($starttime < 0 || $endtime < 0 || $starttime > $endtime) {
            return array();
        }

        $where = ['create_time >=' => $starttime, 'create_time <=' => $endtime];

        if (in_array($user_mark, array('0', '1', '2'))) {
            $where['user_mark'] = $user_mark;
        } elseif ($user_mark != -1) {
            return array();
        }

        $userList = $this->getDb()
            ->select('id')
            ->from($this->getTable())
            ->where($where)
            ->execute()
            ->rows();

        $result = [];
        foreach ($userList as $user) {
            $result[] = $user['id'];
        }
        return $result;
    }

    /**
     * 获取借款用户中已进行芝麻信用授权的用户
     * @return mixed
     */
    public function getUserZmOpenIdList()
    {
        $userList = $this->getDb()
            ->select('id,zm_openid')
            ->from($this->getTable())
            ->where(['zm_openid <>' => ''])
            ->execute()
            ->rows();

        return $userList;
    }

}
