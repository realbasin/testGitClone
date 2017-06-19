<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_Deal extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'red_package_id'//red_package_id
        , 'name'//借款名称
        , 'sub_name'//缩略么
        , 'cate_id'//借款分类
        , 'agency_id'//担保机构（标识ID）
        , 'agency_status'//应邀状态 0 应邀 1邀约中 2 拒绝
        , 'user_id'//借款人（标识ID）
        , 'description'//简介
        , 'is_effect'// 有效性控制
        , 'is_referral_award'//是否纳入推荐奖励 0否 1是
        , 'is_delete'//删除标识
        , 'sort'//排序  大->小
        , 'type_id'//借款用途（标识id）
        , 'fund_type'//资金源类型
        , 'icon_type'//0自己上传，2用户头像，3类型图
        , 'icon'//贷款缩略图
        , 'seo_title'//SEO标题
        , 'seo_keyword'//SEO关键词
        , 'seo_description'//SEO说明
        , 'is_hot'//是否热门
        , 'is_new'//是否最新
        , 'is_best'//是否最佳
        , 'borrow_amount'//借款总额
        , 'apply_borrow_amount'//借款申请金额
        , 'apart_borrow_amount'//拆标后的原借款标金额
        , 'min_loan_money'//最底投标额度
        , 'max_loan_money'//最高投标额度
        , 'repay_time'//借款期限
        , 'rate'//年利率
        , 'day'//招标时间
        , 'create_time'//添加时间
        , 'update_time'//更新时间
        , 'name_match'//name_match
        , 'name_match_row'//name_match_row
        , 'deal_cate_match'//deal_cate_match
        , 'deal_cate_match_row'//deal_cate_match_row
        , 'tag_match'//tag_match
        , 'tag_match_row'//tag_match_row
        , 'type_match'//type_match
        , 'type_match_row'//type_match_row
        , 'is_recommend'//是否推荐
        , 'buy_count'//投标人数
        , 'load_money'//已投标多少
        , 'repay_money'//还了多少！
        , 'start_time'//开始招标日期
        , 'bid_start_time'//上标时间（正式开始招标时间）
        , 'claim_time'//认领时间
        , 'first_audit_time'//初审通过时间
        , 'second_audit_time'//复审通过时间
        , 'success_time'//成功日期
        , 'repay_start_time'//开始还款日
        , 'last_repay_time'//最后一次还款日
        , 'next_repay_time'//下次还款日
        , 'loan_time'//管理员放款时间
        , 'bad_time'//流标时间
        , 'payoff_time'//还清时间
        , 'deal_status'//0待等材料，1进行中，2满标，3流标，4还款中，5已还清
        , 'pay_off_status'//投资人回款状态：0-未完毕；1-完毕
        , 'loan_status'//0未放款,1已放款
        , 'b_status'//续借状态：0:首单,1续借
        , 'enddate'//筹标期限
        , 'voffice'//是否显示公司名称
        , 'vposition'//是否显示职位
        , 'services_fee'//服务费率
        , 'publish_wait'//是否发布 1：待发布 0已发布, 2初审通过，3复审失败
        , 'is_send_bad_msg'//是否已发送流标通知
        , 'is_send_success_msg'//是否已经发送成功通知
        , 'bad_msg'//流标通知内容
        , 'send_half_msg_time'//发送投标过半的通知时间
        , 'send_three_msg_time'//发送三天内需还款的通知时间
        , 'is_send_half_msg'//是否已发送招标过半通知
        , 'is_has_loans'//是否已经放款给招标人
        , 'loantype'//还款方式  0:等额本息 1:付息还本 2:到期本息
        , 'warrant'//担保范围 0:无  1:本金 2:本金及利息
        , 'titlecolor'//标题颜色
        , 'is_send_contract'//是否已经发送电子协议书
        , 'repay_time_type'//借款期限类型 0:按天还款  1:按月还款
        , 'risk_rank'//风险等级
        , 'risk_security'//风险保障
        , 'deal_sn'//借款编号
        , 'is_has_received'//流标是否返已还
        , 'manage_fee'//借款者管理费
        , 'user_loan_manage_fee'//投资者管理费
        , 'manage_impose_fee_day1'//普通逾期管理费
        , 'manage_impose_fee_day2'//严重逾期管理费
        , 'impose_fee_day1'//普通逾期费率
        , 'impose_fee_day2'//严重逾期费率
        , 'user_load_transfer_fee'//债权转让管理费
        , 'transfer_day'//满标放款多少天后才可以进行转让 0不限制
        , 'compensate_fee'//提前还款补偿
        , 'ips_do_transfer'//放款处理中
        , 'ips_over'//IPS 是否完成还款 0 未完成 1完成
        , 'delete_msg'//审核失败提示
        , 'delete_real_msg'//审核失败真实原因
        , 'user_bid_rebate'//投资返利%
        , 'guarantees_amt'//借款保证金（冻结借款人的金额，需要提前存钱）
        , 'l_guarantees_amt'//风险保证金，用于本地标
        , 'real_freezen_l_amt'//已冻结保证金，用于本地标
        , 'un_real_freezen_l_amt'//已冻结保证金，用于本地标
        , 'real_freezen_amt'//借款方 实际冻结金额 = 保证金
        , 'un_real_freezen_amt'//已经解冻的担保保证金（借款方）<=real_freezen_amt
        , 'guarantor_amt'//担保方，担保金额(代偿金额累计不能大于担保金额)
        , 'guarantor_margin_amt'//担保方，担保保证金额(需要冻结担保方的金额）
        , 'guarantor_real_freezen_amt'//担保方 实际冻结金额 = 担保保证金额
        , 'un_guarantor_real_freezen_amt'//已经解冻的担保保证金（担保方）<=guarantor_real_freezen_amt
        , 'guarantor_pro_fit_amt'//担保收益
        , 'guarantor_real_fit_amt'//实际担保收益，转帐后更新<=guarantor_pro_fit_amt
        , 'mer_bill_no'//标的登记时提交的订单单号
        , 'ips_bill_no'//由IPS系统生成的唯一流水号
        , 'ips_guarantor_bill_no'//担保编号ips返回的
        , 'mer_guarantor_bill_no'//提交的担保单号
        , 'view_info'//view_info
        , 'generation_position'//申请延期的额度
        , 'uloadtype'//用户投标类型 0按金额，1 按份数
        , 'portion'//分成多少份
        , 'max_portion'//最多买多少份
        , 'start_date'//开始投标时间，日期格式，方便统计
        , 'repay_start_date'//满标放款,支出奖励时间,日期格式,方便统计
        , 'bad_date'//流标时间,日期格式,方便统计
        , 'contract_id'//借款合同模板
        , 'scontract_id'//咨询服务协议
        , 'tcontract_id'//转让合同模板
        , 'is_advance'//是否预告
        , 'score'//借款者获得积分
        , 'user_bid_score_fee'//投资返还积分比率
        , 'user_loan_interest_manage_fee'//投资者利息管理费
        , 'user_loan_early_interest_manage_fee'//投资者提前还款利息管理费
        , 'guarantees_money'//借款保证金
        , 'attachment'//合同附件
        , 'tattachment'//转让合同附件
        , 'publish_memo'//publish_memo
        , 'is_index_show'//是否首页显示
        , 'loans_pic'//白条满标放款凭证
        , 'is_hidden'//是否在投资列表隐藏 0：不隐藏，1：隐藏
        , 'peizi_order_ids'//临时存储peizi_order的id,翻遍撤销使用
        , 'customers_id'//所属客服
        , 'use_ecv'//是否可以使用红包
        , 'is_mortgage'//是否有抵押物
        , 'mortgage_desc'//抵押说明
        , 'mortgage_infos'//抵押物照片
        , 'mortgage_contract'//借款签约合同
        , 'admin_id'//管理员id
        , 'admin2_id'//催收管理员
        , 'admin2_status'//催收状态，0：线上 1：线下
        , 'collec_cnt'//催收次数
        , 'collec_status'//催收状态，0：NULL，1：本人承诺还款，2：家长承诺还款
        , 'mortgage_fee'//抵押物管理费
        , 'is_mobile'//是否是移动端申请
        , 'sor_code'//客户端识别码
        , 'user_agent'//客户端USER_AGENT
        , 'use_type'//借款用途
        , 'collateral'//抵押物
        , 'client_ip'//申请借款时的IP
        , 'fraudmetrix'//同盾决策
        , 'auditing_status'//审核中 1:等待审核,2:等待视频,3:未接电话
        , 'first_audit_admin_id'//初审人
        , 'bairong_credit'//百融金服信用数据
        , 'ascription'//推荐人归属
        , 'is_autobid'//是否允许自动投标 0:否 1:是
        , 'zm_points'//zm_points
        , 'zm_ivs'//zm_ivs
        , 'zm_watchlist'//zm_watchlist
        , 'contract_pdf'//借款协议pdf文件路径
        , 'contract_imagefiles'//借款协议图片集合
        , 'contract_image_sizes'//借款协议图片总大小（单位：字节）
        , 'contract_esign_link'//借款协议e签宝存档链接
        , 'first_failure_time'//初审失败时间
        , 'second_failure_time'//复审失败时间
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal';
    }

    public function getTotalRecordNum()
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('COUNT(*) AS num')
            ->execute()
            ->value('num');
        return $data;
    }

    public function getDealList($startLimit, $endLimit)
    {
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->limit($startLimit, $endLimit)
            ->execute()
            ->rows();
        return $data;
    }

}
