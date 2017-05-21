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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js?id=1"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('setting');?></span>
  <i class="arrow"></i>
  <span>系统变量列表</span>

</div>
<div class="line10"></div>
<div class="page">
	<div  id="flexitable" class="flexitable">
	</div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('sys_setting','variablessys_json');?>',
        colModel : [
            {display: '<?php echo \Core::L("variable_name");?>', name : 'name', width : 150, sortable : true, align: 'center'}, 
			{display: '<?php echo \Core::L("variable_value");?>', name : 'value', width : 300, sortable : false, align : 'left'},
			{display: '<?php echo \Core::L("variable_info");?>', name : 'info', width : 400, sortable : false, align: 'left'}
            ],
        searchitems : [
            {display: '<?php echo \Core::L("variable_name");?>', name : 'name'},
            {display: '<?php echo \Core::L("variable_value");?>', name : 'value'},
            {display: '<?php echo \Core::L("variable_info");?>', name : 'info'}
            ],
        sortname: "name",
        sortorder: "asc",
        title: '系统变量列表'
    });
});

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以查看或查询系统变量",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>