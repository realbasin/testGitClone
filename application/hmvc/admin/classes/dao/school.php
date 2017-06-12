<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

class dao_school extends Dao {

	public function getColumns() {
		return array(
				'id'//id
				,'province_id'//省份ID
				,'region_id'//城市ID
				,'name'//学校(院)名称
				,'branch'//分校(院)名称
				,'owner_type'//院校隶属(1,部委属、2,省(直辖市)属，3,地区级的院校,4,市管(省级)院校)
				,'invest_type'//办学类型(1,国立,2,公立,3,私立,4,民办,5,中外合作办学)
				,'level_type'//按发展水平分类(1,985工程;2,211工程;3,重点;4.一般)
				,'long'//位置-经度
				,'lat'//位置-纬度
				,'student_scale'//学生规模
				,'description'//学校介绍
				,'url'//学校网址
				,'grade_type'//按院校等级分类(1,一本;2,二本;3,三本;4,大专)
				);
	}

	public function getPrimaryKey() {
		return 'id';
	}

	public function getTable() {
		return 'school';
	}

	public function getRowById($id){
        return $this->getDb()->select('*')->from($this->getTable())->where(['id' => $id])->execute()->row();
    }

}
