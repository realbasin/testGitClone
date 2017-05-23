<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/jquery.daterangepicker.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js?v=201705041335"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
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
    <span><?php echo \Core::L('loan');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_all');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_op_log');?></span>

</div>
<div class="line10"></div>
<div class="page">
    <div  id="flexitable" class="flexitable"></div>
</div>
<!--高级搜索-->
<div class="search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
<div class="search-bar">
    <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
    <div class="title">
        <h3>高级搜索</h3>
    </div>
    <form method="get" name="formSearch" id="formSearch">
        <div id="searchCon" class="content">
            <div class="layout-box">
                <dl>
                    <dt>贷款编号</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="deal_id" id="id" class="s-input-txt" placeholder="输入贷款id">
                        </label>
                    </dd>
                </dl>


                <dl>
                    <dt>操作阶段</dt>
                    <dd>
                        <select class="class-select" id="use_type" name="op_type" value="-1">
                            <option value="-1">-全部-</option>
                            <option value="1">初审操作</option>
                            <option value="2">认领操作</option>
                            <option value="3">取消认领操作</option>
                            <option value="4">彻底删除操作</option>
                            <option value="5">删除操作</option>
                            <option value="6">恢复操作</option>
                            <option value="7">复审操作</option>
                        </select>
                    </dd>
                </dl>

                <dl>
                    <dt>按时间段查询</dt>
                    <dd>
                          <span id="daterange">
                            <input class="s-input-txt" type="text" readonly="true" value="" id="datestart" name="datestart">
         	                至
         	                <input class="s-input-txt" type="text" readonly="true" value="" id="dateend" name="dateend">
                          </span>
                    </dd>
                </dl>


            </div>
        </div>
        <div class="bottom"><a href="javascript:void(0);" id="submit" style="color: #ffffff;" class="btn btn-green mr5">提交查询</a><a href="javascript:void(0);" id="reset" style="color: #ffffff;" class="btn btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i>撤销</a></div>
    </form>
</div>
<script>
    $(function(){
        $("#flexitable").flexigrid({
            url: '<?php echo adminUrl('loan_loan','all_op_log_json',array('loan_id'=>$loan_id));?>',
            colModel : [
                {display: '贷款编号', name : 'deal_id', width : 50, sortable : false, align: 'center'},
                {display: '借款用户', name : 'user_id', width : 100, sortable : false, align : 'center'},
                {display: '操作阶段', name : 'op_name', width : 80, sortable : false, align: 'left'},
                {display: '日志信息', name : 'log', width : 300, sortable : false, align: 'left'},
                {display: '结果', name : 'op_result', width : 80, sortable : false, align: 'center'},
                {display: '操作人', name : 'admin_id', width : 150, sortable : false, align: 'center'},
                {display: '操作时间', name : 'create_time', width : 150, sortable : false, align: 'center'},
                {display: '操作IP', name : 'ip', width : 150, sortable : false, align: 'center'},
            ],
            searchitems : [
                {display: '贷款编号', name : 'deal_id'},
            ],
            sortname: "create_time",
            sortorder: "desc",
            title: '审核日志列表',
            usepager: true, //是否分页
            columnControl:false,
            reload:false,
        });

        $('#submit').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_op_log_json');?>&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        $('#reset').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_op_log_json');?>'}).flexReload();
            $("#formSearch")[0].reset();
        });

    });
    $('#daterange').dateRangePicker({
        shortcuts:
        {
            'prev-days': [1,3,5,7,30,60],
            'prev' : null,
        },
        endDate:'<?php echo date('Y-m-d',time());?>',
        getValue: function()
        {
            if ($('#datestart').val() && $('#dateend').val() )
                return $('#datestart').val() + ' to ' + $('#dateend').val();
            else
                return '';
        },
        setValue: function(s,s1,s2)
        {
            $('#datestart').val(s1);
            $('#dateend').val(s2);
        }
    });
    $('#syshelp').on("click",function(){
        var d = dialog({
            content: "<?php echo \Core::L('loan_all_help');?>",
            quickClose: true
        });
        d.show(this);
    });
    $(".date-picker-wrapper").css('z-index',999);
</script>
</body>
</html>