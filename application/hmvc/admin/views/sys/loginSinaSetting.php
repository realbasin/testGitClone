<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L('upload_setting');?></title>
<link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" onclick="help(this);"  onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('login_setting');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('login_sina_setting');?></span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="tab-bar">
    <div class="tab-title">
    	<div class="subject"></div>
    	<?php if(isset($pagetabs)) echo $pagetabs;?>
    </div>
  </div>
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    	<dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('login_sina');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="login_sina" id="login_sina" value="oss" <?php if(C('login_sina')) echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('login_sina_notice');?></p>
        </dd>
      </dl>
    <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('appid');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="appid" id="appid" class="input-txt" value="<?php if(isset($appid)) echo $appid;?>" datatype="*"  sucmsg=" ">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('appid_notice');?></p>
        </dd>
      </dl>
    <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('appkey');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="appkey" id="appkey" class="input-txt" value="<?php if(isset($appkey)) echo $appkey;?>" datatype="*"  sucmsg=" ">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('appkey_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('metacode');?></label>
        </dt>
        <dd class="opt">
        	<textarea name="metacode" rows="6" class="tarea" id="metacode"><?php if(isset($metacode)) echo $metacode;?></textarea>
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('metacode_notice');?></p>
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
	var help_content="<?php echo \Core::L('login_sina_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
$(function () {
        //初始化表单验证
        $("#form1").initValidform();
    });
</script>
</body>
</html>