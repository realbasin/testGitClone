<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_dealloantypeextern extends Dao {

    public function getColumns() {
        return [];
    }

    public function getPrimaryKey() {
        return 'id';
    }

    public function getTable() {
        return 'deal_loan_type_extern';
    }

    public function getRowByTypeId($loanTypeId){
        return $this->getDb()->select('*')->from($this->getTable())->where(['loan_type_id'=>$loanTypeId])->execute()->row();
    }

    public function exists($loanTypeId){
        $num =  $this->getDb()->select('COUNT(*) AS total')->from($this->getTable())->where(['loan_type_id'=>$loanTypeId])->execute()->value('total');
        return $num > 0;
    }


}