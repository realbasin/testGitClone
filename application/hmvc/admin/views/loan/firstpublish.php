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
    <span><?php echo \Core::L('first_publish');?></span>

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
                    <dt>贷款ID</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="id" id="id" class="s-input-txt" placeholder="输入贷款id">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款名称</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="name" id="name" class="s-input-txt" placeholder="输入贷款名称">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款金额</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="borrow_amount" id="borrow_amount" class="s-input-txt" placeholder="输入贷款金额">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>利率</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="rate" id="rate" class="s-input-txt" placeholder="输入贷款利率">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款期数</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="repay_time" id="repay_time" class="s-input-txt" placeholder="输入贷款期数">
                            <select class="class-select" id="repay_time_type" name="repay_time_type" value="-1">
                                <option value="-1">-全部类型-</option>
                                <?php if($repaytimetype){?>
                                    <?php foreach($repaytimetype as $k=>$v){?>
                                        <?php echo "<option value='".$k."'>".$v."</option>";?>
                                    <?php }?>
                                <?php }?>
                            </select>
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>推荐码</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="work_id" id="work_id" class="s-input-txt" placeholder="输入推荐码">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>还款方式</dt>
                    <dd>
                        <select class="class-select" id="loantype" name="loantype" value="-1">
                            <option value="-1">-全部类型-</option>
                            <?php if($loantype){?>
                                <?php foreach($loantype as $k=>$v){?>
                                    <?php echo "<option value='".$k."'>".$v."</option>";?>
                                <?php }?>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>投标类型</dt>
                    <dd>
                        <select class="class-select" id="cate_id" name="cate_id" value="-1">
                            <option value="-1">-全部类型-</option>
                            <?php if($dealcate){?>
                                <?php foreach($dealcate as $k=>$v){?>
                                    <?php echo "<option value='".$k."'>".$v['name']."</option>";?>
                                <?php }?>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款用途</dt>
                    <dd>
                        <select class="class-select" id="use_type" name="use_type" value="-1">
                            <option value="-1">-全部类型-</option>
                            <?php if($dealusetype){?>
                                <?php foreach($dealusetype as $k=>$v){?>
                                    <?php echo "<option value='".$k."'>".$v['name']."</option>";?>
                                <?php }?>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>客户端</dt>
                    <dd>
                        <select class="class-select" id="sor_code" name="sor_code" value="-1">
                            <option value="-1">-全部类型-</option>
                            <?php if($sorcode){?>
                                <?php foreach($sorcode as $k=>$v){?>
                                    <?php echo "<option value='".$k."'>".$v['code_name']."</option>";?>
                                <?php }?>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款状态</dt>
                    <dd>
                        <select class="class-select" id="deal_status" name="deal_status" value="-1">
                            <option value="-1">-全部类型-</option>
                            <?php if($dealstatus){?>
                                <?php foreach($dealstatus as $k=>$v){?>
                                    <?php echo "<option value='".$k."'>".$v."</option>";?>
                                <?php }?>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt>贷款人ID</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="user_id" id="user_id" class="s-input-txt" placeholder="输入贷款人id">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>手机号</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="user_mobile" id="user_mobile" class="s-input-txt" placeholder="输入手机号">
                        </label>
                    </dd>
                </dl>

                <dl>
                    <dt>贷款人名称</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="user_name" id="user_name" class="s-input-txt" placeholder="输入贷款人名称">
                        </label>
                    </dd>
                </dl>
                <dl>
                    <dt>推荐人名称</dt>
                    <dd>
                        <label>
                            <input type="text" value="" name="p_user_name" id="p_user_name" class="s-input-txt" placeholder="输入推荐人名称">
                        </label>
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
            url: '<?php echo adminUrl('loan_audit','first_publish_json');?>',
            colModel : [
                {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 80, sortable : false, align: 'center', className: 'handle-m'},
                {display: '编号', name : 'id', width : 50, sortable : true, align: 'center'},
                {display: '贷款名称', name : 'name', width : 60, sortable : true, align : 'center'},
                {display: '借款人', name : 'user_id', width : 100, sortable : true, align: 'left'},
                {display: '贷款金额', name : 'borrow_amount', width : 80, sortable : true, align: 'center'},
                {display: '利率(%)', name : 'rate', width : 50, sortable : true, align: 'center'},
                {display: '期数', name : 'repay_time', width : 40, sortable : true, align: 'center'},
                {display: '借款用途', name : 'use_type', width : 60, sortable : true, align: 'center'},
                {display: '还款方式', name : 'loantype', width : 60, sortable : true, align: 'center'},
                {display: '最近操作时间', name : 'update_time', width : 120, sortable : true, align: 'center'},
                {display: '客户端', name : 'sor_code', width : 100, sortable : true, align: 'center'},
                {display: '审核状态', name : 'publish_status', width : 60, sortable : true, align: 'center'},
                {display: '认领操作', name : 'first_audit_admin_id', width : 80, sortable : true, align: 'center'},
            ],

            searchitems : [
                {display: '编号', name : 'id'},
                {display: '贷款名称', name : 'name'}
            ],
            buttons : [
                {display: '<i class=""></i>认领', name : 'do_claim', bclass : 'do_claim', title : '认领', onpress : flexPress }
            ],
            sortname: "id",
            sortorder: "desc",
            title: '<?php echo \Core::L('first_publish');?>'
        });

        $('#submit').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_audit','first_publish_json');?>&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });

        $('#reset').click(function(){
            $("#flexitable").flexOptions({url: '<?php echo adminUrl('loan_audit','first_publish_json');?>'}).flexReload();
            $("#formSearch")[0].reset();
        });

    });

    function flexPress(name, grid) {
        if(name=='csv'){
            var itemlist = new Array();
            if($('.trSelected',grid).length>0){
                $('.trSelected',grid).each(function(){
                    itemlist.push($(this).attr('data-id'));
                });
            }
            flexExport(itemlist);
        }
    }

    //编辑
    function loan_audit(id){
        location.href='<?php echo adminUrl('loan_audit','first_publish_edit');?>&loan_id='+id;
    }

    //还款计划
    function loan_repay_plan(id){
        location.href='<?php echo adminUrl('loan_loan','repay_plan');?>';
    }

    //详情
    function loan_detail(id){
        location.href='<?php echo adminUrl('loan_loan','detail');?>';
    }

    //合同
    function loan_contract(id){
        //dialog
    }

    //预览
    function loan_preview(id){
        location.href='<?php echo adminUrl('loan_audit','preview');?>&loan_id='+id;
    }

    //审核日志
    function loan_audit_log(id){
        location.href='<?php echo adminUrl('loan_oplog','audit_log');?>'+'&loan_id='+id;
    }

    $('#syshelp').on("click",function(){
        var d = dialog({
            content: "<?php echo \Core::L('loan_all_help');?>",
            quickClose: true
        });
        d.show(this);
    });

    function get_owners(id){
        var ids = '';
        $("input[name='key']:checked").each(function(idx){
            ids = $(this).val()+ids+',';
        });
        if(parseInt(id) > 0){
            ids = ids+id;
        }
        if(ids == ''){
            alert("请先选择");
            return;
        }
        //alert(ids);
        $.ajax({
            url	: "<?php echo adminUrl('loan_audit','publish_audit_owner').'&way=1';?>",
            dataType: "json",
            async	: false,
            data	: {id: id, ids: ids},
            type	: "POST",
            success: function(data){
                /*请求成功时处理*/
                alert(data.msg);
                if(data.response_code == 1){
                    window.location.reload();
                }
            },
            error: function(jqxhr, textstatus, errorthrown){
                /*请求出错处理*/
                alert('error');
            }
        });
    }


</script>
</body>
</html>