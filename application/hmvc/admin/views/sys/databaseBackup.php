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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
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
  <span><?php echo \Core::L('database_backup');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('backup_name');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="backup_name" size="100" id="backup_name" class="input-txt" datatype="/^[A-Za-z0-9]{1,30}$/" sucmsg=" "  errormsg="<?php echo \Core::L('backup_name_error');?>" value="<?php echo date('YmdHis', time());?>">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('backup_name_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('backup_volume_size');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="backup_volume_size" size="100" id="backup_volume_size" class="input-txt" datatype="/^([2-9][0-9]|[1-9][0-9]{2,3})$/" sucmsg=" "  errormsg="<?php echo \Core::L('backup_volume_size_error');?>" value="2048">
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('backup_volume_size_notice');?></p>
        </dd>
      </dl>
      </div>
            <div class="form-all">
      <div class="title">
        <h3><?php echo \Core::L('backup_tables');?></h3>
      </div>
      <dl class="row">
        <dd>
        	<?php if(isset($tableslist)){?>
        		<div class="account-container">
        			<h4>
        				 <input id="checkAll" class="checkbox" type="checkbox" checked="checked"><label for="checkAll"><?php echo \Core::L("checkall")?></label>
        			</h4>
        			 <ul class="account-container-list">
        			 <?php foreach($tableslist as $v){?>
        			 	<li>
        			 		<input class="checkbox" type="checkbox" value="<?php echo $v;?>" name="table[]" ltype='subchk' checked="checked"><?php echo $v;?>
        			 	</li>
        			 	<?php }?>
        			 </ul>
        		</div>
        		<?php }?>
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
	var help_content="<?php echo \Core::L('backup_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};

 $('#checkAll').click(function(){
        $('input[ltype="subchk"]').attr("checked",this.checked);
});
$(function () {
	 $("#form1").initValidform();
	});
</script>
</body>
</html>