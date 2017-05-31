<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title><?php echo \Core::L('site_setting');?></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->
    <!--[if IE]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5shiv.min.js"></script>
    <![endif]-->
</head>
<body class="mainbody">
<div class="location">
    <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
    <i class="home"></i>
    <span>优惠券管理</span>
    <i class="arrow"></i>
    <span><?php if ($func_name=='do_type_add'){echo '添加';}else{echo '编辑';}?>优惠券类型</span>
</div>
<div class="line10"></div>
<div class="page">
    <form method="post" id="form1" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="form-default">
            <dl class="row">
                <dt class="tit">
                    <label>优惠券类型名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="bonus_type_name" id="bonus_type_name" class="input-txt" value="<?php echo isset($bonusType['bonus_type_name']) ? trim($bonusType['bonus_type_name']) : '';?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>资产类别</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="use_type" id="use_type_1" value="1" <?php if(empty($bonusType) || $bonusType['use_type']==1) echo "checked"; ?>><label for="use_type_1">理财端</label>&nbsp;&nbsp;
                    <input type="radio" name="use_type" id="use_type_2" value="2" <?php if(!empty($bonusType) && $bonusType['use_type']==2) echo "checked";?>><label for="use_type_2">借款端</label>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>使用限定</label>
                </dt>
                <dd class="opt">
                    <select name="is_limited"><option value="1" <?php if(!empty($bonusType) && $bonusType['is_limited']==1) echo "selected";?>>只能使用一次</option></select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>发放/领取开始时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="start_time" id="start_time" value="<?php if(!empty($bonusType)) echo $bonusType['start_time'];?>">
                    <p class="notic">（如果为空，则马上开始）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>发放/领取结束时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="end_time" id="end_time" value="<?php if(!empty($bonusType)) echo toDate($bonusType['end_time']);?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>开始使用时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" class="input-txt" name="use_start_time" id="use_start_time" value="<?php if(!empty($bonusType)) echo $bonusType['use_start_time'];?>">
                    <p class="notic">（如果为空，则马上开始）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>结束使用时间</label>
                </dt>
                <dd class="opt">
                    <select name="use_end_time_type" id="use_end_time_type">
                        <option value="1" <?php if(empty($bonusType) || $bonusType['use_end_time_type']==1) echo 'selected';?>>设定固定日期</option>
                        <option value="2" <?php if(!empty($bonusType) || $bonusType['use_end_time_type']==2) echo 'selected';?>>激活后有效期</option>
                    </select>
                    <input type="text" class="input-txt" name="use_end_time" id="use_end_time" value="<?php if(!empty($bonusType)) echo toDate($bonusType['use_end_time']);?>" />
                    <input type="text" name="use_end_day" id="use_end_day" style="width: 130px;" value="<?php if(!empty($bonusType)) echo $bonusType['use_end_day'];?>"/>
                    <p class="notic">（类型为"激活后有效期"，直接输入天数即可）</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>发放方式</label>
                </dt>
                <dd class="opt">
                    <select name="send_type">
                        <?php
                            foreach ($sendTypeList as $k=>$type) {
                        ?>
                        <option value="<?php echo $k;?>" <?php if(!empty($bonusType) && $bonusType['send_type']==$k) echo 'selected';?>><?php echo $type;?></option>
                        <?php
                            }
                        ?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>是否启用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_effect" id="is_effect" value="1" <?php if(!empty($bonusType) && $bonusType['is_effect']==1) echo 'checked';?>/>
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
    $.datetimepicker.setLocale('ch');
    $('#start_time,#end_time,#use_start_time,#use_end_time').datetimepicker({format:"Y-m-d H:i:s",timepicker:false,todayButton:false});
    $(document).ready(function () {
        // 初始化载入值
        var select_type = $('#use_end_time_type').val();
        if (select_type == 1) {
            $('#use_end_time').show();
            $('#use_end_day').hide();
        } else {
            $('#use_end_time').val('').hide();
            $('#use_end_day').show();
        }
        $('#use_end_time_type').change(function () {
            var select_type = $(this).val();
            if (select_type == 1) {
                $('#use_end_time').show();
                $('#use_end_day').hide();
            } else {
                $('#use_end_time').val('').hide();
                $('#use_end_day').show();
            }
        });
    });
</script>
</body>
</html>
