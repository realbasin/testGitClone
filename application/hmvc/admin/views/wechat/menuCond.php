<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L('permission_setting');?></title>
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
  <span><?php echo \Core::L('wechat_setting');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('wechat_menu');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('wechat_menu_conditional');?></span>

</div>
<div class="line10"></div>
<div class="page">
	<div class="flexitable">
	<table class="flexigrid">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" align="center" class="handle"><?php echo \Core::L('operate');?></th>
          <th width="100" align="center"><?php echo \Core::L('wechat_cond_menuid');?></th>
          <th width="150" align="center"><?php echo \Core::L('wechat_cond_group');?></th>
          <th width="100" align="center"><?php echo \Core::L('wechat_cond_sex');?></th>
          <th width="150" align="center"><?php echo \Core::L('wechat_cond_area');?></th>
          <th width="150" align="center"><?php echo \Core::L('wechat_cond_client');?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($datalist) && is_array($datalist)){ ?>
        <?php foreach($datalist as $k => $v){ ?>
        <tr class="hover">
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle">
          <a class="btn red" href="javascript:void(null)" onclick="ShowDeleteDialog('<?php echo adminUrl('wechat_menu','conditional_del');?>&menuid=<?php echo $v['menuid'];?>');"><i class="fa fa-trash-o"></i> <?php echo \Core::L("delete");?></a>
          <a class="btn blue" href="javascript:ShowMenu(<?php echo $v['menuid'];?>)"><i class="fa fa-reorder"></i> <?php echo \Core::L('view');?></a>
          </td>
          <td><?php echo $v['menuid'];?></td>
          <td><?php echo $v['matchrule']['group_text'];?></td>
          <td><?php echo $v['matchrule']['sex_text'];?></td>
          <td><?php echo $v['matchrule']['country'].' '.$v['matchrule']['province'].' '.$v['matchrule']['city'];?></td>
          <td><?php echo $v['matchrule']['client_platform_type'];?></td>
          <td></td>
        </tr>
        <?php } ?>
        <?php } ?>
      </tbody>
    </table>
    </div>
</div>
<script>
$('.flexigrid').flexigrid({	
	usepager: false,
	reload: false,
	columnControl: false,
	title: '<?php echo \Core::L("wechat_menu_conditional")?>',
	buttons : [
               {display: '<i class="fa fa-plus"></i> <?php echo \Core::L("wechat_cond_add")?>', name : 'add', bclass : 'add', onpress : btnPress }
           ]
	});

function btnPress(name, grid) {
    if (name == 'add') {
        window.location.href = '<?php echo adminUrl('wechat_menu','conditional_add');?>';
    }
};

function ShowMenu(menuid){
	
}

$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<?php echo \Core::L('wechat_cond_help');?>",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>