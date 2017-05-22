<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 借出统计
 */
class  controller_stat_loan extends Controller {
	
	public function before() {
		
	}
	
	//借出汇总
	//查询缓存10分钟
	public function do_all(){
		$datestart=\Core::post('datestart');
		$dateend=\Core::post('dateend');
		$where=array();
		if($datestart){
			$timestart=strtotime($datestart);
		}else{
			$timestart=0;
		}
		if($dateend){
			$timeend=strtotime($dateend);
		}else{
			$timeend=0;
		}
		
		$daoRepay=\Core::dao('loan_dealloadrepay');
		$daoUser=\Core::dao('user_user');
		$daoLoad=\Core::dao('loan_dealload');
		$data=$daoRepay->getStatLoanAll($timestart,$timeend);
		$data['balancetotal']=$daoUser->getStatBalanceTotal();
		$data['rebatetotal']=$daoLoad->getStatRebateTotal();
		$data['datestart']=$datestart;
		$data['dateend']=$dateend;
		\Core::view()->set($data)->load('stat_loanAll');
	}
	
	//投资人
	//缓存10分钟
	public function do_investor(){
		$datestart=\Core::postGet('datestart');
		$dateend=\Core::postGet('dateend');
		if(!$datestart || !$dateend){
			$datestart=0;
			$dateend=0;
		}
		\Core::view()->set('datestart',$datestart);
		\Core::view()->set('dateend',$dateend);
		\Core::view()->load('stat_investor');
	}
	
	//投资人json数据
	public function do_investor_json(){
		$datestart=\Core::postGet('datestart');
		$dateend=\Core::postGet('dateend');
		if(!$datestart || !$dateend){
			showJSON('100','请选择日期范围');
		}
		$daoLoad=\Core::dao('loan_dealload');
		$data=$daoLoad->getInvest(strtotime($datestart),strtotime($dateend));
		showJSON('200','',$data);
	}
	
	//导出投资人为excel
	public function do_investor_export(){
		
	}
	
	//投资额
	public function do_investAmount(){
		
	}
	
	//回款统计
	public function do_payment(){
		
	}
	
	//待收统计
	public function do_due(){
		
	}
	
	//待收明细
	public function do_dueDetail(){
		
	}
	
	//投资排名
	public function do_investRank(){
		
	}
	
	//投资比例
	public function do_investProportion(){
		
	}
}