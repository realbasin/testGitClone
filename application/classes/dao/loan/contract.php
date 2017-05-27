<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_contract extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'title'//范本标题
				,'content'//范本内容
				,'is_effect'//0无效 1有效
				,'is_delete'//0正常 1删除
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'contract';
	}
	
	public function getContract($id){
		return $this->getDb()->select('*')->from($this->getTable())->where($id)->execute->row();
	}
	//获取有效合同范本列表
	public function getContractList($fields){
		$where = array();
		$where['is_effect'] = 1;
		$where['is_delete'] = 0;
		return $this->getDb()->select($fields)->from($this->getTable())->where($where)->execute()->rows();
	}
}
