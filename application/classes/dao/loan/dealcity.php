<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealcity extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//name
				,'uname'//uname
				,'is_effect'//is_effect
				,'is_delete'//is_delete
				,'pid'//pid
				,'is_default'//is_default
				,'seo_title'//seo_title
				,'seo_keyword'//seo_keyword
				,'seo_description'//seo_description
				,'sort'//sort
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_city';
	}
	
	public function getCitys(){
		return $this->getDb()->select('*')->from($this->getTable())->where(array('is_effect'=>1,'is_delete'=>0))->orderBy('sort', 'asc')->execute()->rows();
	}

}
