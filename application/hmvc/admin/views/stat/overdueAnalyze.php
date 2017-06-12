<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	 
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title></title>
<link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/jquery.daterangepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->
<!--[if IE]>
	<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5shiv.min.js"></script>
<![endif]-->
</head>
<body class="mainbody">
<div class="location">
  <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span>借入统计</span>
  <i class="arrow"></i>
  <span>逾期数据分析</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         按时间段查询：
         <span id="daterange">
         <input class="s-input-txt" type="text" readonly="true" value="<?php echo $datestart?$datestart:date('Y-m-d',strtotime('-30 day'));?>" id="datestart" name="datestart">
         	至
         	<input class="s-input-txt" type="text" readonly="true" value="<?php echo $dateend?$dateend:date('Y-m-d',time());?>" id="dateend" name="dateend">
         		</span>
         	<input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
      </div>
      </form>
   </div>
 	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_borrow','overdueAnalyze_json');?>'+'&'+$("#form1").serialize(),
        colModel : [
            {display: '日期', name : 'create_time', width : 100, sortable : true, align: 'center'}, 
			{display: '逾期等级', name : 'level', width : 100, sortable : false, align : 'center'},
			{display: '逾期管理费', name : 'sum_repay_manage_money', width : 100, sortable : false, align: 'center'},
			{display: '逾期本息', name : 'sum_repay_money', width : 100, sortable : false, align: 'center'},
			{display: '逾期本金', name : 'sum_self_money', width : 100, sortable : false, align: 'center'},
			{display: '逾期笔数', name : 'sum_count_deal', width : 80, sortable : false, align: 'center'},
			{display: '逾期期数', name : 'sum_over_times', width : 80, sortable : false, align: 'center'},
			{display: '剩余未还本金', name : 'sum_over_money', width : 120, sortable : false, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出全部数据到Excel', name : 'csv', bclass : 'csv', title : '导出全部数据到Excel', onpress : flexPress }
        ],
       
        sortname: "create_time",
        sortorder: "asc",
        title: '逾期数据分析'
   });
   
});
	
function flexPress(name, grid) {
    if (name == 'csv') {
        window.location.href = '<?php echo adminUrl('stat_borrow','overdueAnalyze_export',array('datestart'=>$datestart?$datestart:date('Y-m-d',strtotime('-30 day')),'dateend'=>$dateend?$dateend:date('Y-m-d',time())));?>';
    }
};
	
$('#daterange').dateRangePicker({
	shortcuts:
			{
				'prev-days': [1,3,5,7,30,60],
				'prev' : null,
			},
	maxDays:60,
	//startDate:'<?php echo date('Y-m-d',strtotime("-60 day"));?>',
	endDate:'<?php echo date('Y-m-d',time());?>',
	getValue: function()
	{
		if ($('#datestart').val() && $('#dateend').val() )
			return $('#datestart').val() + ' to ' + $('#dateend').val();
		else
			return '';
	},
	setValue: function(s,s1,s2)
	{
		$('#datestart').val(s1);
		$('#dateend').val(s2);
	}
});

//查询
$('#btnsearch').on('click',function(){
	var datestart=$('#datestart').val();
	var dateend=$('#dateend').val();
	$("#flexitable").flexOptions({url: '<?php echo adminUrl('stat_borrow','overdueAnalyze_json');?>&datestart='+datestart+'&dateend='+dateend,query:'',qtype:''}).flexReload();
});

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以导出数据到Excel",
        quickClose: true
        });
       d.show(this);
});

</script>
</body>
</html>