<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_user_usersignature extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'user_id'//用户ID
				,'real_name'//真实姓名
				,'id_no'//身份证号码
				,'mobile'//手机号码
				,'accountId'//e签宝账户ID
				,'accountId_error_msg'//注册帐户返回结果(成功则空)
				,'signature'//e签宝签章数据流
				,'signature_error_msg'//生成签章返回结果(成功则空)
				,'type'//类型：1-个人；2-丙企业；3-丁企业
				,'is_effect'//是否启用：0-否；1-是
				,'create_time'//创建时间
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'user_signature';
	}

    /**
     * 获取e签宝数据
     * @param string $fields
     * @param array $condition
     * @param string $type row-单条数据；one-字段内容；all-所有数据
     * @return array|mixed
     */
	public function getUserSignByCondition($fields='*', $condition=array('is_effect'=>1), $type='row') {
	    $query = $this->getDb()->select($fields)->from($this->getTable())->where($condition)->execute();
        switch ($type) {
            case 'one':
                $result = $query->value($fields);
                break;
            case 'all':
                $result = $query->rows();
                break;
            case 'row':
            default:
                $result = $query->row();
        }
        return $result;
    }
}
