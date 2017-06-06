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
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js?v=201705041335"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
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
    <span><?php echo \Core::L('loan_repay_plan');?></span>

</div>
<div class="line10"></div>
<div class="page">
    <div  id="flexitable" class="flexitable"></div>
</div>
<script>
    $(function(){
        $("#flexitable").flexigrid({
            url: '<?php echo adminUrl('loan_loan','all_repay_plan_json',array('loan_id'=>$loan_id));?>',
            colModel : [
                {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 80, sortable : false, align: 'center', className: 'handle-m'},
                {display: '第几期', name : 'id', width : 50, sortable : false, align: 'center'},
                {display: '还款日', name : 'name', width : 100, sortable : false, align : 'center'},
                {display: '已还总额', name : 'user_id', width : 80, sortable : false, align: 'left'},
                {display: '待还总额', name : 'pid', width : 80, sortable : false, align: 'left'},
                {display: '还需还金额', name : 'pid', width : 80, sortable : false, align: 'left'},
                {display: '待还本息', name : 'borrow_amount', width : 80, sortable : false, align: 'center'},
                {display: '管理费', name : 'rate', width : 80, sortable : false, align: 'center'},
                {display: '逾期/违约金', name : 'repay_time', width : 100, sortable : false, align: 'center'},
                {display: '逾期/违约管理费', name : 'loantype', width : 150, sortable : false, align: 'center'},
                {display: '还款情况', name : 'deal_status', width : 100, sortable : false, align: 'center'},
                {display: '还款时间', name : 'is_has_loans', width : 150, sortable : false, align: 'center'},
                {display: '逾期天数', name : 'is_has_received', width : 150, sortable : false, align: 'center'},
                {display: '查看', name : 'buy_count', width : 40, sortable : false, align: 'center'},
            ],
            buttons : [
                {display: '<i class="fa"></i> 手动还款', name : 'manual_repay',  title : '手动还款', bclass : 'csv',onpress : flexPress },
                {display: '<i class="fa fa-file-excel-o"></i> 导出还款计划列表', name : 'cvs', bclass : 'csv', title : '导出还款计划列表', onpress : flexPress }
            ],
            sortname: "l_key",
            sortorder: "asc",
            title: '还款计划',
            usepager: false, //是否分页
            columnControl:false,
            reload:false,
        });

        $('#submit').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_json');?>&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        $('#reset').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_loan','all_json');?>'}).flexReload();
            $("#formSearch")[0].reset();
        });

    });
    function flexPress(name, grid) {
        if(name=='cvs'){
            window.location.href = '<?php echo adminUrl('loan_loan','repayplan_export',array('deal_id'=>$loan_id));?>';
        }
        if(name=='manual_repay'){
            manual_repay();
        }
    }
    //手动还款
    function manual_repay(id,lkey,all_repay_money){
        if (typeof id == 'string') {
            var id = new Array(id);
        };
        parent.dialog({
            title: '确认手动还款金额',
            content: '账户余额为￥'+<?php echo $usermoney;?>+'元</br>本期需还款总额为￥'+all_repay_money+'元</br>是否确定进行手动还款操作？!',
            width:300,
            okValue: lang['ok'],
            ok: function () {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "<?php echo adminUrl('loan_loan','manual_repay');?>",
                    data: "id="+id+"&l_key="+lkey,
                    success: function(data){
                        if (data.code==200){
                            $("#flexitable").flexReload();
                        } else {
                            jsprint(data.message);
                        }
                    }
                });
            },
            cancelValue: lang['cancel'],

            cancel: function () { }
        }).showModal();
    }

    //导出计划列表
    function repay_plan_export_load(id,lkey){
        location.href='<?php echo adminUrl('loan_loan','repayplan_export');?>'+'&deal_id='+id+'&l_key='+lkey;
    }

    //弹窗显示
    function viewloanitem(deal_id,l_key){
        var d=parent.dialog({
            title: '投资人第'+(l_key + 1)+'期回款列表',
            url: '<?php echo adminUrl('loan_loan','viewloanitem');?>'+'&loan_id='+deal_id+'&l_key='+l_key,
        });
        d.width(1000);
        d.show();
    }
    $('#syshelp').on("click",function(){
        var d = dialog({
            content: "<?php echo \Core::L('loan_all_help');?>",
            quickClose: true
        });
        d.show(this);
    });

</script>
</body>
</html>