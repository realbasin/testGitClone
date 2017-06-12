<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/jquery.daterangepicker.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <!--[if IE]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5shiv.min.js"></script>
    <![endif]-->
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->

</head>
<body class="mainbody">
<div class="location">
    <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
    <i class="home"></i>
    <span><?php echo \Core::L('setting');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('variables_setting');?></span>

</div>
<div class="line10"></div>
<div class="page">
    <div class="form-default">
        <form method="post" id="form1" name="form1">
            <input type="hidden" name="form_submit" value="ok" />
            <div class="title">
                <div style="margin-bottom: 5px;">
                    资产类别
                    <select name='use_type'>
                        <option value="1" <?php if($use_type==1) echo 'selected';?>>理财端</option>
                        <option value="2" <?php if($use_type==2) echo 'selected';?>>借款端</option>
                    </select>
                    优惠券类型名称
                    <label><input type="text" class="s-input-txt" name="bonus_type_name" size="30" value="<?php echo $bonus_type_name;?>" placeholder="请输入优惠券类型名称"></label>
                    领取时间
                    <span id="drawed_daterange">
                        <input class="s-input-txt" type="text" readonly="true" value="" id="drawed_time_start" name="drawed_time_start"> 至
                        <input class="s-input-txt" type="text" readonly="true" value="" id="drawed_time_end" name="drawed_time_end">
                    </span>
                </div>
                <div style="margin-bottom: 5px;">
                    优惠券号 <label><input type="text" class="s-input-txt" name="bonus_sn" value=""></label>
                    用户ID <label><input type="text" size="7" name="user_name" value=""></label>
                    用户名 <label><input type="text" class="s-input-txt" name="user_name" value=""></label>
                    手机号码 <label><input type="text" class="s-input-txt" name="mobile" value=""></label>
                    使用时间
                    <span id="used_daterange">
                        <input class="s-input-txt" type="text" readonly="true" value="" id="used_time_start" name="used_time_start">至
                        <input class="s-input-txt" type="text" readonly="true" value="" id="used_time_end" name="used_time_end">
                    </span>
                </div>
                <div style="margin-bottom: 5px;">
                    使用情况
                    <select name="use_status">
                        <option value="0">-全部-</option>
                        <option value="2">已使用</option>
                        <option value="1">未使用</option>
                        <option value="3">已过期</option>
                    </select>
                    规则启用
                    <select name="rule_effect">
                        <option value="2">全部</option>
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                    规则删除
                    <select name="rule_delete">
                        <option value="0">全部</option>
                        <option value="2">已删除</option>
                        <option value="1">未删除</option>
                    </select>
                    领取方式
                    <select name="issue_type">
                        <option value="-1">---------</option>
                        <option value="0">系统派发</option>
                        <option value="1">手动发放</option>
                    </select>
                    <input type="hidden" name="type_id" value="<?php echo $type_id;?>">
                    <input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询">
                </div>
            </div>
        </form>
    </div>
    <div  id="flexitable" class="flexitable"></div>
</div>
<script type="text/javascript">
    $(function(){
        $("#flexitable").flexigrid({
            url: '<?php echo adminUrl('user_bonus','use_log_json');?>'+'&type_id=<?php echo $type_id;?>&use_type=<?php echo $use_type;?>',
            colModel : [
                {display: '编号', name : 'id', width : 24, sortable : true, align: 'center'},
                {display: '优惠券号', name : 'value', width : 80, sortable : false, align : 'left'},
                {display: '优惠券类型名称', name : 'info', width : 150, sortable : false, align: 'left'},
                {display: '资产类别', name : 'info', width : 50, sortable : false, align: 'left'},
                {display: '用户名', name : 'info', width : 100, sortable : false, align: 'left'},
                {display: '手机号码', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '面额', name : 'info', width : 40, sortable : false, align: 'left'},
                {display: '使用最少金额', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '领取时间', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '领取方式', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '使用时间', name : 'info', width : 80, sortable : false, align: 'left'},
                {display: '使用情况', name : 'info', width : 80, sortable : false, align: 'left'}
            ],
            title: '优惠券使用情况'
        });

        $('#btnsearch').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('user_bonus','use_log_json');?>&'+$("#form1").serialize(),query:'',qtype:''}).flexReload();
        });
    });

    //领取时间范围
    $('#drawed_daterange').dateRangePicker({
        shortcuts:
        {
            'prev-days': [1,3,5,7,30,60],
            'prev' : ['week','month','year']
        },
        endDate:'<?php echo date('Y-m-d',time());?>',
        getValue: function()
        {
            if ($('#drawed_time_start').val() && $('#drawed_time_end').val() )
                return $('#drawed_time_start').val() + ' to ' + $('#drawed_time_end').val();
            else
                return '';
        },
        setValue: function(s,s1,s2)
        {
            $('#drawed_time_start').val(s1);
            $('#drawed_time_end').val(s2);
        }
    });
    //使用时间范围
    $('#used_daterange').dateRangePicker({
        shortcuts:
        {
            'prev-days': [1,3,5,7,30,60],
            'prev' : ['week','month','year']
        },
        endDate:'<?php echo date('Y-m-d',time());?>',
        getValue: function()
        {
            if ($('#used_time_start').val() && $('#used_time_end').val() )
                return $('#used_time_start').val() + ' to ' + $('#used_time_end').val();
            else
                return '';
        },
        setValue: function(s,s1,s2)
        {
            $('#used_time_start').val(s1);
            $('#used_time_end').val(s2);
        }
    });
    $(".date-picker-wrapper").css('z-index',999);
</script>
</body>
</html>