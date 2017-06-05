<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_user extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_name'//会员名
				,'short_name'//缩略名
				,'brief'//担保方介绍
				,'header'//头部\n
				,'company_brief'//company_brief
				,'history'//发展史
				,'content'//内容
				,'sort'//排序
				,'acct_type'//担保账户类型(0:机构，1:个人)
				,'user_pwd'//会员密码
				,'invest_gesturepassword'//理财端打开app手势密码
				,'create_time'//注册时间
				,'update_time'//修改时间
				,'login_ip'//最后登录IP
				,'group_id'//会员组ID
				,'is_effect'//是否被禁用（未验证）
				,'is_delete'//删除
				,'email'//会员邮件
				,'idno'//身份证号
				,'idcardpassed'//身份证是否审核通过
				,'idcardpassed_time'//通过时间
				,'real_name'//真实姓名
				,'mobile'//会员手机号
				,'mobilepassed'//手机是否验证通过
				,'score'//积分
				,'money'//money
				,'quota'//额度
				,'credit_quota'//授信额度
				,'lock_money'//冻结资金
				,'verify'//验证码
				,'code'//登录用的标识码
				,'pid'//推荐人ID
				,'referer_memo'//邀请备注
				,'login_time'//最后登录时间
				,'referral_count'// 返利数量
				,'referral_time'//返利期限
				,'password_verify'//取回密码的验证码
				,'integrate_id'//会员整合的用户ID（如uc中的会员ID）
				,'sina_id'//新浪同步的会员ID
				,'renren_id'//预留
				,'kaixin_id'//预留
				,'sohu_id'//预留
				,'bind_verify'//绑定验证码
				,'verify_create_time'//绑定验证码发送时间
				,'tencent_id'//腾讯微博ID
				,'referer'//会员来路
				,'login_pay_time'//弃用
				,'focus_count'//关注别人的数量
				,'focused_count'//粉丝数
				,'n_province_id'//户籍-省
				,'n_city_id'//户籍-市
				,'province_id'//户口-省
				,'city_id'//户口-市
				,'region_lv1'//现居住地-中国
				,'region_lv2'//现居住地-省
				,'region_lv3'//现居住地-市
				,'region_lv4'//现居住地-区
				,'sex'//性别 0女 1 男
				,'step'// 新手已完成步骤
				,'byear'//出生年
				,'bmonth'//出生月
				,'bday'//出生日
				,'graduation'//学历
				,'graduatedyear'//入学年份
				,'university'//毕业院校
				,'edu_validcode'//学历认证码
				,'has_send_video'//是否已经上传视频
				,'marriage'//婚姻状况
				,'haschild'//有子女 0无 1有
				,'hashouse'//是否有房 0无 1有
				,'houseloan'//是否又房贷
				,'hascar'//是否有车
				,'carloan'//是否又车贷
				,'car_brand'//汽车品牌
				,'car_year'//购车时间
				,'car_number'//汽车数量
				,'address'//住址
				,'phone'//电话
				,'postcode'//邮编
				,'locate_time'//用户最后登陆时间
				,'xpoint'//用户最后登陆x座标
				,'ypoint'//用户最后登陆y座标
				,'topic_count'//主题数
				,'fav_count'//喜欢数
				,'faved_count'//被喜欢数
				,'insite_count'//弃用
				,'outsite_count'//弃用
				,'level_id'//等级ID
				,'point'//经验值
				,'sina_app_key'//新浪的同步验证key
				,'sina_app_secret'//新浪的同步验证密码
				,'is_syn_sina'//是否同步发微博到新浪
				,'tencent_app_key'//腾讯的同步验证key
				,'tencent_app_secret'//腾讯的同步验证密码
				,'is_syn_tencent'//是否同步发微博到腾讯
				,'t_access_token'//腾讯微博授权码
				,'t_openkey'//腾讯微博的openkey
				,'t_openid'//腾讯微博OPENID
				,'sina_token'//新浪的授权码
				,'is_borrow_out'//是否是投标者
				,'is_borrow_in'//是否融资人
				,'creditpassed'//信用认证是否通过
				,'creditpassed_time'//信用认证通过时间
				,'workpassed'//工作认证是否通过
				,'workpassed_time'//工作认证通过时间
				,'incomepassed'//收入认证是否通过
				,'incomepassed_time'//收入认证通过时间
				,'housepassed'//房产认证是否通过
				,'housepassed_time'//房产认证通过时间
				,'carpassed'//汽车认证是否通过
				,'carpassed_time'//汽车认证通过时间
				,'marrypassed'//结婚认证是否通过
				,'marrypassed_time'//结婚认证通过时间
				,'edupassed'//教育认证是否通过
				,'edupassed_time'//教育认证通过时间
				,'skillpassed'//技术职称是否通过
				,'skillpassed_time'//技术职称认证通过时间
				,'videopassed'//视频认证是否通
				,'videopassed_time'//视频认证通过时间
				,'mobiletruepassed'//手机实名认证是否通过
				,'mobiletruepassed_time'//手机实名认证认证通过时间
				,'residencepassed'//residencepassed
				,'residencepassed_time'//residencepassed_time
				,'alipay_id'//alipay_id
				,'qq_id'//qq_id
				,'info_down'//资料下载地址
				,'sealpassed'//电子印章是否通过
				,'paypassword'//支付密码
				,'n_paypassword'//6位数字支付密码
				,'apns_code'//推送设备号
				,'emailpassed'//emailpassed
				,'tmp_email'//tmp_email
				,'view_info'//view_info
				,'ips_acct_no'//pIpsAcctNo 30 IPS托管平台账 户号
				,'referral_rate'//返利抽成比
				,'user_type'//用户类型 0普通用户 1 企业用户 3行长 4普惠用户
				,'create_date'//记录注册日期，方便统计使用
				,'register_ip'//注册IP
				,'admin_id'//所属管理员
				,'customer_id'//所属客服
				,'is_black'//是否黑名单
				,'is_esign'//e签宝是否认证:0-待认证；1-已认证；2-认证失败
				,'is_opened_fuiou'//开通富友托管:0-否;1-是
				,'is_trans_fuiou'//余额转账：0-未转；1-已转
				,'is_warn'//是否警告名单 1是，2否
				,'is_show_warn'//是否展示警告名单 1是，2否
				,'is_second_apply'//审核失败后能否再次申请：0-不能；1-能
				,'is_norepaid_apply'//未还清能否续借：0-不能；1-能
				,'is_multy_borrow'//是否允许多借:0否,1是
				,'is_warn_update_time'//警告名单修改时间
				,'warn_x'//警告X月
				,'is_xiaoshu_black'//是否小树黑名单 1是，2否
				,'is_show_xiaoshu_black'//是否展示小树黑名单 1是，2否
				,'is_xiaoshu_black_update_time'//小树黑名单修改时间
				,'xiaoshu_black_x'//小树黑名单X月
				,'vip_id'//VIP等级id
				,'vip_state'//VIP状态 0关闭 1开启
				,'nmc_amount'//不可提现金额
				,'ips_mer_code'//由IPS颁发的商户号 acct_type = 0
				,'enterpriseName'//企业名称
				,'bankLicense'//开户银行许可证
				,'orgNo'//组织机构代码
				,'businessLicense'//营业执照编号
				,'taxNo'//税务登记号
				,'u_year'//入学年份
				,'u_special'//专业
				,'u_alipay'//支付宝账号
				,'email_encrypt'//邮箱
				,'real_name_encrypt'//真实姓名
				,'idno_encrypt'//身份证号
				,'mobile_encrypt'//手机号
				,'money_encrypt'//账户余额
				,'wx_openid'//微信openid
				,'zm_openid'//芝麻信用openid
				,'zm_points'//zm_points
				,'zm_ivs'//zm_ivs
				,'zm_watchlist'//zm_watchlist
				,'total_invite_borrow_money'//累计被邀请人员的借款金额;
				,'total_invite_invest_money'//累计被邀请人员的投资金额;
				,'vip_end_time'//VIP结束时间
				,'xuex_account'//学信网账号
				,'xuex_pwd'//学信网密码
				,'xuex_chk_status'//验证学校网账号、密码正确否(0,未验证,1,正确,2,错误)
				,'redupassed'//在校认证是否通过
				,'redupassed_time'//在校认证通过时间
				,'contacts'//联系人
				,'contactspassed'//联系人认证是否通过
				,'contactspassed_time'//联系人认证通过时间
				,'is_campus_leader'//是否校园行长
				,'head_pic_file'//用户头像
				,'enrollment_time'//入学时间
				,'graduate_time'//毕业时间
				,'faculty'//二级学院
				,'rpid'//城市合伙人ID
				,'mobile_credit_submited'//运营商认证信息是否已提交
				,'mobile_credit_passed'//手机是否通过运营商认证
				,'user_mark'//用户标记，用来区分借款和投资用户，0:未确定，1:借款用户，2:投资用户
				,'has_loan'//是否参与了投资或借款,如果是,则不能再进行身份转换
				,'bonus_tips'//是否查看优惠券提示信息，0:没查看，1:已查看
				,'work_info'//工作信息
				,'education_info'//教务信息
				,'channel_s'//用户渠道来源
				,'platform_code'//platform_code
				,'platform_user_id'//广告平台用户id
				,'register_port'//注册端
				,'sor_code'//来源号
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user';
	}
	/*
	 * 获取用户
	 * @userId number or array
	 * @return array
	 */
	public function getUser($userId,$field){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('id'=>$userId))->execute()->key('id')->rows();
	}
	
	/*
	 * 通过用户名模糊获取用户
	 * @userName 用户名
	 * @field 要查询的字段
	 * @limit 限制取多少条，默认20条
	 */
	public function getUsersByName($userName,$field='id,user_name,real_name',$limit=10){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('user_name like'=>'%'.$userName.'%'))->limit(0,$limit)->execute()->rows();
	}
	/*
	 * 通过用户名模糊获取用户ID
	 * @userName 用户名
	 * @field ID
	 * @limit 限制取多少条，默认20条
	 */
	/*public function getUsersIdsByName($userName,$field='id',$limit=20){
		return $this->getDb()->from($this->getTable())->where(array("AES_DECRYPT(real_name_encrypt,'__FANWEP2P__') like"=>'%'.$userName.'%'))->limit(0,$limit)->execute()->values($field);
	}*/
	/*
	 * 通过用户手机号模糊获取用户ID
	 * @userName 用户名
	 * @field ID
	 * @limit 限制取多少条，默认20条
	 */
	/*public function getUsersIdsByMobile($userMobile,$field='id',$limit=20){
		return $this->getDb()->from($this->getTable())->where(array("AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') like"=>'%'.$userMobile.'%'))->limit(0,$limit)->execute()->values($field);
	}*/
	//add by zlz 201705191123 通过条件拼接
	/*
	 * 通过用户手机号he姓名组合模糊获取用户ID
	 * @where 条件
	 * @field ID
	 * @limit 限制取多少条，默认20条
	 */
	public function getUsersIdsByMobileAndName($where,$field='id',$limit=20){
		return $this->getDb()->from($this->getTable())->where($where)->limit(0,$limit)->execute()->values($field);
	}

	//统计获得账户余额
	//缓存10分钟
	public function getStatBalanceTotal(){
		return $this->getDb()->select('SUM(AES_DECRYPT(money_encrypt,\''.AES_DECRYPT_KEY.'\')) as balancetotal')->from($this->getTable())->cache(C('stat_sql_cache_time'),'stat_user_balance_total')->execute()->value('balancetotal');
	}
	//获取用户余额
	public function getUserMoney($user_id){
		return $this->getDb()->select('AES_DECRYPT(money_encrypt,\''.AES_DECRYPT_KEY.'\') as aesmoney')->from($this->getTable())->where(array('id'=>$user_id))->execute()->value('aesmoney');
	}
	
	//获取用户注册统计
	public function getStatUserRegist($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,count(id) as usercount")->from($this->getTable())->where(array('create_time >='=>$startDate,'create_time <='=>$endDate))->groupBy('createdate')->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate)->execute()->rows();
	}
	//add by zlz 201706011544
	//通过id获取用户名
	public function getUserNameById($id){
		return $this->getDb()->from($this->getTable())->where(array('id'=>$id))->execute()->value('user_name');
	}
	//通过id获取用户类型
	public function getUserMarkById($id){
		return $this->getDb()->from($this->getTable())->where(array('id'=>$id))->execute()->value('user_mark');
	}
	//获取用户冻结资金余额
	public function getUserLockMoneyById($user_id){
		return $this->getDb()->from($this->getTable())->where(array('id'=>$user_id))->execute()->value('lock_money');
	}
	//获取用户当前积分
	public function getUserScoreById($user_id){
		return $this->getDb()->from($this->getTable())->where(array('id'=>$user_id))->execute()->value('score');
	}
}
