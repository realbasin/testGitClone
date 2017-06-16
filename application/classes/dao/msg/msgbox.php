<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_msg_msgbox extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'title'//标题
				,'content'//内容
				,'from_user_id'//发件人ID 0表示系统自动发送的信息
				,'to_user_id'//收信人ID
				,'create_time'//发信时间
				,'is_read'//是否已读 0:未读 1:已读
				,'is_delete'//是否被用户删除
				,'system_msg_id'//系统群发的系统通知关联的群发数据ID
				,'type'//type
				,'group_key'//group_key
				,'is_notice'//1系统通知 2材料通过 3审核失败 4额度更新 5提现申请 6提现成功 7提现失败 8还款成功 9回款成功 10借款流标 11投标流标 12三日内还款 13标被留言 14标留言被回复 15借款投标过半 16投标满标 17债权转让失败，18债权转让成功 19续约成功 20续约失败 0用户信息
				,'fav_id'//关联数据id
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'msg_box';
	}
//会员信息发送
	/**
	 *
	 * @param $title 标题
	 * @param $content 内容
	 * @param $from_user_id 发件人
	 * @param $to_user_id 收件人
	 * @param $create_time 时间
	 * @param $sys_msg_id 系统消息ID
	 * @param $only_send true为只发送，生成发件数据，不生成收件数据
	 * @param $fav_id 相关ID
	 */
	function sendUserMsg($title,$content,$from_user_id,$to_user_id,$create_time,$sys_msg_id=0,$only_send=false,$is_notice = false,$fav_id = 0)
	{
		$group_arr = array($from_user_id,$to_user_id);
		sort($group_arr);
		if($sys_msg_id>0){
			$group_arr[] = $sys_msg_id;
		}
		if($is_notice > 0){
			$group_arr[] = $is_notice;
		}
		$msg = array();
		$msg['title'] = $title;
		$msg['content'] = addslashes($content);
		$msg['from_user_id'] = $from_user_id;
		$msg['to_user_id'] = $to_user_id;
		$msg['create_time'] = $create_time;
		$msg['system_msg_id'] = $sys_msg_id;
		$msg['type'] = 0;
		$msg['group_key'] = implode("_",$group_arr);
		$msg['is_notice'] = intval($is_notice);
		$msg['fav_id'] = intval($fav_id);
		$id = $this->insert($msg);
		if($is_notice)
			$this->update(array('group_key'=>$msg['group_key']."_".$id),array('id'=>$id));
		if(!$only_send)
		{
			$msg['type'] = 1; //记录发件
			$this->insert($msg);
		}
	}
}
