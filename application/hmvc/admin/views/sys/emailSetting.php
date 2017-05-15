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
  <span><?php echo \Core::L('email_setting');?></span>
</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_server');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="smtp_server" id="smtp_server" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("smtp_server_null");?>" sucmsg=" "  value="<?php echo C('smtp_server');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('smtp_server_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_port');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="smtp_port" id="smtp_port" class="input-txt" datatype="n" nullmsg="<?php echo \Core::L("smtp_port_null");?>" errormsg="<?php echo \Core::L("smtp_port_error");?>" sucmsg=" "  value="<?php echo C('smtp_port');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('smtp_port_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_protocol');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="smtp_protocol" id="smtp_protocol" <?php if(C('smtp_protocol')) echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('smtp_protocol_notice');?></p>
        </dd>
      </dl>
     <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_send');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="smtp_send" id="smtp_send" class="input-txt" datatype="e" nullmsg="<?php echo \Core::L("smtp_send_null");?>" errormsg="<?php echo \Core::L("smtp_send_error");?>" sucmsg=" " value="<?php echo C('smtp_send');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('smtp_send_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_user');?></label>
        </dt>
        <dd class="opt">
          <input type="text" autocomplete="off"   name="smtp_user" id="smtp_user" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("smtp_user_null");?>"   sucmsg=" " value="<?php echo C('smtp_user');?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('smtp_user_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_password');?></label>
        </dt>
        <dd class="opt">
          <input type="password" autocomplete="off" name="smtp_password" id="smtp_password" class="input-txt" datatype="*" nullmsg="<?php echo \Core::L("smtp_password_null");?>"   sucmsg=" " value="<?php echo \Core::decrypt(C('smtp_password'));?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('smtp_password_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('smtp_test');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="smtp_test" id="smtp_test" class="input-txt"> 
          	<input type="button" value="<?php echo \Core::L('test')?>" name="send_email" class="input-btn" id="send_email">
          <p class="notic"><?php echo \Core::L('smtp_test_notice');?></p>
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
	var help_content="<?php echo \Core::L('smtp_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
$(function () {
	 $("#form1").initValidform();
	 
	 $('#send_email').click(function(){
	 	 var dia = dialog({ content: '<?php echo \Core::L("smtp_test_success_process");?>' }).showModal();
		$.ajax({
			type:'POST',
			url:'<?php echo adminUrl("sys_setting","email_test");?>',
			data:'smtp_server='+$('#smtp_server').val()+'&smtp_port='+$('#smtp_port').val()+'&smtp_protocol='+$('#smtp_protocol').val()+'&smtp_send='+$('#smtp_send').val()+'&smtp_user='+$('#smtp_user').val()+'&smtp_password='+$('#smtp_password').val()+'&smtp_test='+$('#smtp_test').val(),
			error:function(){
				   dia.close().remove();
					jsprint('<?php echo \Core::L("smtp_test_error_ajax");?>');
				},
			success:function(data){
				if(data.code==200){
					dia.close().remove();
					jsprint('<?php echo \Core::L("smtp_test_success");?>');
				}else{
					dia.close().remove();
					jsprint('<?php echo \Core::L("smtp_test_error");?>');
				}
			},
			dataType:'json'
		});
	});
    });
</script>
</body>
</html>