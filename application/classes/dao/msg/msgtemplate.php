<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_msg_msgtemplate extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//名称标识
				,'content'//模板内容
				,'type'//类型 0短信 1邮件
				,'is_html'//针对邮件设置的是否超文本标识
				,'is_effect'//是否有效
				,'is_n_send_list'//启用不发送列表
				,'n_send_list'//不发送列表
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'msg_template';
	}
	//通过名称标识获取模板
	public function getTemplateByName($name,$field='*'){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('name'=>$name,'is_effect'=>1))->execute()->row();
	}
}
