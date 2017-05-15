<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_dealcate extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'name'//分类名称
				,'brief'//分类简介
				,'pid'//父类ID
				,'is_delete'//删除标识
				,'is_effect'//有效性标识
				,'sort'//sort
				,'uname'//uname
				,'icon'//分类icon
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'deal_cate';
	}
	
	public function getAllDealCate(){
		return $this->getDb()->select('*')->from($this->getTable())->where(array('is_effect'=>1,'is_delete'=>0))->execute()->key('id')->rows();
	}

}
