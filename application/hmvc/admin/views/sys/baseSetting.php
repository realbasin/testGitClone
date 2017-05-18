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
  <span><?php echo \Core::L('base_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
  <form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="form-default">
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('site_name');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="site_name" id="site_name" class="input-txt" value="<?php echo C('site_name');?>">
          <p class="notic"><?php echo \Core::L('site_name_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('site_icp');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="site_icp" id="site_icp" class="input-txt" value="<?php echo C('site_icp');?>">
          <p class="notic"><?php echo \Core::L('site_icp_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>是否使用url重写模式</label>
        </dt>
        <dd class="opt">
       <input type="checkbox" class="js-switch blue" name="url_model" id="url_model" <?php if(C('url_model')) echo 'checked';?> />
          <p class="notic">是否启用url重写模式</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('time_zone');?></label>
        </dt>
        <dd class="opt">
        <select name="time_zone" id="time_zone" value="<?php echo C('time_zone');?>">
	<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
            <option value="-11">(GMT -11:00) Midway Island, Samoa</option>
            <option value="-10">(GMT -10:00) Hawaii</option>
            <option value="-9">(GMT -09:00) Alaska</option>
            <option value="-8">(GMT -08:00) Pacific Time (US &amp; Canada), Tijuana</option>
            <option value="-7">(GMT -07:00) Mountain Time (US &amp; Canada), Arizona</option>
            <option value="-6">(GMT -06:00) Central Time (US &amp; Canada), Mexico City</option>
            <option value="-5">(GMT -05:00) Eastern Time (US &amp; Canada), Bogota, Lima, Quito</option>
            <option value="-4">(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz</option>
            <option value="-3.5">(GMT -03:30) Newfoundland</option>
            <option value="-3">(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is</option>
            <option value="-2">(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena</option>
            <option value="-1">(GMT -01:00) Azores, Cape Verde Islands</option>
            <option value="0">(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia</option>
            <option value="1">(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome</option>
            <option value="2">(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa</option>
            <option value="3">(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi</option>
            <option value="3.5">(GMT +03:30) Tehran</option>
            <option value="4">(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi</option>
            <option value="4.5">(GMT +04:30) Kabul</option>
            <option value="5">(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
            <option value="5.5">(GMT +05:30) Bombay, Calcutta, Madras, New Delhi</option>
            <option value="5.75">(GMT +05:45) Katmandu</option>
            <option value="6">(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk</option>
            <option value="6.5">(GMT +06:30) Rangoon</option>
            <option value="7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
            <option value="8">(GMT +08:00) Beijing, Hong Kong, Perth, Singapore, Taipei</option>
            <option value="9">(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk</option>
            <option value="9.5">(GMT +09:30) Adelaide, Darwin</option>
            <option value="10">(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok</option>
            <option value="11">(GMT +11:00) Magadan, New Caledonia, Solomon Islands</option>
            <option value="12">(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island</option>
</select>
          <p class="notic"><?php echo \Core::L('time_zone_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="statistics_code"><?php echo \Core::L('statistics_code');?></label>
        </dt>
        <dd class="opt">
          <textarea name="statistics_code" rows="6" class="tarea" id="statistics_code"><?php echo C('statistics_code');?></textarea>
          <p class="notic"><?php echo \Core::L('statistics_code_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('sys_log');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch blue" name="sys_log" id="sys_log" <?php if(C('sys_log')) echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('sys_log_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('maintain_mode');?></label>
        </dt>
        <dd class="opt">
          <input type="checkbox" class="js-switch red" name="maintain_mode" id="maintain_mode" <?php if(C('maintain_mode')) echo 'checked';?> />
          <p class="notic"><?php echo \Core::L('maintain_mode_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('maintain_mode_white');?></label>
        </dt>
        <dd class="opt">
          <input type="text" name="maintain_mode_white" id="maintain_mode_white" class="input-txt" value="<?php echo C('maintain_mode_white');?>">
          <p class="notic"><?php echo \Core::L('maintain_mode_white_notice');?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('maintain_mode_tip');?></label>
        </dt>
        <dd class="opt">
        	<textarea name="maintain_mode_tip" rows="6" class="tarea" id="maintain_mode_tip"><?php echo C('maintain_mode_tip');?></textarea>
          <p class="notic"><?php echo \Core::L('maintain_mode_tip_notice');?></p>
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
	var help_content="<?php echo \Core::L('base_setting_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
$('#time_zone').val('<?php echo C('time_zone');?>');
</script>
</body>
</html>