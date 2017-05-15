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
  <span><?php echo \Core::L('variables_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
	<div  id="flexitable" class="flexitable">
	</div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('sys_setting','variables_json');?>',
        colModel : [
            {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '<?php echo \Core::L("variable_name");?>', name : 'name', width : 150, sortable : true, align: 'center'}, 
			{display: '<?php echo \Core::L("variable_value");?>', name : 'value', width : 300, sortable : false, align : 'left'},
			{display: '<?php echo \Core::L("variable_info");?>', name : 'info', width : 400, sortable : false, align: 'left'}
            ],
        buttons : [
            {display: '<i class="fa fa-trash"></i> <?php echo \Core::L("delete_batch");?>', name : 'delete', bclass : 'del', title : '<?php echo \Core::L("delete_batch_tip");?>', onpress : flexPress },
            {display: '<i class="fa fa-plus"></i> <?php echo \Core::L("add");?>', name : 'add', bclass : 'add', title : '<?php echo \Core::L("add");?>', onpress : flexPress }
        ],
        searchitems : [
            {display: '<?php echo \Core::L("variable_name");?>', name : 'name'},
            {display: '<?php echo \Core::L("variable_value");?>', name : 'value'},
            {display: '<?php echo \Core::L("variable_info");?>', name : 'info'}
            ],
        sortname: "name",
        sortorder: "asc",
        title: '<?php echo \Core::L("variable_list");?>'
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
	if(name=='add'){
		var d=parent.dialog({
            title: '<?php echo \Core::L("variable_add");?>',
			url: '<?php echo adminUrl('sys_setting','variables_add');?>'
		});
		d.width(550);
		d.addEventListener('close',function(){
			$("#flexitable").flexReload();
		});
		d.show();
	}
}

function flexEdit(id){
	var d=parent.dialog({
            title: '<?php echo \Core::L("variable_add");?>',
			url: '<?php echo adminUrl('sys_setting','variables_edit');?>&name='+id
		});
		d.width(550);
		d.addEventListener('close',function(){
			$("#flexitable").flexReload();
		});
		d.show();
}

function flexDelete(id){
	if (typeof id == 'string') {
    	var id = new Array(id);
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
        url: "<?php echo adminUrl('sys_setting','variables_del');?>",
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
        content: "<?php echo \Core::L('variable_help');?>",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>