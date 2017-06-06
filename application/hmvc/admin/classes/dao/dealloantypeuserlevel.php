<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_dealloantypeuserlevel extends Dao {

    public function getColumns() {
        return [];
    }

    public function getPrimaryKey() {
        return 'id';
    }

    public function getTable() {
        return 'deal_loan_type_user_level';
    }

    public function getAllLevel($loanTypeId){
        $data =  $this->getDb()->select('*')->from($this->getTable())->where(['loan_type_id'=>$loanTypeId])->execute()->rows();
        foreach($data as &$item){
            $r = "";
            $str_arr = explode("\n",$item['repaytime']);
            foreach($str_arr as $kk=>$vv){
                $v_arr = explode("|",$vv);
                $r.="期限:".$v_arr[0].((int)$v_arr[1] == 0 ? "天":"月").",最小利率:".$v_arr[2].",最大利率:".$v_arr[3]."<br>";
            }
            $item['repaytime'] = $r;
        }
        return $data;
    }


}