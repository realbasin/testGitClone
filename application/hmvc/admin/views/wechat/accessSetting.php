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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>clipboard.min.js"></script>
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
  <span><?php echo \Core::L('wechat_access_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_url');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_url" id="wechat_url" class="input-txt" readonly="readonly" value="<?php echo C('wechat_url');?>"> <input type="button" class="input-btn" id="btn-url" data-clipboard-target="#wechat_url" value="<?php echo \Core::L('copy');?>">
          <p class="notic"><?php echo \Core::L('wechat_url_notice');?></p>
        </dd>
    </dl>
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_app_id');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_app_id" id="wechat_app_id" class="input-txt" value="<?php echo C('wechat_app_id');?>">
          <p class="notic"><?php echo \Core::L('wechat_app_id_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_app_secret');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_app_secret" id="wechat_app_secret" class="input-txt" value="<?php echo C('wechat_app_secret');?>">
          <p class="notic"><?php echo \Core::L('wechat_app_secret_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_token');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_token" id="wechat_token" class="input-txt" value="<?php echo C('wechat_token');?>"> <input type="button" class="input-btn" id="btn-token" value="<?php echo \Core::L('generate');?>"> <input type="button" class="input-btn" id="btn-token-copy" data-clipboard-target="#wechat_token" value="<?php echo \Core::L('copy');?>">
          <p class="notic"><?php echo \Core::L('wechat_token_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('wechat_aes_key');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="wechat_aes_key" id="wechat_aes_key" class="input-txt" value="<?php echo C('wechat_aes_key');?>"> <input type="button" class="input-btn" id="btn-aeskey" value="<?php echo \Core::L('generate');?>"> <input type="button" class="input-btn" id="btn-aeskey-copy" data-clipboard-target="#wechat_aes_key" value="<?php echo \Core::L('copy');?>">
          <p class="notic"><?php echo \Core::L('wechat_aes_key_notice');?></p>
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
        content: "<?php echo \Core::L('wechat_access_help');?>",
        quickClose: true
        });
       d.show(this);
});

$('#btn-url,#btn-token-copy,#btn-aeskey-copy').on('click',function(){
	jsprint(lang['copy_to_clipboard_success']);
});

$('#btn-token').on('click',function(){
	$('#wechat_token').val(randomString(32));
});

$('#btn-aeskey').on('click',function(){
	$('#wechat_aes_key').val(randomString(43));
});

function randomString(len) {
var d,
e,
b ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
c ="";
for (d = 0; len > d; d += 1)
e = Math.random() * b.length, e = Math.floor(e), c += b.charAt(e);
return c
}

$(function(){
	$('#wechat_url').val('http://'+document.domain+'/?c=wechat&m=api');
	new Clipboard('.input-btn');
});
</script>
</body>
</html>