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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js?v=1111"></script>
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
            <dt>流标返还</dt>
            <dd>
              <select class="class-select" id="is_has_received" name="is_has_received" value="-1">
                <option value="-1">-全部类型-</option>
               <option value="0">未返还</option>
                <option value="1">已返还</option>
              </select>
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
        </div>
      </div>
      <div class="bottom"><a href="javascript:void(0);" id="submit" style="color: #ffffff;" class="btn btn-green mr5">提交查询</a><a href="javascript:void(0);" id="reset" style="color: #ffffff;" class="btn btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i>撤销</a></div>
    </form>
	</div>
<script>
$(function(){
	$("#flexitable").flexigrid({
        url: '<?php echo adminUrl('loan_loan','all_json');?>',
        colModel : [
            {display: '<?php echo \Core::L("operate");?>', name : 'operation', width : 80, sortable : false, align: 'center', className: 'handle-m'},
            {display: '编号', name : 'id', width : 50, sortable : true, align: 'center'}, 
			{display: '贷款名称', name : 'name', width : 60, sortable : true, align : 'center'},
			{display: '借款人', name : 'user_id', width : 100, sortable : true, align: 'left'},
            {display: '推荐人', name : 'pid', width : 100, sortable : true, align: 'left'},
			{display: '贷款金额', name : 'borrow_amount', width : 80, sortable : true, align: 'center'},
			{display: '利率(%)', name : 'rate', width : 50, sortable : true, align: 'center'},
			{display: '期数', name : 'repay_time', width : 40, sortable : true, align: 'center'},
			{display: '还款方式', name : 'loantype', width : 60, sortable : true, align: 'center'},
			{display: '投标状态', name : 'deal_status', width : 60, sortable : false, align: 'center'},
			{display: '是否放款', name : 'is_has_loans', width : 50, sortable : false, align: 'center'},
			{display: '流标返还', name : 'is_has_received', width : 50, sortable : false, align: 'center'},
			{display: '投标数', name : 'buy_count', width : 40, sortable : false, align: 'center'},
			{display: '客户端', name : 'sor_code', width : 100, sortable : true, align: 'center'},
			{display: '初审人', name : 'first_audit_admin_id', width : 80, sortable : true, align: 'center'},
			{display: '复审人', name : 'second_audit_admin_id', width : 80, sortable : true, align: 'center'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出贷款列表', name : 'cvs', bclass : 'csv',title : '导出贷款列表', onpress : flexPress },
        ],
        searchitems : [
            {display: '编号', name : 'id'},
            {display: '贷款名称', name : 'name'},
            {display: '贷款金额', name : 'borrow_amount'},
            {display: '贷款利率', name : 'rate'},
            {display: '贷款人ID', name : 'user_id'},
            ],

        sortname: "id",
        sortorder: "desc",
        title: '全部贷款'
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
        window.location.href = '<?php echo adminUrl('loan_loan','loanlist_export');?>';
    }
}
//还款计划
function loan_repay_plan(id){
	location.href='<?php echo adminUrl('loan_loan','repay_plan');?>'+'&loan_id='+id;
}

//详情
function loan_detail(id){
	location.href='<?php echo adminUrl('loan_loan','detail');?>'+'&loan_id='+id;
}

//预览
function loan_preview(id){
	//window
    location.href='<?php echo adminUrl('loan_loan','preview');?>'+'&loan_id='+id;
}

//审核日志
function loan_audit_log(id){
	location.href='<?php echo adminUrl('loan_oplog','audit_log');?>'+'&loan_id='+id;
}
//编辑贷款
function loan_show_edit(id){
    location.href='<?php echo adminUrl('loan_loan','loan_show_edit');?>'+'&loan_id='+id;
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