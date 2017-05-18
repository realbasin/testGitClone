<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

$menu = array(
			0 => array(
				'text' => $lang['console'],
				'subtext'=>$lang['subconsole'],
				'img'=>'home',
				'list' => array(
					array('link'=>'dashboard,index','text'=>$lang['dashboard']),
				)
			),
			1 => array(
				'text' => $lang['setting'],
				'subtext'=>$lang['subsetting'],
				'img'=>'sys',
				'list' => array(
					array('link'=>'sys_setting,base','text'=>$lang['base_setting']),
					array('link'=>'sys_setting,upload','text'=>$lang['upload_setting']),
					array('link'=>'sys_setting,watermark','text'=>$lang['watermark_setting']),
					array('link'=>'sys_setting,email','text'=>$lang['email_setting']),
					array('link'=>'sys_setting,sms','text'=>$lang['sms_setting']),
					array('link'=>'sys_setting,login','text'=>$lang['login_setting']),
					array('link'=>'sys_setting,permission','text'=>$lang['permission_setting']),
					array('link'=>'sys_setting,admin','text'=>$lang['admin_setting']),
					array('link'=>'sys_setting,log','text'=>$lang['log_setting']),
					array('link'=>'sys_setting,variablessys','text'=>'系统变量'),
					array('link'=>'sys_setting,variables','text'=>$lang['variables_setting']),
					array('link'=>'sys_setting,cache','text'=>$lang['cache_setting']),
					array('link'=>'sys_setting,dbbackup','text'=>$lang['database_backup']),
					array('link'=>'sys_setting,dbrestore','text'=>$lang['database_restore']),
				)
			),
			2 => array(
				'text' => $lang['loan'],
				'subtext'=>$lang['subloan'],
				'img'=>'loan',
				'list' => array(
					array('link'=>'','text'=>$lang['loan'],
						'sub'=>array(
							array('link'=>'loan_loan,all','text'=>$lang['loan_all']),
							array('link'=>'loan_loan,notice','text'=>$lang['loan_notice']),
						)
					),
				)
			),
			3 => array(
				'text' => '会员管理',
				'subtext'=>'会员管理',
				'img'=>'user',
				'list' => array(
					
				)
			),
			4 => array(
				'text' => '资金管理',
				'subtext'=>'资金管理',
				'img'=>'fund',
				'list' => array(
					
				)
			),
			5 => array(
				'text' => '待办事务',
				'subtext'=>'待办事务',
				'img'=>'backlog',
				'list' => array(
					
				)
			),
			6 => array(
				'text' => '数据统计',
				'subtext'=>'数据统计',
				'img'=>'statistics',
				'list' => array(
					
				)
			),
			7 => array(
				'text' => '前端设置',
				'subtext'=>'前端设置',
				'img'=>'front',
				'list' => array(
					
				)
			),
		);

return $menu;
?>
