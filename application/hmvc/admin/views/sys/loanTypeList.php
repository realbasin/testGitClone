<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type"
          content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset() ?>"/>
    <meta name="viewport"
          content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <title></title>
    <link href="<?php echo RS_PATH ?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo RS_PATH ?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css"/>
    <link href="<?php echo RS_PATH ?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css"/>
    <link href="<?php echo RS_PATH ?>css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo RS_PATH ?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" charset="utf-8"
            src="<?php echo RS_PATH ?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName()); ?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8"
            src="<?php echo RS_PATH ?>admin/js/common.js?v=201705041335"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH ?>admin/js/flexigrid.js?v=1111"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->

</head>
<body class="mainbody">
<div class="location">
    <div class="right"><a href="javascript:void(null);" id="syshelp" onfocus="this.blur();"><i
                    class="help"></i><?php echo \Core::L('help'); ?></a></div>
    <i class="home"></i>
    <span><?php echo \Core::L('loan'); ?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_type_setting'); ?></span>

</div>
<div class="line10"></div>
<div class="page">
    <div id="flexitable" class="flexitable"></div>
</div>

<script>
    $(function () {
        $("#flexitable").flexigrid({
            url: '<?php echo adminUrl('sys_loan', 'type_list_json');?>',
            colModel: [
                {
                    display: '<?php echo \Core::L("operate");?>',
                    name: 'operation',
                    width: 80,
                    sortable: false,
                    align: 'center',
                    className: 'handle-m'
                },
                {display: '编号', name: 'id', width: 50, sortable: true, align: 'center'},
                {display: '名称', name: 'name', width: 200, sortable: false, align: 'center'},
                {display: '是否需要额度', name: 'is_quota', width: 100, sortable: false, align: 'left'},
                {display: '是否启用', name: 'is_effect', width: 100, sortable: false, align: 'left'},
                {display: '排序', name: 'sort', width: 80, sortable: true, align: 'center'},
            ],
            buttons : [
                {display: '<i class="fa fa-plus"></i> <?php echo \Core::L("add");?>', name : 'add', bclass : 'add', title : '<?php echo \Core::L("add");?>', onpress: type_add },
            ],

            sortname: "id",
            sortorder: "desc",
            title: '借款类型'
        });
    });

    function type_add() {
        window.location.href = '<?php echo adminUrl('sys_loan','type_add');?>';
    }


</script>
</body>
</html>