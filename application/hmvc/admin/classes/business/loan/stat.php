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
			a.is_effect as is_effect,
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
	
	//逾期排名-业务员SQL
	public function getStatOverdueDetailAgentSql($startDate,$endDate,$orderBy="user_count desc"){
		$sql="select
		d.admin_id as adminid,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate and d.user_type=".C("USER_MARK_SALESMAN")." group by adminid order by ".$orderBy;
		 return $sql;
	}
	
	//逾期排名-业务员
	public function getStatOverdueDetailAgent($startDate,$endDate,$orderBy="user_count desc"){
		$sql=$this->getStatOverdueDetailAgentSql($startDate,$endDate,$orderBy);
		return \Core::db()->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate.str_replace(' ','_',$orderBy))->execute($sql)->key('adminid')->rows();
	}
	
	//逾期排名-行长SQL
	public function getStatOverdueDetailSalemanSql($startDate,$endDate,$order="user_count",$sort="desc"){
		$sql="select
		d.id as saleman_id,
		d.user_name as saleman_name,
		d.real_name as saleman_realname,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate and d.user_type=".C("USER_MARK_SALESMAN")." group by saleman_id order by ".$order." ".$sort;
		 return $sql;
	}
	
	//逾期排名-行长
	public function getStatOverdueDetailSaleman($page,$pagesize,$startDate,$endDate,$order="user_count",$sort="desc"){
		$sql=$this->getStatOverdueDetailSalemanSql($startDate,$endDate,$order,$sort);
		$bussiness=\Core::business('common');
		$datas=$bussiness->getPageList($page,$pagesize,$sql);
		return $datas;
	}
	
	//逾期排名-推荐人SQL
	public function getStatOverdueDetailReferrerSql($startDate,$endDate,$order="user_count",$sort="desc"){
		$sql="select
		c.pid as referrer_id,
		d.user_name as referrer_name,
		d.real_name as referrer_realname,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate  group by referrer_id order by ".$order." ".$sort;
		 return $sql;
	}
	
	//逾期排名-推荐人
	public function getStatOverdueDetailReferrer($page,$pagesize,$startDate,$endDate,$order="user_count",$sort="desc"){
		$sql=$this->getStatOverdueDetailReferrerSql($startDate,$endDate,$order,$sort);
		$bussiness=\Core::business('common');
		$datas=$bussiness->getPageList($page,$pagesize,$sql);
		return $datas;
	}
	
	//逾期排名-初审人SQL
	public function getStatOverdueDetailCheckerSql($startDate,$endDate,$orderBy="user_count desc"){
		$sql="select
		b.first_audit_admin_id as audit_id,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate and  b.first_audit_admin_id>0 group by audit_id order by ".$orderBy;
		 return $sql;
	}
	
	//逾期排名-初审人
	public function getStatOverdueDetailChecker($startDate,$endDate,$orderBy="user_count desc"){
		$sql=$this->getStatOverdueDetailCheckerSql($startDate,$endDate,$orderBy);
		return \Core::db()->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate.str_replace(' ','_',$orderBy))->execute($sql)->key('audit_id')->rows();
	}
	
	//逾期排名-按月SQL
	public function getStatOverdueDetailMonthSql($orderBy="deal_month asc"){
		$sql="select
		RIGHT(CONCAT('0',CAST(FROM_UNIXTIME(b.create_time+3600*8,'%m') AS UNSIGNED)),2) deal_month,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 group by deal_month order by ".$orderBy;
		 return $sql;
	}
	
	//逾期排名-按月
	public function getStatOverdueDetailMonth($orderBy="deal_month asc"){
		$sql=$this->getStatOverdueDetailMonthSql($orderBy);
		return \Core::db()->cache(C('stat_sql_cache_time'),__METHOD__.str_replace(' ','_',$orderBy))->execute($sql)->key('deal_month')->rows();
	}
	
	//逾期排名-按日SQL
	public function getStatOverdueDetailDaySql($orderBy="deal_day asc"){
		$sql="select
		RIGHT(CONCAT('0',CAST(FROM_UNIXTIME(b.create_time+3600*8,'%d') AS UNSIGNED)),2) deal_day,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 group by deal_day order by ".$orderBy;
		 return $sql;
	}
	
	//逾期排名-按日
	public function getStatOverdueDetailDay($orderBy="deal_day asc"){
		$sql=$this->getStatOverdueDetailDaySql($orderBy);
		return \Core::db()->cache(C('stat_sql_cache_time'),__METHOD__.str_replace(' ','_',$orderBy))->execute($sql)->key('deal_day')->rows();
	}
	
	//逾期排名-地区SQL
	public function getStatOverdueDetailAreaSql($startDate,$endDate,$order="user_count",$sort="desc"){
		$sql="select
		(select CONCAT(aa.name,ab.name) from _tablePrefix_region_conf aa,_tablePrefix_region_conf ab where ab.pid=aa.id and aa.id=c.province_id and ab.id=c.city_id) region_name,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate  group by region_name order by ".$order." ".$sort;
		 return $sql;
	}
	
	//逾期排名-地区
	public function getStatOverdueDetailArea($page,$pagesize,$startDate,$endDate,$order="user_count",$sort="desc"){
		$sql=$this->getStatOverdueDetailAreaSql($startDate,$endDate,$order,$sort);
		$bussiness=\Core::business('common');
		$datas=$bussiness->getPageList($page,$pagesize,$sql);
		return $datas;
	}
	
	//逾期排名-学院SQL
	public function getStatOverdueDetailCollegeSql($startDate,$endDate,$order="user_count",$sort="desc"){
		$sql="select
		c.university as college,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate  group by college order by ".$order." ".$sort;
		 return $sql;
	}
	
	//逾期排名-学院
	public function getStatOverdueDetailCollege($page,$pagesize,$startDate,$endDate,$order="user_count",$sort="desc"){
		$sql=$this->getStatOverdueDetailCollegeSql($startDate,$endDate,$order,$sort);
		$bussiness=\Core::business('common');
		$datas=$bussiness->getPageList($page,$pagesize,$sql);
		return $datas;
	}
	
	//逾期排名-年龄SQL
	public function getStatOverdueDetailAgeSql($startDate,$endDate,$order="age",$sort="asc"){
		$sql="select
		(YEAR(FROM_UNIXTIME(b.create_time))-c.byear-1)+if(MONTH(FROM_UNIXTIME(b.create_time))>c.bmonth,1,0)+if((DAY(FROM_UNIXTIME(b.create_time))>=c.bday and MONTH(FROM_UNIXTIME(b.create_time))=c.bmonth),1,0) age,
		count(DISTINCT b.user_id) as user_count,
		count(DISTINCT a.deal_id)  as deal_count,
		count(DISTINCT a.id)  as repay_count,
		count(DISTINCT if(a.has_repay = 1, a.id, null)) as has_repay_count,
		sum(CEILING((if(a.has_repay=1,(a.true_repay_time-a.repay_time),(UNIX_TIMESTAMP(NOW())-a.repay_time)))/(3600*24))) expired_days
			 from _tablePrefix_deal_repay as a inner join _tablePrefix_loan_base as b on a.deal_id=b.id
					inner join _tablePrefix_user as c on b.user_id=c.id
					left join _tablePrefix_user as d on d.id=c.pid
		 where if(isnull(d.rpid),0,d.rpid)=0 and ((a.has_repay = 1 and a.true_repay_time > a.repay_time) or (a.has_repay = 0 and  a.repay_time <= ".time().")) 
		 and a.repay_time>=$startDate and a.repay_time<=$endDate  group by age order by ".$order." ".$sort;
		 return $sql;
	}
	
	//逾期排名-年龄
	public function getStatOverdueDetailAge($page,$pagesize,$startDate,$endDate,$order="age",$sort="asc"){
		$sql=$this->getStatOverdueDetailAgeSql($startDate,$endDate,$order,$sort);
		$bussiness=\Core::business('common');
		$datas=$bussiness->getPageList($page,$pagesize,$sql);
		return $datas;
	}
	
	//增加校园行长
	public function insertSchoolDistributor(Array $insert=array()){
		$sql="insert into _tablePrefix_user";
		$sql1=implode(",", array_keys($insert));
		$sql2=implode(",", array_values($insert));
		$sql=$sql."(".$sql1.")values(".$sql2.")";
		return \Core::Db()->execute($sql);
	}
	
	//编辑校园行长
	public function editSchoolDistributor($id,Array $update=array()){
		$sql="update _tablePrefix_user set ";
		foreach($update as $k=>$v){
			$sql.="$k=$v,";
		}
		$sql=rtrim($sql,",");
		$sql.=" where id=$id";
		return \Core::Db()->execute($sql);
	}
}