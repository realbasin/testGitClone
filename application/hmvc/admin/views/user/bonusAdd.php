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
                    <label><em>*</em>优惠券面额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="money" id="money" value="" datatype="n" nullmsg="不能为空" errormsg="只能为数字" sucmsg=" " > 元
                    <span class="Validform_checktip"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>最低使用金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="limit_amount" value="" datatype="/(\d+)/" nullmsg="不能为空" errormsg="只能为数字" sucmsg=" "> 元
                    <span class="Validform_checktip"></span>
                    <p class="notic">（如果为空，等同现金券）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>每次派送数量</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="num" value="" datatype="/(\d+)/" nullmsg="不能为空" errormsg="只能为数字" sucmsg=" " > 张
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>普通标适用月份</label>
                </dt>
                <dd class="opt">
                    <label><input name="use_deal_month[]" type="checkbox" value="1" />1月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="2" />2月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="3" />3月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="4" />4月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="5" />5月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="6" />6月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="7" />7月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="8" />8月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="9" />9月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="10" />10月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="11" />11月</label>
                    <label><input name="use_deal_month[]" type="checkbox" value="12" />12月</label>
                    <p class="notic">（必选，提交后不可编辑）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>债权标是否适用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="use_deal_load" id="use_deal_load" value="1" checked />
                    <p class="notic">（适用范围，继承普通标适用月份）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>是否启用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_effect" id="is_effect" value="1" checked />
                </dd>
            </dl>
        </div>
        <div class="page-footer">
            <div class="btn-wrap">
                <input type="hidden" name="type_id" value="<?php echo $bonusType['id'];?>" />
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