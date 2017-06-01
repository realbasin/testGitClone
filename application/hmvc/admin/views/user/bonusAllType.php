<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title><?php echo \Core::L('site_setting');?></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
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
    <span>优惠券管理</span>
    <i class="arrow"></i>
    <span>优惠券类型</span>
</div>
<div class="line10"></div>
<div class="page">
    <div class="form-default">
        <form method="post" id="form1" name="form1">
            <input type="hidden" name="form_submit" value="ok" />
            <div class="title">
                资产类别：
                <select value="0" id="use_type" name='use_type'>
                    <option value="1">理财端</option>
                    <option value="2">借款端</option>
                </select>
                优惠券类型名称：
                <input class="s-input-txt" type="text" name="bonus_type_name" placeholder="请输入优惠券类型名称">
                <input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
            </div>
        </form>
    </div>
    <div class="flexitable" id="flexitable"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexitable").flexigrid({
        url: '<?php echo adminUrl('user_bonus','use_log_json');?>',
        colModel : [
            {display: '操作', name : 'operation', width : 50, sortable : false, align: 'center', className: 'handle-m'},
            {display: '编号', name : 'id', width : 50, sortable : false, align: 'center', className: 'center'},
            {display: '优惠券类型名称', name : 'bonus_type_name', width : 150, sortable : false, align: 'center'},
            {display: '资产类别', name : 'use_type', width : 50, sortable : false, align : 'left'},
            {display: '发放方式', name : 'send_type', width : 50, sortable : false, align: 'left'},
            {display: '发放时间范围', name : 'send_time_limit', width : 180, sortable : false, align: 'left'},
            {display: '使用时间范围', name : 'use_time_limit', width : 200, sortable : false, align: 'left'},
            {display: '发放数量', name : 'num', width : 50, sortable : false, align: 'left'},
            {display: '使用数量', name : 'used_num', width : 50, sortable : false, align: 'left'},
            {display: '优惠合计金额', name : 'amount', width : 100, sortable : false, align: 'left'},
            {display: '投资总额', name : 'userd_amount', width : 100, sortable : false, align: 'left'}
        ],
        buttons : [
            {display: '<i class="fa fa-plus"></i> <?php echo \Core::L("add");?>', name : 'add', bclass : 'add', title : '<?php echo \Core::L("add");?>', onpress: type_add },
            {display: '<i class="fa fa-trash"></i> <?php echo \Core::L("clear_batch");?>', name : 'delete', bclass : 'del', title : '<?php echo \Core::L("clear_batch_tip");?>', onpress: flexPress }
        ],
        title: '优惠券类型管理',
    });

    $('#btnsearch').click(function(){
        $("#flexitable").flexOptions({url: '<?php echo adminUrl('user_bonus','use_log_json');?>&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
    });
});
function type_add() {
    window.location.href = '<?php echo adminUrl('user_bonus','type_add');?>';
}
function type_edit(id) {
    window.location.href = '<?php echo adminUrl('user_bonus','type_edit');?>'+'&type_id='+id;
}
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
}
function flexDelete(id){
    var ids = new Array();
    ids.push(id);
    parent.dialog({
        title: lang['tip'],
        content: lang['delete_confirm'],
        okValue: lang['ok'],
        ok: function () {
            ids = ids.join(',');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "<?php echo adminUrl('user_bonus','type_delete');?>",
                data: "id="+ids,
                success: function(data){
                    jsprint(data.message);
                }
            });
        },
        cancelValue: lang['cancel'],
        cancel: function () { }
    }).showModal();
}
</script>
</body>
</html>
