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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
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
  <span><?php echo \Core::L('log_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
	<div  id="flexitable" class="flexitable">
	</div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('sys_setting','log_json');?>',
        colModel : [
            {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
            {display: '<?php echo \Core::L("admin_log_admin_name");?>', name : 'admin_name', width : 120, sortable : true, align: 'center'}, 
			{display: '<?php echo \Core::L("admin_log_content");?>', name : 'content', width : 400, sortable : false, align : 'left'},           
			{display: '<?php echo \Core::L("admin_log_admin_ip");?>', name : 'ip', width : 140, sortable : true, align: 'center'},
			{display: '<?php echo \Core::L("admin_log_time");?>', name : 'operatetime', width : 120, sortable : true, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-trash"></i> <?php echo \Core::L("delete_batch");?>', name : 'delete', bclass : 'del', title : '<?php echo \Core::L("delete_batch_tip");?>', onpress : flexPress },
            {display: '<i class="fa fa-file-excel-o"></i> <?php echo \Core::L("export_excel");?>', name : 'csv', bclass : 'csv', title : '<?php echo \Core::L("export_excel_tip");?>', onpress : flexPress }
        ],
        searchitems : [
            {display: '<?php echo \Core::L("admin_log_admin_name");?>', name : 'admin_name'},
            {display: '<?php echo \Core::L("admin_log_content");?>', name : 'content'},
            {display: '<?php echo \Core::L("admin_log_admin_ip");?>', name : 'ip'}
            ],
        sortname: "operatetime",
        sortorder: "desc",
        title: '<?php echo \Core::L("admin_log");?>'
    });
});

function flexPress(name, grid) {
	if(name=='delete'){
		if($('.trSelected',grid).length>0){
            var itemlist = new Array();
            $('.trSelected',grid).each(function(){
            	itemlist.push($(this).attr('data-id'));
            });
            flexDelete(itemlist);
        } else {
        	jsprint(lang['no_rows_selected']);
            return false;
        }
	}
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
	 var url= '<?php echo adminUrl('sys_setting','log_export');?>&'+$("#flexitable").flexSimpleSearchQueryString(true)+'&id=' + id;
     window.location.href =url;
}

function flexDelete(id){
	if (typeof id == 'number') {
    	var id = new Array(id.toString());
	};
	parent.dialog({
        title: lang['tip'],
        content: lang['delete_confirm'],
        okValue: lang['ok'],
        ok: function () {
        	id = id.join(',');
        	$.ajax({
        type: "GET",
        dataType: "json",
        url: "<?php echo adminUrl('sys_setting','log_del');?>",
        data: "id="+id,
        success: function(data){
            if (data.code==200){
                $("#flexitable").flexReload();
            } else {
            	jsprint(data.message);
            }
        }
    });
        },
        cancelValue: lang['cancel'],
        cancel: function () { }
    }).showModal();
}

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<?php echo \Core::L('admin_log_help');?>",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>