<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_stat extends Business {
	public function business() {
		
	}
	
	//统计投资排名
	//涉及多表
	//@retSql不为0时返回sql语句
	public function getStatTotalAmount($page,$pagesize,$stime,$etime,$loantype=0,$sort='desc',$retSql=false){
		$type_condition="";
		if($loantype){
			$type_condition=" AND _tablePrefix_deal.type_id=".$loantype;
		}
		$date_condition=" BETWEEN ".$stime." and ".$etime." ";
		
		$sql="SELECT
			stat.stat_user,
			stat.stat_user_name,
			stat.stat_real_name,
			stat.stat_mobile,
			stat.stat_amount_1,
			stat.stat_amount_3,
			stat.stat_amount_6,
			stat.stat_amount_9,
			stat.stat_amount_12,
			stat.stat_bond,
			stat.stat_amount_bond,
			stat.stat_amount_total
		FROM
			(
				SELECT
					id AS stat_user,
					user_name as stat_user_name,
					real_name as stat_real_name,		  
					AES_DECRYPT(mobile_encrypt,'__FANWEP2P__') AS stat_mobile,
					(SELECT SUM(_tablePrefix_deal_load.money) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND  _tablePrefix_deal.repay_time=1 AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .") stat_amount_1,
					(SELECT SUM(_tablePrefix_deal_load.money) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND  _tablePrefix_deal.repay_time=3 AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .") stat_amount_3,
					(SELECT SUM(_tablePrefix_deal_load.money) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND  _tablePrefix_deal.repay_time=6 AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .") stat_amount_6,
					(SELECT SUM(_tablePrefix_deal_load.money) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND  _tablePrefix_deal.repay_time=9 AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .") stat_amount_9,
					(SELECT SUM(_tablePrefix_deal_load.money) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND  _tablePrefix_deal.repay_time=12 AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .") stat_amount_12,
					(SELECT SUM(_tablePrefix_deal_load_transfer.transfer_amount) FROM _tablePrefix_deal_load_transfer INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load_transfer.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load_transfer.t_user_id=_tablePrefix_user.id AND (_tablePrefix_deal_load_transfer.transfer_time ".$date_condition.") ". $type_condition .") stat_bond,
					(SELECT SUM(_tablePrefix_deal_load_transfer.transfer_amount) FROM _tablePrefix_deal_load_transfer INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load_transfer.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load_transfer.user_id=_tablePrefix_user.id AND (_tablePrefix_deal_load_transfer.transfer_time ".$date_condition.") ". $type_condition .") stat_amount_bond,
					(SELECT (SELECT IF(SUM(_tablePrefix_deal_load.money) IS NULL,0,SUM(_tablePrefix_deal_load.money)) FROM _tablePrefix_deal_load INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load.user_id=_tablePrefix_user.id AND (_tablePrefix_deal_load.create_time ".$date_condition.") ". $type_condition .")+
(SELECT IF(SUM(_tablePrefix_deal_load_transfer.transfer_amount) IS NULL,0,SUM(_tablePrefix_deal_load_transfer.transfer_amount)) FROM _tablePrefix_deal_load_transfer INNER JOIN _tablePrefix_deal ON _tablePrefix_deal_load_transfer.deal_id=_tablePrefix_deal.id WHERE _tablePrefix_deal_load_transfer.t_user_id=_tablePrefix_user.id AND (_tablePrefix_deal_load_transfer.transfer_time ".$date_condition.") ". $type_condition .")) stat_amount_total
				FROM
					_tablePrefix_user
				WHERE
					user_mark = ".C('USER_MARK_INVEST')." AND is_delete=0 AND is_effect=1
			";

		$sql .= "GROUP BY id ORDER BY stat_amount_total ".$sort.") stat where stat.stat_amount_total>0 or stat.stat_bond>0 or stat.stat_amount_bond>0";
		//分页
		if(!$retSql){
			$bussiness=\Core::business('common');
			$data=$bussiness->getPageList($page,$pagesize,$sql);
			return $data;
		}
		return $sql;
	}
	
	//债券转让统计
	public function getStatTransferData($startDate,$endDate){
		$reDatas=\Core::cache()->get(__METHOD__.$startDate.$endDate);
		if($reDatas) {
			return $reDatas;
		}
			
		
		$daoTransfer=\Core::dao('loan_dealloadtransfer');
		$daoLog=\Core::dao('loan_sitemoneylog');
		//获取债权转让
		$dataAll=$daoTransfer->getStatTransferAll($startDate,$endDate);
		//获取成功债权转让
		$dataSuc=$daoTransfer->getStatTransferSuc($startDate,$endDate);
		//获取债权转让管理费
		$dataFee=$daoLog->getStatTransferFee($startDate,$endDate);
		//数据处理
		$datas=array();
		$dateList=getDateFromRange($startDate,$endDate,true);
		foreach($dateList as $v){
			$row=array();
			$row['date']=$v;
			$datas[$v]=$row;
		}
		$reDatas=array_merge_recursive($datas,$dataAll,$dataSuc,$dataFee);
		
		
		//缓存
		\Core::cache()->set(__METHOD__.$startDate.$endDate,$reDatas,C('stat_sql_cache_time'));
		return $reDatas;
	}
	
	public function getStatAutobid($userId,$userName,$userGroup,$ids='',$page,$pagesize,$order='',$sort='desc',$retSql=false){
		$where="";
		if($userId){
			$where.=' and u.id='.$userId;
		}
		if($userName){
			$where.=" and u.user_name like '%".$userId."%'";
		}
		if($userGroup>-1){
			$where.=" and a.is_effect=".$userGroup;
		}
		if($ids){
			$where.=" and u.id in(".$ids.")";
		}
		if(!$order){
			$order='id';
		}
		$sql="
			select 
			u.id as id,
			u.user_name as user_name,
			AES_DECRYPT(u.money_encrypt,'" . AES_DECRYPT_KEY . "') AS money,
			a.min_amount as min_money,
			a.max_amount as max_money,
			a.retain_amount as retain_money,
			a.min_rate as min_rate,
			a.max_rate as max_rate,
			a.min_period as min_period,
			a.max_period as max_period,
			a.is_use_bonus as use_bonus,
			a.is_effect as auto_bid,
			a.last_bid_time as last_bid_time 
			from _tablePrefix_user_autobid a  
			right join 
			_tablePrefix_user u 
			on a.user_id=u.id 
			where u.user_mark=".C('USER_MARK_INVEST').$where.' order by '.$order.' '.$sort;
		$sqlTotal="
		select 
		sum(AES_DECRYPT(u.money_encrypt,'" . AES_DECRYPT_KEY . "')) as total_money 
		from _tablePrefix_user_autobid a  
		right join 
		_tablePrefix_user u 
		on a.user_id=u.id 
		where u.user_mark=".C('USER_MARK_INVEST').$where;
		
		if(!$retSql){
			$bussiness=\Core::business('common');
			$datas=$bussiness->getPageList($page,$pagesize,$sql);
			//获取汇总
			$totalMoney=$bussiness->getDatas($sqlTotal);
			$total_money=0;
			if($totalMoney){
				$row=$totalMoney[0];
				$total_money=is_null($row['total_money'])?0:$row['total_money'];
			}
			$datas['total_money']=$total_money;
			return $datas;
		}
		return $sql;
	}
	
	//逾期排名-业务员
	public function getStatOverdueDetailAgent(){
		
	}
}