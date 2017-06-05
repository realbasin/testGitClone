<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  task_loanSuccess extends Task {
	/*针对$args参数要做说明，以便以后进行配置
	 * 注意：task代码内不能写任何die,exit
	 * 放款成功后自动执行的任务
	 *@option deal_id int 借款ID
	 */
	public function execute(CliArgs $args) {
		$deal_id=$args->get('deal_id');
		if($deal_id && is_numeric($deal_id)){
			$loanBaseDao->update(array('is_effect' => 1, 'loan_status' => 1), array('id' => $deal_id));
			//发送短信发送邮件

		    //是否存在优投用户，存在发送推送

			//发借款成功邮件

			//发借款成功站内信

			//发送借款协议范本
		}
	}
}