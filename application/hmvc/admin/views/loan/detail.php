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
    <link href="<?php echo RS_PATH?>jquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
    <![endif]-->
    <!--[if IE]>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5shiv.min.js"></script>
    <![endif]-->
</head>
<body class="mainbody">
<div class="location">
	<div  class="right"><a href="javascript:void(null);" onclick="help(this);"  onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('loan');?></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_all');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('loan_detail');?></span>
  
</div>
<div class="line10"></div>
<div class="page">
    <div class="form-default" >
    <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_loan_name');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo $loan['name'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_up_time');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo $loan['start_time']!=0?date('Y-m-d H:i:s',$loan['start_time']):'';?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_all_borrow');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo '￥'.$loan['borrow_amount'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_loan_money');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo '￥'.$loan['load_money'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_loan_need_money');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo '￥'.$loan['need_money'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><?php echo \Core::L('loan_detail_repay_time_type');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo $loan['repay_time_type'];?>
        </dd>
      </dl>

      <dl class="row" <?php echo $loan['deal_status'] >= 4?'style="display:none;"':'';?>>
        <dt class="tit">
          <label><?php echo \Core::L('operate');?>：</label>
        </dt>
        <dd class="opt">
            <?php echo $loan['deal_status']==11?'<font color="red">问题标，请联系技术处理</font>':($loan['need_money']<0?'<font color="red">问题标，请联系技术处理</font>':'')?>
            <?php echo ($loan['deal_status']==2&&$loan['is_has_loans']==0)?($loan['need_money']>0?'<label><input type="radio" name="deal_status" value="1" disabled/>满标放款</lable>':'<label><input type="radio" name="deal_status" value="1"/>满标放款</lable>'):''?>
            <?php echo (($loan['deal_status']==3||$loan['deal_status']==2||($loan['deal_status']==1&&$loan['is_over_time']==1)||$loan['deal_status']==1||$loan['deal_status']==0)&&$loan['is_has_received']==0)?($loan['buy_count']>0?'<label><input type="radio" name="deal_status" value="2"/>流标返还</lable>':'<label><input type="radio" name="deal_status" value="3"/>流标</lable>'):''?>

        </dd>
      </dl>
        <from class="bid_full">
        <dl class="row fullbid" style="display: none;">
            <dt class="tit">
                <label>上传凭证：</label>
            </dt>
            <dd class="opt">
                <input type="file" name="upload" />
            </dd>
        </dl>
        <dl class="row fullbid" style="display: none;">
            <dt class="tit">
                <label>确认时间：</label>
            </dt>
            <dd class="opt">
                 <span id="daterange">
                     <input type="text" plugin="datepicker" class="s-input-txt" id="datestart" name="time" placeholder="点击选择时间"/>
                 </span>
                <input type="button" class="input-btn" value="确定" onclick="do_loans(<?php echo $loan_id;?>);">
                <input type="button" class="input-btn" value="取消" onclick="nothingdo();">
                <br>
				<span style="color:#ff9600;">
					还款日：<br>
					天标按确认之日起算，如 设置为 2014.1.1，借款期限为2天，还款日为：2014.1.3<br>
					其他标从确认时间开始的起算，如 设置为 2014.1.1 即第一次还款日为：2014.2.1，确认时间不要设置为29,30,31号
				</span>

            </dd>
        </dl>
        </from>
        <dl class="row failsbid" style="display: none;">
            <dt class="tit">
                <label>&nbsp;</label>
            </dt>
            <dd class="opt">
                    <p>已流标原因:</p>
                    <textarea name="reason" rows="3" cols="50" style="height:auto"></textarea>
                    <div ></div>
                    <input type="button" class="input-btn" value="确定返款" onclick="do_received(<?php echo $loan_id;?>);">
                    <input type="button" class="input-btn" value="取消" onclick="nothingdo();">
            </dd>
        </dl>
        <dl class="row" <?php echo $loan['deal_status'] < 4?'style="display:none;"':'';?>>
            <dt class="tit">
                <label><?php echo \Core::L('loan_detail_loan_time');?>：</label>
            </dt>
            <dd class="opt">
                <?php echo $loan['loan_time']!=0?date('Y-m-d H:i:s',$loan['loan_time']):'';?>
            </dd>
        </dl>
        <dl class="row" <?php echo $loan['deal_status'] < 4?'style="display:none;"':'';?>>
            <dt class="tit">
                <label><?php echo \Core::L('loan_detail_repay_start_time');?>：</label>
            </dt>
            <dd class="opt">
                <?php echo $loan['repay_start_time']!=0?date('Y-m-d H:i:s',$loan['repay_start_time']):'';?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label>投标列表：</label>
            </dt>
            <dd class="opt">
            <div  id="flexitable" class="flexitable">
                <table class="flexigrid">
                    <thead>
                    <tr>
                        <th width="24" style="width: 24px;" align="center" class="sign"><i class="ico-check"></i></th>
                        <th width="150" style="width: 150px;" align="center">投标人</th>
                        <th width="150" style="width: 150px;" align="center">投标金额</th>
                        <th width="150" style="width: 150px;" align="center">状态</th>
                        <th width="150" style="width: 150px;" align="center">是否转账</th>
                        <th width="150" style="width: 150px;" align="center">流标返还</th>
                        <th width="150" style="width: 150px;" align="center">投标时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            </dd>
        </dl>
      </div>
      <div class="page-footer">

</div>
</div>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/highcharts.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/modules/exporting.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>highcharts/plugin/highcharts-zh_CN.js"></script>
<script type="text/javascript">
    $('.flexigrid').flexigrid({
        usepager: false,
        reload: false,
        columnControl: false,

        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i> 导出投标列表', name : 'cvs', bclass : 'csv', title : '导出投标列表', onpress : flexPress }
        ],
    });
    function flexPress(name, grid) {
        if(name=='cvs'){
            window.location.href = '<?php echo adminUrl('loan_loan','bidlist_export',array('deal_id'=>$loan_id));?>';
        }
    }
    function initList(){
        $.ajax({
            url:'<?php echo adminUrl('loan_loan','bidlist_json',array('loan_id'=>$loan_id));?>',
            type:'get',
            dataType:'json',
            success:function(msg){
                if(msg.code!=200){
                    jsprint(msg.message);
                    return;
                }
                fillFlexTable(msg.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                jsprint('网络也太差了吧！');
            }
        });
    }
    function fillFlexTable(data){
        var jsonhtml='{"rows":[';
        if(data.length != 0){
            $.each(data, function(key,val) {
                jsonhtml+='{"id":"'+key+'",';
                jsonhtml+='"cell":[';
                jsonhtml+='"'+val.user_name+'",';
                jsonhtml+='"￥'+val.money+'",';
                jsonhtml+='"'+val.is_auto+'",';
                jsonhtml+='"'+val.is_has_loans+'",';
                jsonhtml+='"'+val.is_repay+'",';
                jsonhtml+='"'+val.create_time+'",';
                jsonhtml+='""]},';
            });
            jsonhtml = jsonhtml.substring(0, jsonhtml.length - 1);
            jsonhtml+='],"total":"'+data.length+'"}';
        }else {
            jsonhtml+='],"total":"'+data.length+'"}';
        }
        $('.flexigrid').flexAddData(JSON.parse(jsonhtml));
    }
    var help_content="<?php echo \Core::L('sms_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
    };
    $(function () {
        //操作。选中不同类型，加载不同页面
        $(":radio").click(function(){
            var deal_status = $(this).val();
            if(deal_status == 1){
                $(".fullbid").css('display','block');
                $(".failsbid").css('display','none');
            }
            if(deal_status == 2 || deal_status == 3){
                $(".failsbid").css('display','block');
                $(".fullbid").css('display','none');
            }
        });

    });
    function nothingdo() {
        $(".failsbid").css('display','none');
        $(".fullbid").css('display','none');
        $(":radio").attr('checked',false);
    }
    //放款操作
    function do_loans(id) {
        var repay_start_time = $("#datestart").val();
        $.ajax({
            type: "GET",
            dataType: "json",
            //放款
            url: "<?php echo adminUrl('loan_loan','loans');?>",
            data: "id="+id+"&repay_start_time="+repay_start_time,
            success: function(data){
                if (data.code==200){
                    alert(data.message);
                    location.reload();
                } else {
                    jsprint(data.message);
                }
            }
        });
    }
    //流标返还操作
    function do_received(id) {
        $.ajax({
            type: "GET",
            dataType: "json",
            //流标返还
            url: "<?php echo adminUrl('loan_loan','received');?>",
            data: "id="+id,
            success: function(data){
                if (data.code==200){
                    $("#flexitable").flexReload();
                } else {
                    jsprint(data.message);
                }
            }
        });
    }
    initList();
    $.datetimepicker.setLocale('ch');
    $('#datestart').datetimepicker({format:"Y-m-d H:i:s",timepicker:true,todayButton:true});
    $(".bid_full").Validform();
</script>
</body>
</html>