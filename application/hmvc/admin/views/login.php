<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L("login_title");?></title>
<link href="<?php echo RS_PATH?>/admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/jquery.dropdown.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RS_PATH?>jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="<?php echo RS_PATH?>jquery/jquery.dropdown.min.js"></script>
<script type="text/javascript" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
<script type="text/javascript" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<style>
	.jq-dropdown .jq-dropdown-panel{min-width: 1px;}
</style>
<script type="text/javascript">
$(function(){
	$.Tipmsg.r=null;
		
	var showmsg=function(msg){//假定你的信息提示方法为showmsg， 在方法里可以接收参数msg，当然也可以接收到o及cssctl;
		var d = dialog({
        content: msg
        });
       d.show();
       setTimeout(function () {
          d.close().remove();
          }, 2000);
	  }
	
	$("#loginform").Validform({
		tiptype:function(msg){
			showmsg(msg);
		},
		tipSweep:false,
		ajaxPost:false
	});
	
	$("#verifycode").click(function(){
		var timestamp = new Date().getTime();
        $(this).attr('src','<?php echo \Core::getUrl('captcha','',\Core::config()->getAdminModule())?>' + '&' +timestamp );
	})
})

</script>
</head>

<body class="loginbody">
<form method="post" id="loginform" name="loginform">
	<input type="hidden" name="form_submit" id="form_submit" value="ok" />
<div style="width:100%; height:100%; min-width:300px; min-height:300px;"></div>
<div class="login-wrap">
  <div class="login-logo">LOGO</div>
  <div class="login-form">
    <div class="col">
      <input name="txtUserName" type="text" id="txtUserName" class="login-input" placeholder="<?php echo \Core::L("user_name");?>" title="<?php echo \Core::L("user_name");?>" datatype="*" nullmsg="<?php echo \Core::L("null_user_name");?>" />
      <label class="icon user" for="txtUserName"></label>
    </div>
    <div class="col">
      <input name="txtPassword" type="password" id="txtPassword" class="login-input" placeholder="<?php echo \Core::L("user_pwd");?>" title="<?php echo \Core::L("user_pwd");?>" datatype="*" nullmsg="<?php echo \Core::L("null_user_pwd");?>" />
      <label class="icon pwd" for="txtPassword"></label>
    </div>
    <div class="col">
      <input name="txtVerify" type="text" id="txtVerify" autocomplete="off" class="login-input" data-jq-dropdown="#jq-dropdown-1"    placeholder="<?php echo \Core::L("user_captcha");?>" title="<?php echo \Core::L("user_captcha");?>" datatype="*" nullmsg="<?php echo \Core::L("null_user_captcha");?>" />
      <label class="icon verify" for="txtVerify"></label>
    </div>
    
    <div class="col">
      <input type="submit" name="btnSubmit" value="<?php echo \Core::L("login");?>" id="btnSubmit" class="login-btn" />
    </div>
  </div>
  <div class="login-tips"><i></i><p id="msgtip"><?php echo \Core::L("login_tip");?></p></div>
</div>

<div class="copy-right">
  <p><?php echo \Core::L("copyright");?></p>
</div>

<div id="jq-dropdown-1" class="jq-dropdown jq-dropdown-tip">
        <div class="jq-dropdown-panel">
        <img style="cursor: hand;" id="verifycode" src="<?php echo \Core::getUrl('captcha','',\Core::config()->getAdminModule())?>">
        </div>
    </div>
</form>
</body>
</html>
