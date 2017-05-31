<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_generationrepay extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'deal_id'//deal_id
				,'repay_id'//第几期
				,'admin_id'//管理员ID
				,'agency_id'//担保机构ID
				,'self_money'//self_money
				,'interest_money'//interest_money
				,'repay_money'//代还多少本息
				,'manage_money'//代换多少管理费
				,'mortgage_fee'//代换多少抵押物管理费
				,'impose_money'//代还多少罚息
				,'manage_impose_money'//代换多少逾期管理费
				,'create_time'//代还时间
				,'create_date'//create_date
				,'status'//0待收款 1已收款 
				,'memo'//操作备注
				,'total_money_fee'//垫付罚息
				,'fee_day'//垫付天数
				,'is_auto_site_repay'//网站垫付操作：0-手动；1-自动
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'generation_repay';
	}
	
	//获取网站垫付统计
	public function getStatPlatformPayment($startDate,$endDate){
		return $this->getDb()->select("FROM_UNIXTIME(create_time,'%Y-%m-%d') as createdate,sum(repay_money) as paymenttotal,sum(manage_money) as feetotal,sum(impose_money) as imposetotal,sum(manage_impose_money) as managetotal")->from($this->getTable())->where(array('create_time >='=>$startDate,'create_time <='=>$endDate))->groupBy('createdate')->cache(C('stat_sql_cache_time'),__METHOD__.$startDate.$endDate)->execute()->rows();
	}

}
