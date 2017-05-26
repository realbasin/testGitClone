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
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
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
  <span>借出汇总</span>
</div>
<div class="line10"></div>
<div class="page">
	<div class="form-default">
		<form method="post" id="form1" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
   <div class="title">
         按时间段查询：<span id="daterange"><input class="s-input-txt" type="text" readonly="true" value="<?php echo $datestart;?>" id="datestart" name="datestart">
         	至
         	<input class="s-input-txt" type="text" readonly="true" value="<?php echo $dateend;?>" id="dateend" name="dateend">
         	</span>
         	<input type="submit" style="height: 26px;padding: 0 5px;margin-left: 20px;" value="提交查询"></button>
      </div>
      </form>
    </div>
	<div class="form-all stat-general">
	    <div class="title">
	      <h3>最新汇总情报</h3>
	    </div>
	    <div class="line10"></div>
	    <dl class="row">
        <dd class="opt">
        <ul class="def-row">
          <li title="投资人数：<?php echo $investor;?> 人">
            <h4>投资人数</h4>
            <h6>投资人数量</h6>
            <h2 class="timer" id="count-number"  data-to="<?php echo $investor;?>" data-speed="1000"></h2>
          </li>
          <li title="账户余额合计：<?php echo $balancetotal;?> 元">
            <h4>账户余额合计</h4>
            <h6>账户余额（元）</h6>
            <h2 class="timer" id="count-number"  data-to="<?php echo $balancetotal;?>" data-speed="1000" data-decimals="2"></h2>
          </li>
          <li title="成功投资金额：<?php echo $investmoney;?> 元">
            <h4>成功投资金额</h4>
            <h6>成功投资金额（元）</h6>
            <h2 class="timer" id="count-number"  data-to="<?php echo $investmoney;?>" data-speed="1000" data-decimals="2"></h2>
          </li>
          <li title="奖励总额：<?php echo $rebatetotal;?> 元">
            <h4>奖励总额</h4>
            <h6>奖励总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $rebatetotal;?>" data-speed="1000"></h2>
          </li>
          <li title="待收总额：<?php echo $due;?> 元">
            <h4>待收总额</h4>
            <h6>待收总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $due;?>" data-speed="1000"></h2>
          </li>
          <li title="待收本金总额：<?php echo $capital;?> 元">
            <h4>待收本金总额</h4>
            <h6>待收本金总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $capital;?>" data-speed="1000"></h2>
          </li>
          <li title="待收利息总额：<?php echo $interest;?> 元">
            <h4>待收利息总额</h4>
            <h6>待收利息总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $interest;?>" data-speed="1000"></h2>
          </li>
          <li title="已收总额：<?php echo $receivetatal;?> 元">
            <h4>已收总额</h4>
            <h6>已收总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $receivetatal;?>" data-speed="1000"></h2>
          </li>
          <li title="已收本金总额：<?php echo $receivecapital;?> 元">
            <h4>已收本金总额</h4>
            <h6>已收本金总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $receivecapital;?>" data-speed="1000"></h2>
          </li>
          <li title="已收利息总额：<?php echo $receiveinterest;?> 元">
            <h4>已收利息总额</h4>
            <h6>已收利息总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $receiveinterest;?>" data-speed="1000" ></h2>
          </li>
          <li title="提前还款罚息：<?php echo $receivefine;?> 元">
            <h4>提前还款罚息</h4>
            <h6>提前还款罚息总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $receivefine;?>" data-speed="1000"></h2>
          </li>
          <li title="逾期还款罚金：<?php echo $receivepanalty;?> 元">
            <h4>逾期还款罚金</h4>
            <h6>逾期还款罚金总额（元）</h6>
            <h2 class="timer" id="count-number"  data-decimals="2" data-to="<?php echo $receivepanalty;?>" data-speed="1000" ></h2>
          </li>
        </ul>
      </dl>
	</div>
	<div class="stat-chart">
    <div class="title">
      <h3>借出汇总图表</h3>
    </div>
    <div id="container" class=" " style="height:400px"></div>
  </div>
</div>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.numberAnimation.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/highcharts.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/modules/exporting.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/plugin/highcharts-zh_CN.js"></script>
<script>
var datetext='';
<?php if(!$datestart && !$dateend){?>
	datetext='(全部数据)';
<?php }?>
<?php if($datestart && !$dateend){?>
	datetext='(<?php echo $datestart;?> 之后)';
<?php }?>
<?php if(!$datestart && $dateend){?>
	datetext='(<?php echo $dateend;?> 之前)';
<?php }?>
<?php if($datestart && $dateend){?>
	datetext='(<?php echo $datestart;?> 至 <?php echo $dateend?>)';
<?php }?>
$(function(){
	    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '借出汇总'
        },
        subtitle: {
            text: '借出汇总图表 '+datetext
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: '金额 (元)'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '金额: <b>{point.y:.2f} 元</b>'
        },
        series: [{
            name: '总计金额',
            data: [
                ['账户余额合计', <?php echo $balancetotal?$balancetotal:0;?>],
                ['成功投资金额', <?php echo $investmoney?$investmoney:0;?>],
                ['奖励总额', <?php echo $rebatetotal?$rebatetotal:0;?>],
                ['待收总额', <?php echo $due?$due:0;?>],
                ['待收本金总额', <?php echo $capital?$capital:0;?>],
                ['待收利息总额', <?php echo $interest?$interest:0;?>],
                ['已收总额', <?php echo $receivetatal?$receivetatal:0;?>],
                ['已收本金总额', <?php echo $receivecapital?$receivecapital:0;?>],
                ['已收利息总额', <?php echo $receiveinterest?$receiveinterest:0;?>],
                ['提前还款罚息总额', <?php echo $receivefine?$receivefine:0;?>],
                ['逾期还款罚金总额', <?php echo $receivepanalty?$receivepanalty:0;?>]
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.2f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
    
});

$('#daterange').dateRangePicker({
	shortcuts:
			{
				'prev-days': [1,3,5,7,30,60],
				'prev' : ['week','month','year']
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
        content: "<li>可以对图表进行相关格式的导出",
        quickClose: true
        });
       d.show(this);
});


</script>
</body>
</html>