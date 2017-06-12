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
  <span>行长列表</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         会员名称：<input class="s-input-txt" type="text" value="" id="user_name" name="user_name"> 
         真实姓名：<input class="s-input-txt" type="text" value="" id="real_name" name="real_name"> 
         手机号码：<input class="s-input-txt" type="text" value="" id="mobile" name="mobile">  
         归属：<select id="admin_id" name="admin_id" value="-1">
         	<option value="-1">请选择</option>
         	<option value="0">无归属</option>
         	<?php foreach($agents as $k=>$v){?>
         		<option value="<?php echo $k;?>"><?php echo $v['real_name']?$v['real_name']:$v['agent_name'];?></option>
         	<?php }?>
         </select>
         	<input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
      </div>
      </form>
   </div>
 	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_distributor','schoolDistributor_json');?>'+'&'+$("#form1").serialize(),
        colModel : [
        	{display: '操作', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
            {display: '编号', name : 'id', width : 80, sortable : true, align: 'center'}, 
			{display: '名称', name : 'user_name', width : 100, sortable : true, align : 'center'},
			{display: '真实姓名', name : 'real_name', width : 100, sortable : true, align: 'center'},
			{display: '邮箱', name : 'email', width : 130, sortable : true, align: 'left'},
			{display: '手机号', name : 'mobile', width : 120, sortable : true, align: 'left'},
			{display: '归属', name : 'admin_id', width : 80, sortable : true, align: 'center'},
			{display: '最后登录时间', name : 'login_time', width : 100, sortable : true, align: 'left'},
			{display: '是否启用', name : 'is_effect', width : 60, sortable : false, align: 'center'}
            ],
        buttons : [
        	{display: '<i class="fa fa-plus"></i> 新增行长', name : 'add', bclass : 'add', title : '新增行长', onpress : flexPress },
        	{display: '<i class="fa fa-user-circle"></i> 批量修改归属', name : 'edit', bclass : 'del', title : '批量修改归属人', onpress : flexPress },
            {display: '<i class="fa fa-file-excel-o"></i> 导出Excel', name : 'csv', bclass : 'csv', title : '导出勾选的数据或者全部数据到Excel', onpress : flexPress }
        ],
       
        sortname: "create_time",
        sortorder: "asc",
        title: '行长列表'
   });
   
});
	
function flexPress(name, grid) {
	if(name=='add'){
		flexAddInfo();
	}else if (name == 'edit') {
		if($('.trSelected',grid).length>0){
			var itemlist = new Array();
            $('.trSelected',grid).each(function(){
            	itemlist.push($(this).attr('data-id'));
            });
            flexEdit(itemlist);
        } else {
        	jsprint(lang['no_rows_selected']);
            return false;
        }
	}else if (name == 'csv') {
		var itemlist = new Array();
        if($('.trSelected',grid).length>0){
            $('.trSelected',grid).each(function(){
            	itemlist.push($(this).attr('data-id'));
            });
        }
        flexExport(itemlist);
    }
};

//导出
function flexExport(id){
	 var ids = id.join(',');
	 var url= '<?php echo adminUrl('stat_distributor','schoolDistributor_export');?>'+'&'+$("#form1").serialize()+'&id=' + ids;
     window.location.href =url;
}

//批量修改归属
function flexEdit(id){
	var ids = id.join(',');
	var d=parent.dialog({
            title: '批量修改行长归属',
			url: '<?php echo adminUrl('stat_distributor','schoolDistributor_edit');?>&id='+ids
		});
		d.width(550);
		d.addEventListener('close',function(){
			$("#flexitable").flexReload();
		});
		d.show();
}

//修改行长信息
function flexEditInfo(id){
	location.href='<?php echo adminUrl('stat_distributor','schoolDistributor_editInfo');?>&id='+id;
}

//增加行长
function flexAddInfo(){
	location.href='<?php echo adminUrl('stat_distributor','schoolDistributor_add');?>';
}

//查询
$('#btnsearch').on('click',function(){
	$("#flexitable").flexOptions({url: '<?php echo adminUrl('stat_distributor','schoolDistributor_json');?>'+'&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
});

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以导出勾选的数据或者全部数据到Excel",
        quickClose: true
        });
       d.show(this);
});

</script>
</body>
</html>