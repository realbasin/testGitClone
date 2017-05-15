<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L('site_setting');?></title>
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
	  <div  class="right"><a href="javascript:void(null);" id="syshelp" onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('wechat_setting');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('wechat_payment_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" enctype="multipart/form-data"   id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_mch_id');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_mch_id" id="wechat_mch_id" class="input-txt" value="<?php echo C('wechat_mch_id');?>">
          <p class="notic"><?php echo \Core::L('wechat_mch_id_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_partner_key');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_partner_key" id="wechat_partner_key" class="input-txt" value="<?php echo C('wechat_partner_key');?>">
          <p class="notic"><?php echo \Core::L('wechat_partner_key_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_ssl_cer');?></label>
        </dt>
        <dd class="opt">
          <div class="input-file-show" style="padding-left: 10px;"><span class="type-file-box">
        	<input onfocus="this.blur();" type='text' name='filecer' id='filecer' class='type-file-text' datatype="/.*pem$/" ignore="ignore" sucmsg=" "  errormsg="<?php echo \Core::L('wechat_ssl_upload_error');?>"><input type='button' name='button' id='button' value='<?php echo C('wechat_ssl_cer')? \Core::L('upload_again') : \Core::L('upload');?> ...' class='type-file-button' />
            <input class="type-file-file" id="wechat_ssl_cer" name="wechat_ssl_cer[]" type="file" size="30" hidefocus="true" dz_type="add_wechat_ssl_cer">
            </span>
            </div>
           <span class="Validform_checktip"></span>
           <p class="notic"><?php echo \Core::L('wechat_ssl_cer_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_ssl_key');?></label>
        </dt>
        <dd class="opt">
          <div class="input-file-show" style="padding-left: 10px;"><span class="type-file-box">
        	<input onfocus="this.blur();" type='text' name='filekey' id='filekey' class='type-file-text' datatype="/.*pem$/" ignore="ignore" sucmsg=" "  errormsg="<?php echo \Core::L('wechat_ssl_upload_error');?>"><input type='button' name='button' id='button' value='<?php  echo C('wechat_ssl_key')? \Core::L('upload_again') : \Core::L('upload');?> ...' class='type-file-button' />
            <input class="type-file-file" id="wechat_ssl_key" name="wechat_ssl_key[]" type="file" size="30" hidefocus="true" dz_type="add_wechat_ssl_key">
            </span>
            </div>
           <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('wechat_ssl_key_notice');?></p>
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
        content: "<?php echo \Core::L('wechat_payment_help');?>",
        quickClose: true
        });
       d.show(this);
});

$(function(){
	$("#form1").initValidform();
	
	$("#wechat_ssl_cer").change(function(){
		$("#filecer").val($("#wechat_ssl_cer").val());
    });	
    
    $("#wechat_ssl_key").change(function(){
		$("#filekey").val($("#wechat_ssl_key").val());
    });
});
</script>
</body>
</html>