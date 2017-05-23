<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
/*
 * 借出统计
 */
class  controller_stat_loan extends controller_sysBase {
	
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
	
	//导出投资人excel
	public function do_investor_export(){
		$datestart=\Core::get('datestart');
		$dateend=\Core::get('dateend');
		$datestart=$datestart?$datestart:date('Y-m-d',strtotime('-30 day'));
		$dateend=$dateend?$dateend:date('Y-m-d',time());
		//Excel头部
		$header = array();
		$header['日期'] = 'datetime';
		$header['投资人数量'] = 'integer';
		$header['投资金额'] = 'price';

		$daoLoad=\Core::dao('loan_dealload');
		//Excel内容
		$data=$daoLoad->getInvest(strtotime($datestart),strtotime($dateend));
		//导出
		$this -> log('导出投资人/金额统计列表('.$datestart.' - '.$dateend.')', 'export');
		exportExcel('投资人/金额统计列表('.$datestart.' - '.$dateend.')', $header, $data);
	}
	
	//投资额
	public function do_investAmount(){
		$datestart=\Core::postGet('datestart');
		$dateend=\Core::postGet('dateend');
		if(!$datestart || !$dateend){
			$datestart=0;
			$dateend=0;
		}
		\Core::view()->set('datestart',$datestart);
		\Core::view()->set('dateend',$dateend);
		\Core::view()->load('stat_investAmount');
	}
	
	public function do_investAmount_json(){
		$datestart=\Core::postGet('datestart');
		$dateend=\Core::postGet('dateend');
		if(!$datestart || !$dateend){
			showJSON('100','请选择日期范围');
		}
		$daoLoad=\Core::dao('loan_dealload');
		$data=$daoLoad->getInvestAmount(strtotime($datestart),strtotime($dateend));
		showJSON('200','',$data);
	}
	
	public function do_investAmount_export(){
		$datestart=\Core::get('datestart');
		$dateend=\Core::get('dateend');
		$datestart=$datestart?$datestart:date('Y-m-d',strtotime('-30 day'));
		$dateend=$dateend?$dateend:date('Y-m-d',time());
		//Excel头部
		$header = array();
		$header['日期'] = 'datetime';
		$header['投资人次'] = 'integer';
		$header['投资成功金额'] = 'price';
		$header['冻结投资金额'] = 'price';
		$header['投资失败金额'] = 'price';
		$header['奖励金额'] = 'price';

		$daoLoad=\Core::dao('loan_dealload');
		//Excel内容
		$data=$daoLoad->getInvestAmount(strtotime($datestart),strtotime($dateend));
		//导出
		$this -> log('导出成功投资比率汇总('.$datestart.' - '.$dateend.')', 'export');
		exportExcel('成功投资比率汇总('.$datestart.' - '.$dateend.')', $header, $data);
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