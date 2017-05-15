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
		);

return $menu;
?>
