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
<link href="<?php echo RS_PATH?>jquery/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>jquery/jquery.daterangepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
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
  <span>借入统计</span>
  <i class="arrow"></i>
  <span>借款金额</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         按时间段查询：
         <span id="daterange">
         <input class="s-input-txt" type="text" readonly="true" value="<?php echo $datestart?$datestart:date('Y-m-d',strtotime('-30 day'));?>" id="datestart" name="datestart">
         	至
         	<input class="s-input-txt" type="text" readonly="true" value="<?php echo $dateend?$dateend:date('Y-m-d',time());?>" id="dateend" name="dateend">
         		</span>
         	<input type="button" id="btnsearch" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
      </div>
      </form>
   </div>
	<div class="stat-chart">
    <div class="title">
      <h3>借款金额汇总表</h3>
    </div>
    <div id="container" class=" " style="height:400px"></div>
  </div>
 	<div  id="flexitable" class="flexitable">
 		<table class="flexigrid">
      <thead>
        <tr>
          <th width="24" style="width: 24px;" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="150" style="width: 150px;" align="center">时间</th>
          <th width="150" style="width: 150px;" align="center">申请借款金额</th>
          <th width="100" style="width: 100px;" align="center">申请人数</th>
          <th width="150" style="width: 150px;" align="center">满标放款金额</th>
          <th width="150" style="width: 150px;" align="center">流标失败金额</th>
          <th width="150" style="width: 150px;" align="center">审核通过金额</th>
          <th width="150" style="width: 150px;" align="center">审核通过人数</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
	</div>
</div>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/highcharts.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/modules/exporting.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/plugin/highcharts-zh_CN.js"></script>
<script>
$('.flexigrid').flexigrid({	
	usepager: false,
	reload: false,
	columnControl: false,
	title: '借款金额',
	buttons : [
               {display: '<i class="fa fa-file-excel-o"></i> 导出Excel', name : 'csv', bclass : 'csv', onpress : btnPress }
           ]
	});	
	
function btnPress(name, grid) {
    if (name == 'csv') {
        window.location.href = '<?php echo adminUrl('stat_borrow','borrowerAmount_export',array('datestart'=>$datestart,'dateend'=>$dateend));?>';
    }
};
	
$('#daterange').dateRangePicker({
	shortcuts:
			{
				'prev-days': [1,3,5,7,30,60],
				'prev' : null,
			},
	maxDays:60,
	//startDate:'<?php echo date('Y-m-d',strtotime("-60 day"));?>',
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
        content: "<li>可以导出全部数据或者导出图表",
        quickClose: true
        });
       d.show(this);
});

//查询
$('#btnsearch').on('click',function(){
	var datestart=$('#datestart').val();
	var dateend=$('#dateend').val();
	var url='<?php echo adminUrl('stat_borrow','borrowerAmount');?>';
	url+='&datestart='+datestart+'&dateend='+dateend;
	location.href=url;
});

//查询初始化
function initSearch(){
	var datestart=$('#datestart').val();
	var dateend=$('#dateend').val();
	$.ajax({
		url:'<?php echo adminUrl('stat_borrow','borrowerAmount_json');?>',
  		type:'get',
  		data:{
  			datestart:datestart,
  			dateend:dateend
  		},
  		dataType:'json',
  		success:function(msg){
  			if(msg.code!=200){
  				jsprint(msg.message);
  				return;
  			}
  			fillCharts(msg.data);
  			fillFlexTable(msg.data);
  		},
  		 error: function(XMLHttpRequest, textStatus, errorThrown){
  		 	jsprint('网络也太差了吧！');
  		 }
	});
}

function fillFlexTable(data){
	var jsonhtml='{"rows":[';
	$.each(data, function(key,val) {
		jsonhtml+='{"id":"'+key+'",';
		jsonhtml+='"cell":[';
		jsonhtml+='"'+val.createdate+'",';
		jsonhtml+='"'+val.apply_borrow_amount+'",';
		jsonhtml+='"'+val.apply_user_count+'",';
		jsonhtml+='"'+val.real_borrow_amount+'",';
		jsonhtml+='"'+val.fail_borrow_amount+'",';
		jsonhtml+='"'+val.audit_borrow_amount+'",';
		jsonhtml+='"'+val.audit_user_count+'",';
		jsonhtml+='""]},';
	});
	jsonhtml = jsonhtml.substring(0, jsonhtml.length - 1);
	jsonhtml+='],"total":"'+data.length+'"}';
	$('.flexigrid').flexAddData(JSON.parse(jsonhtml));
}

function fillCharts(data){
	var datestart=$('#datestart').val();
	var dateend=$('#dateend').val();
	var createdateArr=[];
	var apply_borrow_amount_Arr=[];
	var apply_user_count_Arr=[];
	var real_borrow_amount_Arr=[];
	var fail_borrow_amount_Arr=[];
	var audit_borrow_amount_Arr=[];
	var audit_user_count_Arr=[];
	
	$.each(data, function(key,val) {
		createdateArr[key]=val.createdate;
		apply_borrow_amount_Arr[key]=parseFloat(val.apply_borrow_amount);
		apply_user_count_Arr[key]=parseInt(val.apply_user_count);
		real_borrow_amount_Arr[key]=parseFloat(val.real_borrow_amount);
		fail_borrow_amount_Arr[key]=parseFloat(val.fail_borrow_amount);
		audit_borrow_amount_Arr[key]=parseFloat(val.audit_borrow_amount);
		audit_user_count_Arr[key]=parseInt(val.audit_user_count);
	});
	var chart = new Highcharts.Chart('container', {
    title: {
        text: '借款金额/人数汇总图表',
    },
    subtitle: {
        text: '数据时间:('+datestart+" 至 "+dateend+")",
    },
    xAxis: {
        categories: createdateArr
    },
    yAxis: {
        title: {
            text: '借款金额/人数'
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }]
    },
    tooltip: {
        valueSuffix: ''
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
    },
    series: [{
        name: '申请借款金额',
        data: apply_borrow_amount_Arr
    },{
        name: '申请人数',
        data: apply_user_count_Arr
    },{
        name: '满标放款金额',
        data: real_borrow_amount_Arr
    },{
        name: '流标失败金额',
        data: fail_borrow_amount_Arr
    },{
        name: '审核通过金额',
        data: audit_borrow_amount_Arr
    },{
        name: '审核通过人数',
        data: audit_user_count_Arr
    }]
});
}

initSearch();
</script>
</body>
</html>