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
<link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js?v=201705041335"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
<!--[if lt IE 9]>
      <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>html5.js"></script>
<![endif]-->

</head>
<body class="mainbody">
<div class="location">
	<div  class="right"><a href="javascript:void(null);" onclick="help(this);"  onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
  <i class="home"></i>
  <span><?php echo \Core::L('loan');?></span>
  <i class="arrow"></i>
  <span><?php echo \Core::L('loan_detail');?></span>
  
</div>
<div class="line10"></div>
<div class="page">
    <div class="form-default" >
    <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_loan_name');?>：</label>
        </dt>
        <dd class="opt" style="font-size:16px;">
            <?php echo $loan['name'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_up_time');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo $loan['start_time']!=0?date('Y-m-d H:i:s',$loan['start_time']):'';?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_all_borrow');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo '￥'.$loan['borrow_amount'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_loan_money');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo '￥'.$loan['load_money'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_loan_need_money');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo '￥'.$loan['need_money'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('loan_detail_repay_time_type');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo $loan['repay_time_type'];?>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit" style="font-size: 16px;">
          <label><?php echo \Core::L('operate');?>：</label>
        </dt>
        <dd class="opt" style="font-size: 16px;">
            <?php echo $loan['deal_status']==11?'<font color="red">问题标，请联系技术处理</font>':($loan['need_money']<0?'<font color="red">问题标，请联系技术处理</font>':'')?>
            <?php echo ($loan['deal_status']==2&&$loan['is_has_loans']==0)?($loan['need_money']>0?'<label><input type="radio" name="deal_status" value="0" disabled/>满标放款</lable>':'<label><input type="radio" name="deal_status" value="0"/>满标放款</lable>'):''?>
            <?php echo (($loan['deal_status']==3||$loan['deal_status']==2||($loan['deal_status']==1&&$loan['is_over_time']==1)||$loan['deal_status']==1||$loan['deal_status']==0)&&$loan['is_has_received']==0)?($loan['buy_count']>0?'<label><input type="radio" name="deal_status" value="0"/>流标返还</lable>':'<label><input type="radio" name="deal_status" value="0"/>流标</lable>'):''?>
            <input type="button" value="导出投标列表" name="send_sms" class="input-btn" id="send_sms">
        </dd>
      </dl>
        <dl class="row">
            <dt class="tit" style="font-size: 16px;">
                <label><?php echo \Core::L('loan_detail_loan_time');?>：</label>
            </dt>
            <dd class="opt" style="font-size: 16px;">
                <?php echo $loan['loan_time']!=0?date('Y-m-d H:i:s',$loan['loan_time']):'';?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit" style="font-size: 16px;">
                <label><?php echo \Core::L('loan_detail_repay_start_time');?>：</label>
            </dt>
            <dd class="opt" style="font-size: 16px;">
                <?php echo $loan['repay_start_time']!=0?date('Y-m-d H:i:s',$loan['repay_start_time']):'';?>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit" style="font-size: 16px;">
                <label><?php echo \Core::L('loan_detail_loan_list');?>：</label>
            </dt>
            <dd class="opt" style="font-size: 16px;">
                <table id="dataTable" class="dataTable" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr class="row">
                        <th>投标人</th>
                        <th>投标金额</th>
                        <th>状态</th>
                        <th >是否转账</th>
                        <th >流标返还</th>
                        <th >投标时间</th>
                    </tr>
                    <tr>
                        <td>1111</td>
                        <td align="left">2222</td>
                        <td align="left">自动</td>
                        <td align="left"> 已转账</td>
                        <td align="left">无返还</td>
                        <td align="left">2017-01-01</td>
                    </tr>
                    <tr>
                        <td>1111</td>
                        <td align="left">2222</td>
                        <td align="left">自动</td>
                        <td align="left"> 已转账</td>
                        <td align="left">无返还</td>
                        <td align="left">2017-01-01</td>
                    </tr>
                    <tr>
                        <td>1111</td>
                        <td align="left">2222</td>
                        <td align="left">自动</td>
                        <td align="left"> 已转账</td>
                        <td align="left">无返还</td>
                        <td align="left">2017-01-01</td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="blank5"></div>

                            <div class="page">

                            </div>
                        </td>
                    </tr>
                </table>
            </dd>
        </dl>
      </div>
      <div class="page-footer">

</div>
</div>
<script type="text/javascript">
	var help_content="<?php echo \Core::L('sms_help');?>";
	function help(ctrl){
		var d = dialog({
        content: help_content,
        quickClose: true
        });
       d.show(ctrl);
};
</script>
</body>
</html>