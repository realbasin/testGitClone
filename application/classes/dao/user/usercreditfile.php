<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_usercreditfile extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//会员ID
				,'type'//审核类型
				,'file'//序列化后的审核资料地址
				,'create_time'//上传时间
				,'status'//0未处理，1已处理
				,'passed'//是否认证通过
				,'passed_time'//认证日期
				,'msg'//失败原因
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_credit_file';
	}
	
	//根据用户id获取用户认证资料
	public function getByUserId($user_id)
	{
		return $this->getDb()->select('*')->from($this->getTable())->where(array('user_id'=>$user_id))->execute()->rows();
	}

}
