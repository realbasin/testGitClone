<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L('permission_setting');?></title>
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
  <span><a href="<?php echo adminUrl('sys_setting','permission');?>"><?php echo \Core::L('permission_setting');?></a></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('add,permission_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('auth_name');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="gname" id="gname" class="input-txt" value="" datatype="*" nullmsg="<?php echo \Core::L("auth_name_null");?>" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('auth_name_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('auth_info');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="info" id="info" class="input-txt" value="" datatype="*" nullmsg="<?php echo \Core::L("auth_info_null");?>" sucmsg=" "  >
          	<span class="Validform_checktip"></span>
          <p class="notic"><?php echo \Core::L('auth_info_notice');?></p>
        </dd>
      </dl>
      </div>
       <div class="form-all">
      <div class="title">
        <h3><?php echo \Core::L('auth_choose');?></h3>
      </div>
      <dl class="row">
        <dt class="tit">
          <span><input class="checkbox" type="checkbox" id="checkAll"><label for="checkAll"><?php echo \Core::L("checkall")?></label></span></dt>
        <dd>
        	<?php if(isset($authlist)){?>
        	<?php foreach($authlist as $v){?>
        		<div class="account-container">
        			<h4>
        				 <input class="checkbox" type="checkbox" ltype="groupAll"><?php echo $v['text']?>
        			</h4>
        			 <ul class="account-container-list">
        			 	<?php foreach($v['list'] as $ls){?>
        			 	<li>
        			 		<input class="checkbox" type="checkbox" value="<?php echo $ls['link']?>" name="permission[]"><?php echo $ls['text']?>
        			 	</li>
        			 	<?php }?>
        			 </ul>
        		</div>
        	<?php }?>
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
$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<?php echo \Core::L('auth_list_help');?>",
        quickClose: true
        });
       d.show(this);
});

 $('#checkAll').click(function(){
    	$('input[type="checkbox"]').attr('checked',$(this).attr('checked') == 'checked');
    });
 $('input[ltype="groupAll"]').click(function(){
        $(this).parents('h4:first').next().find('input[type="checkbox"]').attr('checked',$(this).attr('checked') == 'checked');
    });
    
 $(function () {
	 $("#form1").initValidform();
	});
</script>
</body>
</html>