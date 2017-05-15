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
<link href="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	  <div  class="right"><a href="javascript:void(null);" onclick="help(this);"  onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('loan');?></span>
  <i class="arrow"></i>
  <span><a href="<?php echo adminUrl('loan_loan','index');?>"><?php echo \Core::L('loan_all');?></a></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('loan_add');?></span>
</div>
<div class="line10"></div>
<div class="page">
<div id="floatHead" class="content-tab-wrap">
  <div class="content-tab">
    <div class="content-tab-ul-wrap">
      <ul>
        <li><a class="selected" href="javascript:;">基本信息</a></li>
        <li><a href="javascript:;">相关参数</a></li>
        <li><a href="javascript:;">物品抵押</a></li>
        <li><a href="javascript:;">相关资料</a></li>
        <li><a href="javascript:;">SEO</a></li>
      </ul>
    </div>
  </div>
</div>
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="tab-content">
      	<dl class="row">
        <dt class="tit">
          <label><em>*</em>借款编号</label>
        </dt>
        <dd class="opt">
          <input type="text" name="deal_sn" id="deal_sn" class="input-txt" value="">
          <p class="notic">用于合同处的借款编号，不能重复</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>借款名称</label>
        </dt>
        <dd class="opt">
          <input type="text" name="name" id="name" class="input-txt" value="">
          <p class="notic">借款名称</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>简短名称</label>
        </dt>
        <dd class="opt">
          <input type="text" name="sub_name" id="sub_name" class="input-txt" value="">
          <p class="notic">用户邮件、短信的名称显示，字数不能超过20</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>会员</label>
        </dt>
        <dd class="opt">
          <input type="text" name="user_name" id="user_name" class="input-txt" value="">
          <input type="hidden" name="user_id" id="user_id" value="">
          <p class="notic">填写名称，系统会自动验证</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>借款分类</label>
        </dt>
        <dd class="opt">
        	<select name="cate_id" id="cate_id" value="-1">
        		<option value="-1">-请选择分类-</option>
        		<?php if($dealcate){?>
        			<?php foreach($dealcate as $k=>$v){?>
        				<?php echo "<option value='".$k."'>".$v['name']."</option>";?>
        			<?php }?>
        		<?php }?>
        	</select>
        	<p class="notic">借款投标分类</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>借款类型</label>
        </dt>
        <dd class="opt">
        	<select name="type_id" id="type_id" value="-1">
        		<option value="-1">-请选择类型-</option>
        		<?php if($dealloantype){?>
        			<?php foreach($dealloantype as $k=>$v){?>
        				<?php echo "<option value='".$k."'>".$v['name']."</option>";?>
        			<?php }?>
        		<?php }?>
        	</select>
        	<p class="notic">借款类型</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em>还款方式</label>
        </dt>
        <dd class="opt">
        	<select name="loantype" id="loantype" value="-1">
        		<option value="-1">-请选择还款方式-</option>
        		<?php if($loantype){?>
        			<?php foreach($loantype as $k=>$v){?>
        				<?php echo "<option value='".$k."'>".$v."</option>";?>
        			<?php }?>
        		<?php }?>
        	</select>
        	<p class="notic">借款用户的还款方式</p>
        </dd>
      </dl>
      </div>
     <div class="tab-content" style="display: none;">
     	2
     </div>
     <div class="tab-content" style="display: none;">
     	3
     </div>
     <div class="tab-content" style="display: none;">
     	4
     </div>
     <div class="tab-content" style="display: none;">
     	5
     </div>
 <div class="page-footer">
  <div class="btn-wrap">
    <input type="submit" name="btnSubmit" value="<?php echo \Core::L('submit');?>" id="btnSubmit" class="btn" />
  </div>
</div>
  </form>
</div>
<script type="text/javascript">
	var help_content="<?php echo \Core::L('loan_add_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
//用户名自动补全
$('#user_name').on('focus',function(){
	var obj = $(this);
	    obj.autocomplete("<?php echo adminUrl('common','autoGetUsers')?>", {
	    	width:288,
			selectFirst: false,
			autoFill: false,    //自动填充
			dataType: "json",
			parse: function(data) {
				return $.map(data, function(row) {
					return {
						data: row,
						value: row.user_name,
						result: function(){
							if (row.id > 0)
								return row.user_name;
							else
								return "";
						}
					}
				});
			},
			formatItem: function(row, i, max) {
				return row.user_name + (row.real_name =="" ? "" : " [" + row.real_name + "]");
			}
		}).result(function(e,item) {
			$('#user_id').val(item.id);
			return item.id;
		});
});
$(function () {
        //初始化表单验证
        $("#form1").initValidform();
    });
</script>
</body>
</html>