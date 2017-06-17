<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_DealMsgList extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'dest'//发送目标（邮件/手机号
        , 'send_type'//发送类型 0:短信 1:邮件
        , 'content'//发送的内容
        , 'send_time'//发出的时间
        , 'is_send'//是否已发送 0:否 1:等待队列发送
        , 'create_time'//生成的时间
        , 'user_id'//会员ID
        , 'result'//发送结果（如出错存放服务器或接口返回的错误信息）
        , 'is_success'//是否发送成功
        , 'is_html'//只针对邮件使用，是否为超文本邮件 0:否 1:是
        , 'title'//只针对邮件使用 邮件的标题
        , 'is_youhui'//is_youhui
        , 'youhui_id'//youhui_id
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_msg_list';
    }

}
