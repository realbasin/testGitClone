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
  <span>借出统计</span>
  <i class="arrow"></i>
  <span>投资排名</span>

</div>
<div class="line10"></div>
<div class="page">
	<!--查询-->
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         时间段：
         <span id="daterange">
         <input class="s-input-txt" type="text" readonly="true" value="<?php echo $datestart?$datestart:date('Y-m-d',strtotime('-30 day'));?>" id="datestart" name="datestart">
         	至
         	<input class="s-input-txt" type="text" readonly="true" value="<?php echo $dateend?$dateend:date('Y-m-d',time());?>" id="dateend" name="dateend">
         		</span>
         	借款类型：
         	<select value="0" id="loantype" name='loantype'>
         		<option value="0" selected="selected">--全部类型--</option>
         		<?php foreach($loantype as $v){?>
         			<option value="<?php echo $v['id']?>" ><?php echo $v['name']?></option>
         		<?php }?>
         	</select>
         	<input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
      </div>
      </form>
   </div>
	<!--列表-->
	<div  id="flexitable" class="flexitable">
	</div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_loan','investRank_json');?>',
        colModel : [
			{display: '投资人', name : 'stat_user', width : 100, sortable : false, align : 'center'},
			{display: '手机号码', name : 'stat_mobile', width : 100, sortable : false, align: 'center'},
			{display: '总投标金额', name : 'stat_amount_total', width : 100, sortable : true, align: 'center'},
			{display: '普通标(1)', name : 'stat_amount_1', width : 90, sortable : false, align: 'center'},
			{display: '普通标(3)', name : 'stat_amount_3', width : 90, sortable : false, align: 'center'},
			{display: '普通标(6)', name : 'stat_amount_6', width : 90, sortable : false, align: 'center'},
			{display: '普通标(9)', name : 'stat_amount_9', width : 90, sortable : false, align: 'center'},
			{display: '普通标(12)', name : 'stat_amount_12', width : 90, sortable : false, align: 'center'},
			{display: '债权标', name : 'stat_bond', width : 90, sortable : false, align: 'center'},
			{display: '债权转让金额', name : 'stat_amount_bond', width : 90, sortable : false, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 全部导出为Excel', name : 'csv', bclass : 'csv', title : '将选择的行或者全部数据导出为Excel', onpress : flexPress }
        ],
        sortname: "amount_total",
        sortorder: "desc",
        title: '投资排名'
    });
    
    $('#btnsearch').click(function(){
        $("#flexitable").flexOptions({url: '<?php echo adminUrl('stat_loan','investRank_json');?>&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
    });
});

function flexPress(name, grid) {
	if(name=='csv'){
        flexExport();
	}
}

function flexExport(){
	 var url= '<?php echo adminUrl('stat_loan','investRank_export');?>&'+$("#flexitable").flexSimpleSearchQueryString(true)+"&"+$("#form1").serialize();
     window.location.href =url;
}

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


$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>系统自动过滤投资额为0的用户<li>可以选择需要导出的行<li>如果不选择任何行，则导出全部数据",
        quickClose: true
        });
       d.show(this);
});
</script>
</body>
</html>