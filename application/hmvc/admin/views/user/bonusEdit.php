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
<div class="page">
    <form method="post" id="form1" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="form-default">
            <dl class="row">
                <dt class="tit">
                    <label>优惠券类型名称</label>
                </dt>
                <dd class="opt">
                    <label><?php echo $bonusType['bonus_type_name'];?></label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>优惠券面额</label>
                </dt>
                <dd class="opt"><?php echo $bonusRule['money'];?> 元</dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>最低使用金额</label>
                </dt>
                <dd class="opt"><?php echo $bonusRule['limit_amount'];?> 元</dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>每次派送数量</label>
                </dt>
                <dd class="opt"><?php echo $bonusRule['num'];?> 张</dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>普通标适用月份</label>
                </dt>
                <dd class="opt"><?php echo str_replace(',', '个月，', $bonusRule['use_deal_month']).'个月';?></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>债权标是否适用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="use_deal_load" id="use_deal_load" value="1" <?php if($bonusRule['use_deal_load']) echo 'checked';?> />
                    <p class="notic">（适用范围，继承普通标适用月份）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>是否启用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_effect" id="is_effect" value="1" <?php if($bonusRule['is_effect']) echo 'checked';?> />
                </dd>
            </dl>
        </div>
        <div class="page-footer">
            <div class="btn-wrap">
                <input type="hidden" name="rule_id" value="<?php echo $bonusRule['id'];?>" />
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