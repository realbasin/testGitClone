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
							//array('link'=>'loan_loan,notice','text'=>$lang['loan_notice']),
							//add by zlz 20170518
							array('link'=>'loan_oplog,index','text'=>$lang['loan_op_log']),
						)
					),
				)
			),
			3 => array(
				'text' => '会员管理',
				'subtext'=>'会员管理',
				'img'=>'user',
				'list' => array(
					array('link'=>'','text'=>'优惠券管理',
						'sub'=>array(
							array('link'=>'user_bonus,all_type','text'=>'优惠券类型'),
							array('link'=>'user_bonus,manual','text'=>'手动发放优惠券'),
							array('link'=>'user_bonus,use_log','text'=>'优惠券使用情况'),
						),
					),
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
					array('link'=>'','text'=>'借出统计',
						'sub'=>array(
							array('link'=>'stat_loan,all','text'=>'借出汇总'),
							array('link'=>'stat_loan,investor','text'=>'投资人数/金额'),
							array('link'=>'stat_loan,investAmount','text'=>'成功投资比率'),
							array('link'=>'stat_loan,payment','text'=>'回款统计'),
							array('link'=>'stat_loan,due','text'=>'待收统计'),
							array('link'=>'stat_loan,dueDetail','text'=>'待收明细'),
							array('link'=>'stat_loan,investRank','text'=>'投资排名'),
							array('link'=>'stat_loan,investProportion','text'=>'投资比例'),
						)
					),
					array('link'=>'','text'=>'借入统计',
						'sub'=>array(
							array('link'=>'stat_borrow,all','text'=>'借入汇总'),
							array('link'=>'stat_borrow,borrower','text'=>'借款人次'),
							array('link'=>'stat_borrow,borrowerAmount','text'=>'借款额'),
							array('link'=>'stat_borrow,repayment','text'=>'已还统计'),
							array('link'=>'stat_borrow,noRepayment','text'=>'待还统计'),
							array('link'=>'stat_borrow,overdueDetail','text'=>'逾期排名'),
							array('link'=>'stat_borrow,overdueAnalyze','text'=>'逾期分析'),
							array('link'=>'stat_borrow,overdueDay','text'=>'逾期波动'),
						)
					),
					array('link'=>'','text'=>'债权统计',
						'sub'=>array(
							array('link'=>'stat_debenture,debentureTransfer','text'=>'债券转让'),
						)
					),
					array('link'=>'','text'=>'平台统计',
						'sub'=>array(
							array('link'=>'stat_platform,recharge','text'=>'充值统计'),
							array('link'=>'stat_platform,withdraw','text'=>'提现统计'),
							array('link'=>'stat_platform,userRegist','text'=>'用户注册统计'),
							array('link'=>'stat_platform,platformPayment','text'=>'垫付统计'),
							array('link'=>'stat_platform,check','text'=>'审核汇总'),
							array('link'=>'stat_platform,autoBid','text'=>'自动投标'),
						)
					),
					array('link'=>'','text'=>'校园行长',
						'sub'=>array(
							array('link'=>'stat_distributor,schoolDistributor','text'=>'行长列表'),
							array('link'=>'stat_distributor,schoolDistributorPerformance','text'=>'行长业绩'),
						)
					),
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
