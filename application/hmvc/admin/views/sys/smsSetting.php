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
  <span><?php echo \Core::L('setting');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('sms_setting');?></span>
  
</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_type');?></label>
        </dt>
        <dd class="opt">
        	<select id="sms_type" name="sms_type" datatype="*" sucmsg=" " nullmsg="<?php echo \Core::L('sms_type_null')?>">
        		<option value="">请选择短信服务商...</option>
        		<option value="wdkj">沃动科技</option>
        	</select>
        	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('sms_type_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_sign');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="sms_sign" id="sms_sign" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("sms_sign_null");?>"  sucmsg=" "  value="<?php echo C('sms_sign');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('sms_sign_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_sign_location');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="sms_sign_location" id="sms_sign_location" <?php if(C('sms_sign_location')) echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('sms_sign_location_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_user_id');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="sms_user_id" id="sms_user_id" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("sms_user_id_null");?>"  sucmsg=" "  value="<?php echo C('sms_user_id');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('sms_user_id_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_acount');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="sms_acount" id="sms_acount" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("sms_acount_null");?>"  sucmsg=" "  value="<?php echo C('sms_acount');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('sms_acount_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_password');?></label>
        </dt>
        <dd class="opt">
          <input type="password" name="sms_password" id="sms_password" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("sms_password_null");?>"  sucmsg=" "  value="<?php echo \Core::decrypt(C('sms_password'));?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('sms_password_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sms_test');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="sms_test" id="sms_test" class="input-txt"> 
          	<input type="button" value="<?php echo \Core::L('test')?>" name="send_sms" class="input-btn" id="send_sms">
          <p class="notic"><?php echo \Core::L('sms_test_notice');?></p>
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
	var help_content="<?php echo \Core::L('sms_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
$(function () {
	 $("#form1").initValidform();
	 
	 $('#sms_type').val('<?php echo C('sms_type');?>');	
	 
	 $('#send_sms').click(function(){
		$.ajax({
			type:'POST',
			url:'<?php echo adminUrl("sys_setting","sms_test");?>',
			data:'sms_type='+$('#sms_type').val()+'&sms_user_id='+$('#sms_user_id').val()+'&sms_acount='+$('#sms_acount').val()+'&sms_password='+$('#sms_password').val()+'&sms_sign='+$('#sms_sign').val()+'&sms_sign_location='+$('#sms_sign_location').val()+'&sms_test='+$('#sms_test').val(),
			error:function(){
					jsprint('<?php echo \Core::L("sms_test_error");?>');
				},
			success:function(data){
				if(data.code==200){
					jsdialog('系统提示','<?php echo \Core::L("sms_test_success");?><br><br>'+data.message,'');
				}else{
					jsdialog('系统提示','<?php echo \Core::L("sms_test_fail");?><br><br>'+data.message,'');
				}
			},
			dataType:'json'
		});
	});
    });
</script>
</body>
</html>