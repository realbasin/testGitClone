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
<div class="line10"></div>
<div class="page">
	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('loan_loan','all_loaditem_json',array('id'=>$id,'lkey'=>$lkey));?>',
        colModel : [
            {display: '贷款编号', name : 'deal_id', width : 50, sortable : false, align: 'center'},
			{display: '会员', name : 'user_id', width : 60, sortable : false, align : 'center'},
			{display: '承接人', name : 't_user_id', width : 60, sortable : false, align: 'left'},
            {display: '还款金额', name : 'repay_money', width : 60, sortable : false, align: 'left'},
			{display: '管理费', name : 'manage_money', width : 50, sortable : false, align: 'center'},
			{display: '利息管理费', name : 'true_manage_interest_money', width : 60, sortable : false, align: 'center'},
			{display: '提前还款利息管理费', name : 'true_manage_early_interest_money', width : 100, sortable : false, align: 'center'},
			{display: '逾期/违约金', name : 'impose_money', width : 60, sortable : false, align: 'center'},
			{display: '预期收益', name : 'reward_money', width : 60, sortable : false, align: 'center'},
			{display: '实际收益', name : 'true_reward_money', width : 50, sortable : false, align: 'center'},
			{display: '状态', name : 'status', width : 50, sortable : false, align: 'center'},
			{display: '还款人', name : 'is_site_repay', width : 40, sortable : false, align: 'center'},
            ],
        usepager: false,
        reload: false,
        columnControl: false,
        sortname: "deal_id",
        sortorder: "desc",
        title: ''
    });
    
});

</script>
</body>
</html>