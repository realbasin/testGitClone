<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/**
 * 全国省市区dao
 * Class dao_regionconf
 */
class dao_regionconf extends Dao
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
        return 'region_conf';
    }

    public function getProvinceList()
    {
        //TODO    设置缓存
        $provinceList = $this->getDb()->select('*')->from($this->getTable())->where(['pid' => 1, 'region_level' => 2])->execute()->rows();
        return $provinceList;
    }

    /**
     * 获取省份城市列表
     * @return array
     */
    public function getProvinceCityList()
    {
        //TODO    设置缓存
        $result = [];
        $provinceList = $this->getDb()->select('*')->from($this->getTable())->where(['pid' => 1, 'region_level' => 2])->execute()->rows();
        foreach ($provinceList as $province) {
            $cityList = $this->getDb()->select('id,name')->from($this->getTable())->where(['pid' => $province['id'], 'region_level' => 3])->execute()->rows();
            $province['city_list'] = $cityList;
            $result[] = $province;
        }

        return $result;
    }

    /**
     * 获取区域名称
     * @param $regionId
     * @return mixed|null
     */
    public function getRegionName($regionId)
    {
        if (empty($regionId)) {
            return '';
        }
        return $this->getDb()->select('name')->from($this->getTable())->where(['id' => $regionId])->execute()->value('name');
    }

    public function getRowById($regionId){
        return $this->getDb()->select('*')->from($this->getTable())->where(['id' => $regionId])->execute()->row();
    }


}