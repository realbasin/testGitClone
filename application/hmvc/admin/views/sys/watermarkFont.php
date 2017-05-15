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
  <span><?php echo \Core::L('watermark_font_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
 <div class="tab-bar">
    <div class="tab-title">
    	<div class="subject">  </div>
    	<?php if(isset($pagetabs)) echo $pagetabs;?>
    </div>
  </div>
  <form enctype="multipart/form-data"  method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L("upload_watermark_text_font_all")?></label>
        </dt>
        <dd class="opt">
        	<?php foreach($fonts as $v){?>
         <div class="fontlist">
         	<img src="/resource/admin/images/font.png" width="40px" align="center"><br>
         		<?php if($v['sys']){?>
         		<a><?php echo \Core::L('sys_font')?></a>
         		<?php }else{?>
         		<a href="javascript:deleteFont(<?php echo $v['font_id']?>)"><?php echo \Core::L("delete")?></a>
         		<?php }?>
         		<br>
         	<?php echo $v['font_name']?>
         </div>
         <?php }?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('upload_watermark_text_font_name');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="upload_watermark_text_font_name" id="upload_watermark_text_font_name" class="input-txt" datatype='*1-30' nullmsg='<?php echo \Core::L("upload_watermark_text_font_name_notice_null");?>' sucmsg=" ">
          <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('upload_watermark_text_font_name_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('upload_watermark_text_font');?></label>
        </dt>
        <dd class="opt">
        	<div class="input-file-show" style="padding-left: 10px;"><span class="type-file-box">
        	<input onfocus="this.blur();" type='text' name='filetext' id='filetext' class='type-file-text' datatype="/.*ttf$/" sucmsg=" " nullmsg="<?php echo \Core::L('upload_watermark_text_font_error');?>" errormsg="<?php echo \Core::L('upload_watermark_text_font_error');?>"><input type='button' name='button' id='button' value='<?php echo \Core::L('upload');?> ...' class='type-file-button' />
            <input class="type-file-file" id="watermark_text_font" name="watermark_text_font[]" type="file" size="30" hidefocus="true" dz_type="add_watermark_text_font">
            </span></div>
            <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('upload_watermark_text_font_notice');?></p>
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
	function deleteFont(id){
		var d = dialog({
	title: '<?php echo \Core::L("tip") ?>',
	content: '<?php echo \Core::L("delete_watermark_confirm") ?>',
	okValue: '<?php echo \Core::L("ok") ?>',
	ok: function () {
		this.title('<?php echo \Core::L("progress") ?>');
		location.href="<?php echo \Core::getUrl('sys_setting','watermark_font_del',\Core::config()->getAdminModule()).'&id='?>"+"&id="+id;
	},
	cancelValue: '<?php echo \Core::L("cancel") ?>',
	cancel: function () {}
});
d.showModal();
	}


	var help_content="<?php echo \Core::L('upload_watermark_text_font_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};

$(function () {
	 $("#form1").initValidform();
 
    $("#watermark_text_font").change(function(){
	$("#filetext").val($("#watermark_text_font").val());
    });	
    });
</script>
</body>
</html>