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
  <span>借出统计</span>
  <i class="arrow"></i>
  <span>投资额比例</span>
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
      <h3>投资额比例汇总图表</h3>
    </div>
    <div id="container" class=" " style="height:400px"></div>
  </div>
 	<div  id="flexitable" class="flexitable">
 		<table class="flexigrid">
      <thead>
        <tr>
          <th width="24" style="width: 24px;" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="80" style="width: 80px;" align="center">时间</th>
          <th width="80" style="width: 80px;" align="center">总人次</th>
          <th width="80" style="width: 80px;" align="center">5千以下</th>
          <th width="80" style="width: 80px;" align="center">5千(含)至1万</th>
          <th width="80" style="width: 80px;" align="center">1万(含)至5万</th>
          <th width="80" style="width: 80px;" align="center">5万(含)至10万</th>
          <th width="90" style="width: 90px;" align="center">10万(含)至20万</th>
          <th width="90" style="width: 90px;" align="center">20万(含)至50万</th>
          <th width="80" style="width: 80px;" align="center">50万(含)以上</th>
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
	title: '投资额比例汇总',
	buttons : [
               {display: '<i class="fa fa-file-excel-o"></i> 导出Excel', name : 'csv', bclass : 'csv', onpress : btnPress }
           ]
	});	
	
function btnPress(name, grid) {
    if (name == 'csv') {
        window.location.href = '<?php echo adminUrl('stat_loan','investProportion_export',array('datestart'=>$datestart,'dateend'=>$dateend));?>';
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
	var url='<?php echo adminUrl('stat_loan','investProportion');?>';
	url+='&datestart='+datestart+'&dateend='+dateend;
	location.href=url;
});

//查询初始化
function initSearch(){
	var datestart=$('#datestart').val();
	var dateend=$('#dateend').val();
	$.ajax({
		url:'<?php echo adminUrl('stat_loan','investProportion_json');?>',
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
		jsonhtml+='"'+val.usertotal+'",';
		jsonhtml+='"'+val.p1+'",';
		jsonhtml+='"'+val.p2+'",';
		jsonhtml+='"'+val.p3+'",';
		jsonhtml+='"'+val.p4+'",';
		jsonhtml+='"'+val.p5+'",';
		jsonhtml+='"'+val.p6+'",';
		jsonhtml+='"'+val.p7+'",';
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
	var usertotalArr=[];
	var investrepayArr=[];
	var p1Arr=[];
	var p2Arr=[];
	var p3Arr=[];
	var p4Arr=[];
	var p5Arr=[];
	var p6Arr=[];
	var p7Arr=[];
	
	$.each(data, function(key,val) {
		createdateArr[key]=val.createdate;
		usertotalArr[key]=parseInt(val.usertotal);
		p1Arr[key]=parseInt(val.p1);
		p2Arr[key]=parseInt(val.p2);
		p3Arr[key]=parseInt(val.p3);
		p4Arr[key]=parseInt(val.p4);
		p5Arr[key]=parseInt(val.p5);
		p6Arr[key]=parseInt(val.p6);
		p7Arr[key]=parseInt(val.p7);
	});
	var chart = new Highcharts.Chart('container', {
    title: {
        text: '投资额比例汇总图表',
    },
    subtitle: {
        text: '数据时间:('+datestart+" 至 "+dateend+")",
    },
    xAxis: {
        categories: createdateArr
    },
    yAxis: {
        title: {
            text: '人数/金额'
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
        name: '总人次',
        data: usertotalArr
    }, 
    {
        name: '5千以下',
        data: p1Arr
    }, 
    {
        name: '5千(含)至1万',
        data: p2Arr
    }, 
    {
        name: '1万(含)至5万',
        data: p3Arr
    }, 
    {
        name: '5万(含)至10万',
        data: p4Arr
    }, 
    {
        name: '10万(含)至20万',
        data: p5Arr
    }, 
    {
        name: '20万(含)至50万',
        data: p6Arr
    }, 
    {
        name: '50万(含)以上',
        data: p7Arr
    }]
});
}

initSearch();
</script>
</body>
</html>