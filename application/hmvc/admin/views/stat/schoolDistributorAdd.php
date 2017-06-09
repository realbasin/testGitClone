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
<link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->
</head>
<body class="mainbody">
<div class="location">
  <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span>行长统计</span>
  <i class="arrow"></i>
  <span><a href="<?php echo adminUrl('stat_distributor','schoolDistributor');?>">行长列表</a></span>
  <i class="arrow"></i>
  <span>新增行长</span>
</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><em>*</em>用户名称</label>
        </dt>
        <dd class="opt">
          <input type="text" name="user_name" id="user_name" maxlength="20" class="input-txt" value="" ajaxurl="<?php echo adminUrl('stat_distributor','schoolDistributor_userName_Verify');?>" datatype="*" errormsg="系统中存在相同的用户" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic">用户名称由任意字符组成</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>电子邮件</label>
        </dt>
        <dd class="opt">
          <input type="text" class="input-txt" name="email" id="email" ignore="ignore" datatype="e"  sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic">用户的电子邮件地址</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>手机号码</label>
        </dt>
        <dd class="opt">
          <input type="text" class="input-txt" name="mobile" id="mobile" ignore="ignore" datatype="m" sucmsg=" " ajaxurl="<?php echo adminUrl('stat_distributor','schoolDistributor_mobile_Verify');?>" errormsg="手机号码错误或系统中存在相同的手机号码">
          <span class="Validform_checktip"></span>
          <p class="notic">用户的手机号码</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>会员密码</label>
        </dt>
        <dd class="opt">
          <input type="password" class="input-txt" name="user_pwd" id="user_pwd"  datatype="*6-16" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic">请填写一个6-16位的会员密码</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>重复密码</label>
        </dt>
        <dd class="opt">
          <input type="password" class="input-txt" name="user_pwd1" id="user_pwd1" recheck="user_pwd" datatype="*6-16"  sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic">与填写的会员密码相同</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>是否启用</label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="is_effect" id="is_effect"  />
          <p class="notic">是否启用该用户</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>归属业务员</label>
        </dt>
        <dd class="opt">
          <select id="admin_id" name="admin_id" >
         	<?php foreach($agents as $k=>$v){?>
         		<option value="<?php echo $k;?>"><?php echo $v['real_name']?$v['real_name']:$v['agent_name'];?></option>
         	<?php }?>
         </select>
          <p class="notic">请选择行长所归属的业务员</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>真实姓名</label>
        </dt>
        <dd class="opt">
          <input type="text" class="input-txt" maxlength="20" name="real_name" id="real_name" datatype="*" sucmsg=" "  >
          <span class="Validform_checktip"></span>
          <p class="notic">用户的真实姓名</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>身份证号码</label>
        </dt>
        <dd class="opt">
          <input type="text" class="input-txt" maxlength="18" name="idno" id="idno" datatype="/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/" sucmsg=" "  ajaxurl="<?php echo adminUrl('stat_distributor','schoolDistributor_idno_Verify');?>" errormsg="身份证号码错误或系统中存在相同的身份证号码">
          <span class="Validform_checktip"></span>
          <p class="notic">用户的身份证号码</p>
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
 $("#form1").initValidform();
	
	$('#syshelp').on("click",function(){
	var d = dialog({
        content: "<li>添加一个新的行长用户",
        quickClose: true
        });
       d.show(this);
});

</script>
</body>
</html>