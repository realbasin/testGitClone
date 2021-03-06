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
  <span><?php echo \Core::L('upload_setting');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('upload_qiniu_setting');?></span>
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
          <label><?php echo \Core::L('upload_driver');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="upload_driver" id="upload_driver" value="qiniu" <?php if(C('upload_driver')=='qiniu') echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('upload_driver_qiniu');?></p>
        </dd>
      </dl>
    <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('upload_qiniu_bucket');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="bucket" id="bucket" class="input-txt" value="<?php if(isset($bucket)) echo $bucket;?>" datatype="*"  sucmsg=" ">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('upload_qiniu_bucket_notice');?></p>
        </dd>
      </dl>
    <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('upload_qiniu_accesskey');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="accesskey" id="accesskey" class="input-txt" value="<?php if(isset($accesskey)) echo $accesskey;?>" datatype="*"  sucmsg=" ">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('upload_qiniu_accesskey_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em><?php echo \Core::L('upload_qiniu_secretkey');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="secretkey" id="secretkey" class="input-txt" value="<?php if(isset($secretkey)) echo $secretkey;?>" datatype="*"  sucmsg=" ">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('upload_qiniu_accesskey_notice');?></p>
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
	var help_content="<?php echo \Core::L('upload_setting_qiniu_help');?>";
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