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
            url: '<?php echo adminUrl('user_bonus','use_log_json');?>',
            colModel : [
                {display: '编号', name : 'id', width : 24, sortable : true, align: 'center'},
                {display: '优惠券号', name : 'value', width : 80, sortable : false, align : 'left'},
                {display: '优惠券类型名称', name : 'info', width : 150, sortable : false, align: 'left'},
                {display: '资产类别', name : 'info', width : 50, sortable : false, align: 'left'},
                {display: '用户名', name : 'info', width : 100, sortable : false, align: 'left'},
                {display: '手机号码', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '面额', name : 'info', width : 40, sortable : false, align: 'left'},
                {display: '使用最少金额', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '领取时间', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '领取方式', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '使用时间', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '使用情况', name : 'info', width : 80, sortable : false, align: 'left'}
            ],
            title: '优惠券使用情况'
        });
    });
</script>
</body>
</html>