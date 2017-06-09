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
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="ids" value="<?php echo $ids;?>" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label>行长名称</label>
        </dt>
        <dd class="opt">
           <?php foreach($users as $k=>$v){?>
       <a href="javascript:void(null);" class="input-btn mr5 mb10"><?php echo $v['user_name']?>
       	<?php echo $v['real_name']?'('.$v['real_name'].')':''?>
       </a>
        <?php }?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>归属</label>
        </dt>
        <dd class="opt">
         <select id="admin_id" name="admin_id" >
         	<?php foreach($agents as $k=>$v){?>
         		<option value="<?php echo $k;?>"><?php echo $v['real_name']?$v['real_name']:$v['agent_name'];?></option>
         	<?php }?>
         </select>
          <p class="notic">请选择一个归属业务员</p>
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
 $(function () {
	 $("#form1").initValidform();
	});
</script>
</body>
</html>