<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_stat_dealaudit extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'date_time'//所统计数据的日期
				,'admin_id'//审核人员ID
				,'totals'//总审核笔数
				,'success_totals'//审核成功总笔数
				,'first_totals'//首借审核总笔数
				,'first_success_totals'//首借审核成功笔数
				,'renew_totals'//续借审核总笔数
				,'renew_success_totals'//续借审核成功笔数
				,'true_totals'//复审总笔数
				,'true_success_totals'//复审成功笔数
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_audit_stat';
	}
	
	//获取统计报表
	//@startDate 开始时间 非unix时间
	//@endDate 结束时间
	public function getStatCheck($startDate,$endDate,$order='',$sort='desc'){
		$select = "admin_id,
		SUM(totals) total_deals,
		SUM(success_totals) success_deals,
		SUM(success_totals)/SUM(totals) success_percent,
		SUM(first_totals) first_check_deals,
		SUM(first_success_totals) first_success_deals,
		SUM(first_success_totals)/SUM(first_totals) first_success_percent,
		SUM(renew_totals) renew_check_deals,
		SUM(renew_success_totals) renew_success_deals,
		SUM(renew_success_totals)/SUM(renew_totals) renew_success_percent,
		SUM(true_totals) true_deals,
		SUM(true_success_totals) true_success_deals";
		$where=array();
		$where['unix_timestamp(date_time) >=']=$startDate;
		$where['unix_timestamp(date_time) <=']=$endDate;
		$orderBy=$order;
		if(!$orderBy){
			$orderBy='total_deals';
		}
		if($orderBy=='admin_name'){
			$orderBy='admin_id';
		}
		if(!in_array($orderBy, array('admin_id','total_deals','success_deals','first_check_deals','renew_check_deals','renew_success_deals','true_deals','true_success_deals','success_percent','first_success_percent','renew_success_percent'))){
			$orderBy='total_deals';
		}
		$this->getDb()->select($select)->from($this->getTable())->where($where)->groupBy('admin_id')->orderBy($orderBy,$sort)->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate.$order.$sort);
		return $this->getDb()->execute()->key('admin_id')->rows();
	}
	
	public function getStatCheckDetail($page,$pageSize,$startDate,$endDate,$adminId,$order='',$sort='desc',$ids=array()){
		$select = "id,
		date_time,
		totals,
		success_totals,
		(success_totals)/(totals) as success_percent,
		first_totals,
		first_success_totals,
		(first_success_totals)/(first_totals) as first_success_percent,
		renew_totals,
		renew_success_totals,
		(renew_success_totals)/(renew_totals) as renew_success_percent,
		true_totals,
		true_success_totals";
		$where=array();
		$where['unix_timestamp(date_time) >=']=$startDate;
		$where['unix_timestamp(date_time) <=']=$endDate;
		$where['admin_id']=$adminId;
		if($ids){
			$where['id']=$ids;
		}
		$orderBy=$order;
		if(!$orderBy){
			$orderBy='date_time';
		}
		if(!in_array($orderBy, array('date_time','total_deals','success_deals','first_check_deals','renew_check_deals','renew_success_deals','true_deals','true_success_deals','success_percent','first_success_percent','renew_success_percent'))){
			$orderBy='date_time';
		}
		return $this->getFlexPage($page,$pageSize,$select,$where,array($orderBy=>$sort));
	}

}
