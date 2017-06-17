<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_RegionPartnerConf extends Dao
{

    public function getColumns()
    {
        return array(
            'id'//id
        , 'name'//名称
        , 'value'//值
        );
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'region_partner_conf';
    }

    public function getRowByName($name)
    {
        $where = array('name' => $name);
        $data = $this->getDb()
            ->from($this->getTable())
            ->select('*')
            ->where($where)
            ->execute()
            ->row();
        return $data;
    }

}
