<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title><?php echo \Core::L('upload_setting');?></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.js"></script>
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
    <span><a href="<?php echo adminUrl('loan_loan','index');?>"><?php echo \Core::L('loan_all');?></a></span>
    <i class="arrow"></i>
    <span><?php echo \Core::L('loan_add');?></span>
</div>
<div class="line10"></div>
<div class="page">
    <div id="floatHead"  class="content-tab-wrap">
        <div class="content-tab" >
            <div class="content-tab-ul-wrap" >
                <ul>
                    <li><a class="selected" href="javascript:;">基本信息</a></li>
                    <li><a href="javascript:;">相关参数</a></li>
                    <li><a href="javascript:;">相关资料</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form method="post" id="form1" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <!--基本信息-->
        <div class="tab-content">
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>借款编号</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" id="deal_sn" class="input-txt" readonly="readonly" value="<?php echo $loanbase['deal_sn'];?>">
                    <p class="notic">用于合同处的借款编号，不能重复</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>借款名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="name" id="name" class="input-txt" value="<?php echo $loanbase['name'];?>">
                    <p class="notic">借款名称</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>会员</label>
                </dt>
                <dd class="opt">
                    <?php echo $username;?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>其他平台注册情况</label>
                </dt>
                <dd class="opt">


                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>会员详情</label>
                </dt>
                <dd class="opt">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>芝麻信用</label>
                </dt>
                <dd class="opt">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>同盾决策</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>百融黑名单核查</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款类型</label>
                </dt>
                <dd class="opt">
                    <select name="type_id" id="type_id" value="-1">
                        <option value="-1">-请选择类型-</option>
                        <?php if($dealloantype){?>
                            <?php foreach($dealloantype as $k=>$v){?>
                                <?php if($k == $loanbase['use_type']) {?>
                                <?php echo "<option value='".$k."' selected='selected'>".$v['name']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$k."'>".$v['name']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
                    <p class="notic">借款类型</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>还款方式</label>
                </dt>
                <dd class="opt">
                    <select name="loantype" id="loantype" value="-1">
                        <option value="-1">-请选择还款方式-</option>
                        <?php if($loantype){?>
                            <?php foreach($loantype as $k=>$v){?>
                                <?php if($k == $loanbase['loantype']) {?>
                                <?php echo "<option value='".$k."'selected='selected'>".$v."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$k."'>".$v."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
                    <p class="notic">借款用户的还款方式</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款合同范本</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>咨询服务合同范本</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>转让合同范本</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" readonly="readonly" value="<?php echo $loanbase['borrow_amount'];?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险保证金（非托管标）</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投标类型</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>最低投标金额</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>最高投标金额</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款期限</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>年化利率</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="rate" id="rate" style="width: 100px;" readonly="readonly" value="<?php echo $loanbase['rate'];?>">%
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>筹标期限</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>是否纳入推荐奖励</label>
                </dt>
                <dd class="opt">
                    <?php echo $loanbase['is_referral_award']?'是':'否';?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>可否使用红包</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>贷款描述</label>
                </dt>
                <dd class="opt">
                    <textarea  cols="80" style="height: 100px;"></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险等级</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险控制</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>所在城市</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款用途</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款状态</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>排序</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
        </div>
        <!--相关参数-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>成交服务费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者利息管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者提前还款利息管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>普通逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>严重逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>普通逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>严重逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>债权转让管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>提前还款补偿</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资人返利</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deal_sn" readonly="true" id="deal_sn" class="input-txt" value="">
                </dd>
            </dl>
        </div>
        <!--相关资料-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>认证资料显示</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款签约合同</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>抵押物图片</label>
                </dt>
                <dd class="opt">

                </dd>
            </dl>
        </div>

        <div class="page-footer">
            <div class="btn-wrap">
                <input type="submit" name="btnSubmit" value="<?php echo \Core::L('submit');?>" id="btnSubmit" class="btn" />
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    var help_content="<?php echo \Core::L('loan_add_help');?>";
    function help(ctrl){
        var d = dialog({
            content: help_content,
            quickClose: true
        });
        d.show(ctrl);
    };
    //用户名自动补全
    $('#user_name').on('focus',function(){
        var obj = $(this);
        obj.autocomplete("<?php echo adminUrl('common','autoGetUsers')?>", {
            width:288,
            selectFirst: false,
            autoFill: false,    //自动填充
            dataType: "json",
            parse: function(data) {
                return $.map(data, function(row) {
                    return {
                        data: row,
                        value: row.user_name,
                        result: function(){
                            if (row.id > 0)
                                return row.user_name;
                            else
                                return "";
                        }
                    }
                });
            },
            formatItem: function(row, i, max) {
                return row.user_name + (row.real_name =="" ? "" : " [" + row.real_name + "]");
            }
        }).result(function(e,item) {
            $('#user_id').val(item.id);
            return item.id;
        });
    });
    $(function () {
        //初始化表单验证
        $("#form1").initValidform();
    });
</script>
</body>
</html>