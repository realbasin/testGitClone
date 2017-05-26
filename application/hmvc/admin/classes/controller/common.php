<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 通用调用控制器
 * 无需管理员权限
 */
class  controller_common extends controller_sysBase {
	public function do_index() {

	}

	//用户查询自动补全
	public function do_autoGetUsers() {
		$userName = \Core::getPost('q');
		$userBusiness = \Core::business('user_userinfo');
		$users = $userBusiness -> getUsersByName($userName);
		echo @json_encode($users);
	}
	
	//分片Excel下载
	public function do_excelExport(){
		//获取page
		$page=\Core::get('page');
		$url=\Core::get('url',adminUrl('dashboard'));
		if(!$page || !preg_match('/^[\d,]+$/', $page)){
			//错误跳转
			\Core::message('下载参数错误', $url, 'fail', 3, 'message');
		}
		//获取datakey
		$dataKey=\Core::get('datakey');
		$data=\Core::cache()->get($dataKey);
		if(!$data){
			\Core::message('下载链接已超时', $url, 'fail', 3, 'message');
		}
		if(!\Core::arrayKeyExists('sql', $data) || !\Core::arrayKeyExists('excelName', $data) || !\Core::arrayKeyExists('head', $data) || !!\Core::arrayKeyExists('murl', $data)){
			\Core::cache()->delete($dataKey);
			\Core::message('下载数据异常错误', $url, 'fail', 3, 'message');
		}
		$business=\Core::business('common');
		$business->exportExcel($data['sql'],$data['excelName'],$data['head'],$data['murl'],$page);
	}

}
