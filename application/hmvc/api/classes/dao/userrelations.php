<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_userrelations extends Dao {

	public function getColumns() {
		return array(
				'user_id'//用户ID
				,'all_children'//所有下线user_id
				,'rank_children'//下线的层级的user_id
				,'all_parents'//所有上线的user_id
				,'update_time'//好友关系更新时间
				);
	}

	public function getPrimaryKey() {
		return 'user_id';
	}

	public function getTable() {
		return 'user_relations';
	}

    public function getUserRelationsByUserId($user_id)
    {
        return $this->getDb()
            ->select('*')
            ->from($this->getTable())
            ->where(['user_id' => $user_id])
            ->execute()
            ->row();
    }

}
