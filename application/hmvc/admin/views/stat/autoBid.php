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
  <span>平台统计</span>
  <i class="arrow"></i>
  <span>自动投标</span>

</div>
<div class="line10"></div>
<div class="page">
	<!--查询-->
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         会员ID：
         <input class="s-input-txt" type="text"  id="user_id" name="user_id">
         	 用户名：
          <input class="s-input-txt" type="text"  id="user_name" name="user_name">
          	 用户组：
          <select id="auto_bid" name="auto_bid">
          	<option value="-1">全部理财用户</option>
          	<option value="1">开启自动投标用户</option>
          	<option value="0">关闭自动投标用户</option>
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
        url: '<?php echo adminUrl('stat_platform','autoBid_json');?>'+'&'+$("#form1").serialize(),
        colModel : [
			{display: 'ID', name : 'id', width : 40, sortable : true, align: 'center'},
			{display: '用户名', name : 'user_name', width : 70, sortable : true, align: 'center'},
			{display: '当前账户余额', name : 'money', width : 80, sortable : true, align: 'center'},
			{display: '最小投标额', name : 'min_money', width : 70, sortable : true, align: 'center'},
			{display: '最大投标额', name : 'max_money', width : 70, sortable : true, align: 'center'},
			{display: '保留金额', name : 'retain_money', width : 60, sortable : true, align: 'center'},
			{display: '最小利率(%)', name : 'min_rate', width : 70, sortable : true, align: 'center'},
			{display: '最大利率(%)', name : 'max_rate', width : 70, sortable : true, align: 'center'},
			{display: '最小期限', name : 'min_period', width : 60, sortable : true, align: 'center'},
			{display: '最大期限', name : 'max_period', width : 60, sortable : true, align: 'center'},
			{display: '使用优惠券', name : 'use_bonus', width : 60, sortable : true, align: 'center'},
			{display: '启用', name : 'auto_bid', width : 30, sortable : true, align: 'center'},
			{display: '最近自动投标', name : 'last_bid_time', width : 80, sortable : true, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出Excel', name : 'csv', bclass : 'csv', title : '将选择的行或者全部数据导出为Excel', onpress : flexPress }
        ],
        sortname: "id",
        sortorder: "desc",
        title: '自动投标统计',
        columnControl:false
    });
    
    $('#btnsearch').click(function(){
        $("#flexitable").flexOptions({url: '<?php echo adminUrl('stat_platform','autoBid_json');?>&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
    });
});

function flexPress(name, grid) {
	if(name=='csv'){
       var itemlist = new Array();
        if($('.trSelected',grid).length>0){
            $('.trSelected',grid).each(function(){
            	itemlist.push($(this).attr('data-id'));
            });
        }
        flexExport(itemlist);
	}
}

function flexExport(id){
	 var ids = id.join(',');
	 var url= '<?php echo adminUrl('stat_platform','autoBid_export');?>&'+$("#flexitable").flexSimpleSearchQueryString(true)+"&"+$("#form1").serialize()+'&id='+ids;
     window.location.href =url;
}

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>选择需要导出的行可以导出指定数据<li>不选择任何行可以导出全部数据",
        quickClose: true
        });
       d.show(this);
});
</script>
</body>
</html>