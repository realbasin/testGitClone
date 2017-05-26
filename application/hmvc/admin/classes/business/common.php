<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

/*
 * 业务通用使用调用类
 * 多表查询分页
 * 通用业务功能
 */
class  business_common extends Business {
	public function business() {

	}

	//获取sql查询条数
	public function getCount($sql) {
		$sqlcount = "select count(*) as total from(" . $sql . ") as TotalTable";
		return \Core::db() -> execute($sqlcount) -> value('total', 0);
	}

	//获取分页数据列表
	public function getPageList($page, $pagesize, $sql) {
		$limit = " limit " . ($page - 1) * $pagesize . "," . $pagesize;
		$sqlList = $sql . $limit;
		$data = array();
		$data['total'] = $this -> getCount($sql);
		$data['rows'] = \Core::db() -> execute($sqlList)->rows();

		return $data;
	}

	//自动分片的Excel导出
	//@sql String 要执行的sql语句
	//@excelName String 导出Excel的名称
	//@head Array Excel头，需要对应查询的列
	//@murl String 上一页返回的地址，缓存KEY依据该地址
	//@format dataFormat.php中的数据格式化方法
	public function exportExcel($sql, $excelName, $head, $murl,$format='',$page=0) {
		if($page){
			$data=$this->getPageList($page,C('export_perpage'),$sql)->rows();
			if(!$format){
				$formatBusiness=\Core::business('dataFormat');
				$data=$formatBusiness->format($format,$data);
			}
			exportExcel($excelName."[".$page."]", $head, $data['rows']);
			exit;
		}
		$total = $this -> getCount($sql);
		if ($total > C('export_perpage')) {
			//数据量太大，执行分片下载
			//计算分片数量
			$pageArr = array();
			$page = ceil($total / C('export_perpage'));
			for ($i = 1; $i <= $page; $i++) {
				$limitStart = ($i - 1) * C('export_perpage') + 1;
				$limitEnd = $i * C('export_perpage') > $total ? $total : $i * C('export_perpage');
				$pageArr[$i] = $limitStart . ' ~ ' . $limitEnd;
			}
			//缓存分片数据
			$dataKey=md5($murl);
			\Core::cache()->set($dataKey,array('sql'=>$sql,'excelName'=>$excelName,'head'=>$head,'murl'=>$murl));
			//设置view数据
			\Core::view()->set('dataKey',$dataKey);
			\Core::view()->set('murl',$murl);
			\Core::view()->set('pages',$pageArr);
			//显示分片view
			\Core::view()->load('excel');
		} else {
			//数据量不够直接下载
			$data = \Core::db() -> execute($sql)->rows();
			if(!$format){
				$formatBusiness=\Core::business('dataFormat');
				$data=$formatBusiness->format($format,$data);
			}
			exportExcel($excelName, $head, $data);
		}
	}


}
