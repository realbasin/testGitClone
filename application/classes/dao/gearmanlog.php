<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_gearmanlog extends Dao {

	public function getColumns() {
		return array(
				'id'//ID
				,'task_name'//函数名称
				,'task_args'//函数参数数据
				,'content'//日志内容
				,'add_time'//插入时间
				,'unique_id'//唯一id
				,'server_ip'//IP地址
				,'status'//任务状态
				,'result'//成功后的结果
				,'sync'//是否是同步任务 0:否,1:是
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'gearman_log';
	}

}
