<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');
class  business_loan_stat extends Business {
	public function business() {
		
	}
	
	//统计投资排名
	//涉及多表
	//@retSql不为0时返回sql语句
	public function getStatTotalAmount($page,$pagesize,$stime,$etime,$loantype=0,$sort='desc',$retSql=0){
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
}