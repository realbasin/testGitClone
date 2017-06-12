<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_dealloantypeuserlevel extends Dao
{

    public function getColumns()
    {
        return [];
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'deal_loan_type_user_level';
    }

    public function getAllLevel($loanTypeId)
    {
        $data = $this->getDb()->select('*')->from($this->getTable())->where(['loan_type_id' => $loanTypeId])->execute()->rows();
        foreach ($data as &$item) {
            $r = "";
            $str_arr = explode("\n", $item['repaytime']);
            foreach ($str_arr as $kk => $vv) {
                $v_arr = explode("|", $vv);
                $r .= "期限:" . $v_arr[0] . ((int)$v_arr[1] == 0 ? "天" : "月") . ",最小利率:" . $v_arr[2] . ",最大利率:" . $v_arr[3] . "<br>";
            }
            $item['repaytime'] = $r;
        }
        return $data;
    }

    /**
     * 获取一条用户等级记录
     * @param $userLevelId
     * @return array
     */
    public function getRowById($userLevelId)
    {
        $data = $this->getDb()->select('*')->from($this->getTable())->where(['id' => $userLevelId])->execute()->row();

        $repayTimeDetails = [];
        $str_arr = explode("\n", $data['repaytime']);
        foreach ($str_arr as $kk => $vv) {
            $v_arr = explode("|", $vv);
            $repayTimeDetails[] = [
                'deadline' => $v_arr[0],
                'deadline_type' => $v_arr[1],
                'min_rate' => $v_arr[2],
                'max_rate' => $v_arr[3]
            ];
        }

        $data['repaytime_items'] = $repayTimeDetails;
        return $data;
    }

    public function updateData($data)
    {
        $repaytime = "";
        foreach ($data['repaytime'] as $k => $v) {
            if ((int)$v['deadline'] > 0) {
                $repaytime .= (int)$v['deadline'] . "|" . (int)$v['deadline_type'] . "|" . (float)$v['min_rate'] . "|" . (float)$v['max_rate'] . "\n";
            }
        }
        $repaytime = substr($repaytime, 0, strlen($repaytime) - 1);

        $userLevel = [
            'services_fee' => $data['services_fee'],
            'enddate' => $data['enddate'],
            'repaytime' => $repaytime
        ];

        $this->update($userLevel, ['id' => $data['id']]);
    }


}