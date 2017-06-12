<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title></title>
<link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js?v=201705041335"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js?v=1111"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span>系统设置</span>
  <i class="arrow"></i>
  <span>院校管理</span>

</div>
<div class="line10"></div>
<div class="page">
    <div class="form-default">
        <form method="post" id="form1" name="form1">
            <input type="hidden" name="form_submit" value="ok" />
            <div class="title">
                名称:
                <input class="s-input-txt" type="text" name="name" />
                院校等级:
                <select value="0" id="grade_type" name='grade_type'>
                    <option value="">请选择</option>
                    <option value="">一本</option>
                    <option value="">二本</option>
                    <option value="">三本</option>
                    <option value="">专科</option>
                </select>
                省:
                <select id="province_id" name='province_id' onchange="load_city(this);">
                    <option value="0">请选择</option>
                    <?php foreach($provinceList as $province){ ?>
                        <option value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
                    <?php } ?>
                </select>
                市:
                <select id="region_id" name='region_id'>
                    <option value="0">请选择</option>
                </select>
                <input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
            </div>
        </form>
    </div>
	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
    var provinceCityList = <?php echo $provinceCityList; ?>;
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('sys_setting','school_list_json');?>',
        colModel : [
            {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 80, sortable : false, align: 'center', className: 'handle-m'},
            {display: '编号', name : 'id', width : 50, sortable : true, align: 'center'}, 
			{display: '名称', name : 'name', width : 200, sortable : true, align : 'center'},
			{display: '省份', name : 'province_id', width : 50, sortable : true, align: 'left'},
            {display: '城市', name : 'region_id', width : 50, sortable : true, align: 'left'},
			{display: '院校等级', name : 'grade_type', width : 100, sortable : true, align: 'center'},
			{display: '办学类型', name : 'invest_type', width : 100, sortable : true, align: 'center'},
			{display: '院校隶属', name : 'owner_type', width : 100, sortable : true, align: 'center'},
			{display: '发展水平', name : 'level_type', width : 100, sortable : false, align: 'center'},
            ],

        buttons : [
            {display: '<i class="fa fa-plus"></i> 添加', name : 'add', bclass : 'add', title : '添加', onpress: school_add },
        ],

        sortname: "id",
        sortorder: "desc",
        title: '院校列表'
    });
    
    $('#btnsearch').click(function(){
        $("#flexitable").flexOptions({url: '<?php echo adminUrl('sys_setting','school_list_json');?>&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
    });
    
});

function load_city(obj){
    var provinceId = $(obj).find("option:selected").val();
    for(var i=0;i<provinceCityList.length;i++){
        if(provinceCityList[i].id == provinceId){
            var cityList = provinceCityList[i].city_list;
            var html = '<option value="0">请选择</option>';
            for(var j=0;j<cityList.length;j++){
                html += "<option value=\""+cityList[j].id+"\">"+cityList[j].name+"</option>";
            }

            $('#region_id').empty().append(html);
            break;
        }
    }
}

function school_add(){
    location.href='<?php echo adminUrl('sys_setting','school_edit');?>';
}

function school_edit(id){
    location.href='<?php echo adminUrl('sys_setting','school_edit');?>' + '&id=' + id;
}

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<?php echo \Core::L('loan_all_help');?>",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>