<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_LoanBase extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//自动增长ID
        , 'deal_sn'//借款编号
        , 'name'//借款名称
        , 'sub_name'//借款缩略名称
        , 'apply_borrow_amount'//申请借款金额
        , 'borrow_amount'//实际借款金额
        , 'user_id'//借款用户ID
        , 'cate_id'//借款分类ID
        , 'type_id'//借款类型ID
        , 'use_type'//借款用途ID
        , 'agency_id'//担保机构ID
        , 'agency_status'//担保机构审核状态 0应邀 1邀请中 2拒绝
        , 'repay_time'//贷款期限
        , 'repay_time_type'//贷款期限类型 0按天 1按月
        , 'rate'//利率
        , 'loantype'//还款方式  0:等额本息 1:付息还本 2:到期本息
        , 'warrant'//担保范围 0:无  1:本金 2:本金及利息
        , 'is_referral_award'//是否有推荐人奖励
        , 'publish_wait'//与is_delete共用 1等待初审（初审失败） 2等待复审 3复审失败
        , 'is_delete'//删除标识
        , 'is_effect'//是否有效
        , 'loan_status'//放款状态 0未放款 1已放款
        , 'b_status'//首单还是续借  0首单 1续借
        , 'icon'//缩略图array('type'=>0,'img'=>'')序列化type 0用户上传 1用户头像 2类型图
        , 'description'//用户填写的申请信息
        , 'create_time'//添加时间
        , 'update_time'//更新时间
        , 'is_mobile'//是否是移动端申请
        , 'sor_code'//客户端识别码
        , 'user_agent'//客户端user_agent
        , 'client_ip'//客户端IP
        , 'claim_time'//认领时间
        , 'first_audit_time'//初审通过时间
        , 'delete_msg'//审核失败提示
        , 'delete_real_msg'//审核失败真实原因
        , 'first_audit_admin_id'//初审人
        , 'second_audit_time'//复审通过时间
        , 'publish_memo'//复审失败原因
        , 'second_audit_admin_id'//复审人
        , 'source_id'//source_id
        , 'first_failure_time'//初审失败时间
        , 'second_failure_time'//复审失败时间
        , 'risk_rank'//风险等级
        , 'risk_security'//风险描述
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'loan_base';
    }

}
