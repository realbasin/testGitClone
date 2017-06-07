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
  <span>借入统计</span>
  <i class="arrow"></i>
  <span>逾期排名</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="tab-bar">
    <div class="tab-title">
    	<div class="subject"></div>
    	<?php if(isset($pagetabs)) echo $pagetabs;?>
    </div>
  </div>
 	<div  id="flexitable" class="flexitable"></div>
</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('stat_borrow','overdueDetail_day_json');?>',
        colModel : [
            {display: '借款日', name : 'deal_day', width : 150, sortable : true, align: 'center'}, 
			{display: '逾期总人数', name : 'user_count', width : 120, sortable : true, align : 'center'},
			{display: '逾期总笔数', name : 'deal_count', width : 120, sortable : true, align: 'center'},
			{display: '逾期总期数', name : 'repay_count', width : 120, sortable : true, align: 'center'},
			{display: '逾期已还期数', name : 'has_repay_count', width : 120, sortable : true, align: 'center'},
			{display: '逾期总天数', name : 'expired_days', width : 120, sortable : true, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出全部数据到Excel', name : 'csv', bclass : 'csv', title : '导出全部数据到Excel', onpress : flexPress }
        ],
       
        sortname: "deal_day",
        sortorder: "asc",
        title: '按日逾期列表',
        usepager:false
   });
   
});
	
function flexPress(name, grid) {
    if (name == 'csv') {
        window.location.href = '<?php echo adminUrl('stat_borrow','overdueDetail_day_export');?>';
    }
};

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>可以导出数据到Excel",
        quickClose: true
        });
       d.show(this);
});

</script>
</body>
</html>