<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_loan_sorcode extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'type'//类型，1PC，2Android，3IOS
				,'code_pfix'//编码特征
				,'sor_code'//客户端来源编码
				,'code_name'//代码描述
				,'user_agent'//客户端浏览器描述
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'sor_code';
	}
	
	public function getSorList(){
		return $this->getDb()->select('*')->from($this->getTable())->execute()->key('sor_code')->rows();
	}

}
