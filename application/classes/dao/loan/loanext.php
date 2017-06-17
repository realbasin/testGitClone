<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_loanext extends Dao {

	public function getColumns() {
		return array(
				'loan_id'//loan表对应id
				,'seo_title'//SEO标题
				,'seo_keyword'//SEO关键词
				,'seo_description'//SEO说明
				,'contract_id'//借款合同模板ID
				,'scontract_id'//咨询服务协议ID
				,'tcontract_id'//转让服务合同模板ID
				,'is_send_contract'//是否已发送电子协议书
				,'contract_pdf'//借款协议pdf文件路径
				,'contract_imagefiles'//借款协议图片集合
				,'contract_esign_link'//E签宝链接
				,'is_mortgage'//是否有抵押物
				,'mortgage_desc'//抵押说明
				,'mortgage_infos'//抵押物照片
				,'mortgage_fee'//抵押物管理费
				,'collateral'//抵押物类型
				,'mortgage_contract'//借款签约合同照片
				,'config_common'//普通配置
				,'config_amt'//保证金配置
				);
	}

	public function getPrimaryKey() {
		return 'loan_id';
	}

	public function getTable() {
		return 'loan_ext';
	}
	/*
	 * 获取配置，默认获取普通配置
	 * */
	public function getConfig($field='config_common') {
		return $this->getDb()->from($this->getTable())->execute()->value($field);
	}
	/*
	 * 获取合同id
	 * */
	public function getContract($id) {
		return $this->getDb()->select('contract_id,scontract_id,tcontract_id')->from($this->getTable())->where(array('loan_id'=>$id))->execute()->row();
	}
	/*
	 * 获取保证金配置
	 * */
	public function getAmtconfig($id) {
		return unserialize($this->getDb()->from($this->getTable())->where(array('loan_id'=>$id))->execute()->value('config_amt'));
	}
	/*
	 * 获取普通配置
	 * */
	public function getCommonconfig($id) {
		return unserialize($this->getDb()->from($this->getTable())->where(array('loan_id'=>$id))->execute()->value('config_common'));
	}
	//根据id获取一条数据
	public function getExtByLoanId($loan_id,$field='*'){
		return $this->getDb()->select($field)->from($this->getTable())->where(array('loan_id'=>$loan_id))->execute()->row();
	}
}
