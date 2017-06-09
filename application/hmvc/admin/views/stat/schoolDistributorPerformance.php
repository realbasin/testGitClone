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
  <span>行长统计</span>
  <i class="arrow"></i>
  <span>行长业绩</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title" style="line-height: 30px;">
         名称：<input class="s-input-txt" type="text" value="" id="user_name" name="user_name"> 
         姓名：<input class="s-input-txt" type="text" value="" id="real_name" name="real_name"> 
         手机：<input class="s-input-txt" type="text" value="" id="mobile" name="mobile">  
         归属：<select id="admin_id" name="admin_id" value="-1">
         	<option value="-1">请选择</option>
         	<option value="0">无归属</option>
         	<?php foreach($agents as $k=>$v){?>
         		<option value="<?php echo $k;?>"><?php echo $v['real_name']?$v['real_name']:$v['agent_name'];?></option>
         	<?php }?>
         </select>
         	时间段：
         	<span id="daterange"><input class="s-input-txt" type="text" readonly="true" value="<?php echo $datestart?$datestart:date('Y-m-d',strtotime('-30 day'));?>" id="datestart" name="datestart">
         	至
         	<input class="s-input-txt" type="text" readonly="true" value="<?php echo $dateend?$dateend:date('Y-m-d',time());?>" id="dateend" name="dateend">
         	</span>
         	<input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 10px;" value="提交查询"></button>
      </div>
      </form>
   </div>
 	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_distributor','schoolDistributorPerformance_json');?>'+'&'+$("#form1").serialize(),
        colModel : [
        	{display: '操作', name : 'operation', width : 100, sortable : false, align: 'center', className: 'handle-m'},
            {display: '编号', name : 'id', width : 80, sortable : true, align: 'center'}, 
			{display: '名称', name : 'user_name', width : 100, sortable : true, align : 'center'},
			{display: '真实姓名', name : 'real_name', width : 100, sortable : true, align: 'center'},
			{display: '归属', name : 'admin_id', width : 80, sortable : true, align: 'center'},
			{display: '总客户数量', name : 'affiliates_count', width : 60, sortable : true, align: 'left'},
			{display: '总放款金额', name : 'total_amount', width : 80, sortable : true, align: 'left'},
			{display: '首借放款额', name : 'first_amount', width : 80, sortable : true, align: 'left'},
			{display: '续借放款额', name : 'more_amount', width : 80, sortable : false, align: 'left'},
			{display: '首借还款额', name : 'first_repay', width : 80, sortable : true, align: 'left'},
			{display: '续借还款额', name : 'more_repay', width : 80, sortable : false, align: 'left'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出全部数据到Excel', name : 'csv', bclass : 'csv', title : '导出全部数据到Excel', onpress : flexPress }
        ],
        sortname: "id",
        sortorder: "desc",
        title: '行长业绩统计'
   });
   
});
	
function flexPress(name, grid) {
	if (name == 'csv') {
        flexExport();
    }
};

//导出
function flexExport(){
	 var url= '<?php echo adminUrl('stat_distributor','schoolDistributorPerformance_export');?>'+'&'+$("#form1").serialize();
     window.location.href =url;
}

function list_day(id){
	
}

function list_month(id){
	
}

function list_sub(id){
	
}

$('#daterange').dateRangePicker({
	shortcuts:
			{
				'prev-days': [1,3,5,7,30,60],
				'prev' : ['week','month','year']
			},
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
	$("#flexitable").flexOptions({url: '<?php echo adminUrl('stat_distributor','schoolDistributorPerformance_json');?>'+'&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
});

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以导出全部数据到Excel",
        quickClose: true
        });
       d.show(this);
});

</script>
</body>
</html>