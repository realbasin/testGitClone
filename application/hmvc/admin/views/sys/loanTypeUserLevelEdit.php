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
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->

</head>
<body class="mainbody">
<div class="location">
    <div  class="right"><a href="javascript:void(null);" onclick="help(this);"  onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
    <i class="home"></i>
    <span>贷款设置</span>
    <i class="arrow"></i>
    <span>修改用户等级</span>

</div>
<div class="line10"></div>
<div class="page">
    <form method="post" id="form1" action="?c=sys_loan&a=loan_type_user_level_edit&m=admin" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="id" value="<?php echo $userLevel['id'] ?>" />
        <input type="hidden" name="loan_type_id" value="<?php echo $userLevel['loan_type_id'] ?>" />
        <div class="form-default">
            <dl class="row">
                <dt class="tit">
                    <label>等级名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="name" class="input-txt" disabled="disabled" value="<?php echo $userLevel['name'] ?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>所需信用积分</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="point" class="input-txt" disabled="disabled" value="<?php echo $userLevel['point'] ?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>服务费用</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="services_fee" class="input-txt" value="<?php echo $userLevel['services_fee'] ?>">%
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款期限</label>
                </dt>
                <dd class="opt">
                    <div id="repay_time_div">
                        <?php foreach($userLevel['repaytime_items'] as $key => $item){ ?>
                            <div class="repay_time_wrap" data-index="<?php echo $key; ?>">
                                期限：<input type="text" name="repaytime[<?php echo $key; ?>][deadline]" style="width: 80px;" value="<?php echo $item['deadline']; ?>">
                                <select name="repaytime[<?php echo $key; ?>][deadline_type]">
                                    <option value="1" <?php if($item['deadline_type'] == 1) echo 'selected'; ?>>月</option>
                                    <option value="0" <?php if($item['deadline_type'] == 0) echo 'selected'; ?>>天</option>
                                </select>
                                最小利率：<input type="text" name="repaytime[<?php echo $key; ?>][min_rate]" style="width: 80px;" value="<?php echo $item['min_rate']; ?>">
                                最大利率：<input type="text" name="repaytime[<?php echo $key; ?>][max_rate]" style="width: 80px;" value="<?php echo $item['max_rate']; ?>">
                                <input type="button" class="input-btn" value="删除" onclick="repay_time_wrap_del(this);" />
                            </div>
                        <?php } ?>
                    </div>
                    <input type="button" class="input-btn" value="增加" onclick="repay_time_wrap_add();" />
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="statistics_code">筹款期限</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="enddate" class="input-txt" value="<?php echo $userLevel['enddate'] ?>">
                    <p class="notic">多个用,隔开</p>
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


    function repay_time_wrap_add(){
        var maxIndex = 0;
        $.each($('.repay_time_wrap'),function(i,obj){
            var currentIndex = $(obj).data('index');
            if(currentIndex > maxIndex){
                maxIndex = currentIndex;
            }
        });

        maxIndex += 1;

        var html = '<div class="repay_time_wrap" data-index="'+maxIndex+'">'+
            '期限：<input type="text" name="repaytime['+maxIndex+'][deadline]" style="width: 80px;" value="">'+
        '<select name="repaytime['+maxIndex+'][deadline_type]">'+
            '<option value="1">月</option>'+
        '<option value="0">天</option>'+
        '</select>'+
        '最小利率：<input type="text" name="repaytime['+maxIndex+'][min_rate]" style="width: 80px;" value="">'+
        '最大利率：<input type="text" name="repaytime['+maxIndex+'][max_rate]" style="width: 80px;" value="">'+
        '<input type="button" class="input-btn" value="删除" onclick="repay_time_wrap_del(this);" />'+
        '</div>';
        $('#repay_time_div').append(html);
    }

    function repay_time_wrap_del(obj){
        $(obj).parent().remove();
    }
</script>
</body>
</html>