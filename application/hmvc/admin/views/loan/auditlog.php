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
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->

</head>
<body class="mainbody">
<div class="location">
    <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
    <i class="home"></i>
    <span><?php echo \Core::L('loan');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_all');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_op_log');?></span>

</div>
<div class="line10"></div>
<div class="page">
    <div  id="flexitable" class="flexitable"></div>
</div>
<script>
    $(function(){
        $("#flexitable").flexigrid({
            url: '<?php echo adminUrl('loan_loan','all_op_log_json',array('loan_id'=>$loan_id));?>',
            colModel : [
                {display: '贷款编号', name : 'deal_id', width : 50, sortable : false, align: 'center'},
                {display: '借款用户', name : 'user_id', width : 100, sortable : false, align : 'center'},
                {display: '操作阶段', name : 'op_name', width : 80, sortable : false, align: 'left'},
                {display: '日志信息', name : 'log', width : 500, sortable : false, align: 'left'},
                {display: '结果', name : 'op_result', width : 80, sortable : false, align: 'center'},
                {display: '操作人', name : 'admin_id', width : 150, sortable : false, align: 'center'},
                {display: '操作时间', name : 'create_time', width : 150, sortable : false, align: 'center'},
                {display: '操作IP', name : 'ip', width : 150, sortable : false, align: 'center'},
            ],
            searchitems : [
                {display: '贷款编号', name : 'deal_id'},
            ],
            sortname: "create_time",
            sortorder: "desc",
            title: '审核日志列表',
            usepager: true, //是否分页
            columnControl:false,
            reload:false,
        });

        $('#submit').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_op_log_json');?>&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        $('#reset').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_op_log_json');?>'}).flexReload();
            $("#formSearch")[0].reset();
        });

    });
    function flexPress(name, grid) {
        if(name=='add'){
            location.href='<?php echo adminUrl('loan_loan','add');?>';
        }
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