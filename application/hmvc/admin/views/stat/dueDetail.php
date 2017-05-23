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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js?v=1.0"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span>借出统计</span>
  <i class="arrow"></i>
  <span>待收明细</span>

</div>
<div class="line10"></div>
<div class="page">
	<!--列表-->
	<div  id="flexitable" class="flexitable">
	</div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_loan','dueDetail_json');?>',
        colModel : [
            {display: '会员ID', name : 'user_id', width : 120, sortable : true, align : 'center'},
			{display: '会员名称', name : 'user_name', width : 120, sortable : false, align : 'center'},
			{display: '真实姓名', name : 'real_name', width : 120, sortable : true, align: 'center'},
			{display: '手机号码', name : 'mobile', width : 120, sortable : true, align: 'center'},
			{display: '账户余额', name : 'money', width : 120, sortable : true, align: 'center'},
			{display: '冻结资金', name : 'lock_money', width : 120, sortable : true, align: 'center'},
			{display: '待收本金', name : 'self_money', width : 120, sortable : false, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出Excel', name : 'csv', bclass : 'csv', title : '将选择的行或者全部数据导出为Excel', onpress : flexPress }
        ],
        searchitems : [
            {display: '会员ID', name : 'user_id'},
            {display: '会员名称', name : 'user_name'},
            {display: '真实姓名', name : 'real_name'},
            {display: '手机号码', name : 'mobile'},
            {display: '身份证号码', name : 'idno'}
            ],
        sortname: "operatetime",
        sortorder: "desc",
        title: '待收明细',
        placeholder:'请填写完整搜索数据'
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
	 var url= '<?php echo adminUrl('stat_loan','dueDetail_export');?>&'+$("#flexitable").flexSimpleSearchQueryString(true)+'&id=' + id;
     window.location.href =url;
}


$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以选择需要导出的行<li>如果不选择任何行，则导出全部数据",
        quickClose: true
        });
       d.show(this);
});
</script>
</body>
</html>