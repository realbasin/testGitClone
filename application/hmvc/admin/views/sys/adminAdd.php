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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->
</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" id="syshelp" onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('setting');?></span>
  <i class="arrow"></i>
  <span><a href="<?php echo adminUrl('sys_setting','admin');?>"><?php echo \Core::L('admin_setting');?></a></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('add');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('admin_name');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="admin_name" id="admin_name" class="input-txt" value="" datatype="/^[a-zA-Z]\w{4,19}$/" nullmsg="<?php echo \Core::L("admin_name_null");?>" errormsg="<?php echo \Core::L("admin_name_error");?>" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('admin_name_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('admin_password');?></label>
        </dt>
        <dd class="opt">
          <input type="password" name="admin_password" id="admin_password" class="input-txt" value="" datatype="/^[a-zA-Z]\w{5,19}$/" nullmsg="<?php echo \Core::L("admin_password_null");?>" errormsg="<?php echo \Core::L("admin_password_error");?>" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('admin_password_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('admin_password_confirm');?></label>
        </dt>
        <dd class="opt">
          <input type="password" name="admin_password_confirm" id="admin_password_confirm" class="input-txt" value="" recheck="admin_password" datatype="*" nullmsg="<?php echo \Core::L("admin_password_confirm_null");?>" errormsg="<?php echo \Core::L("admin_password_confirm_error");?>" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('admin_password_confirm_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('admin_gname');?></label>
        </dt>
        <dd class="opt">
          <div class="rule-single-select">
        <select name="admin_gid" id="admin_gid" datatype="*" nullmsg="<?php echo \Core::L('admin_gname_null');?>" sucmsg=" ">
	<option value=""><?php echo \Core::L("select_choose");?></option>
	<?php if(isset($authlist)){?>
		<?php foreach($authlist as  $v){?>
	<option value="<?php echo $v['gid']?>"><?php echo $v['gname']?></option>
	<?php }?>
		<?php }?>
</select>
      </div>
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('admin_gname_notice');?></p>
        </dd>
      </dl>
      </div>
      <div class="page-footer">
  <div class="btn-wrap">
    <input type="submit" name="btnSubmit" value="<?php echo \Core::L('submit');?>" id="btnSubmit" class="btn" />
  </div>
</div>
  </form>
</div>
<script type="text/javascript">
$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<?php echo \Core::L('admin_add_help');?>",
        quickClose: true
        });
       d.show(this);
});
    
 $(function () {
	 $("#form1").initValidform();
	});
</script>
</body>
</html>