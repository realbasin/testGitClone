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
    <span><?php echo $loanbase['name'];?></span>
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
    <form method="post" id="form1" name="form1" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="loan_id" value="<?php echo $loanbase['id'];?>" />
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
                    <?php echo $plathtml;?>

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>会员详情</label>
                </dt>
                <dd class="opt">
                    <?php echo $user_detail;?>
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
                                <?php if($k == $loanbase['type_id']) {?>
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
                    <select name="contract_id" id="contract_id" value="-1">
                        <option value="-1">-请选择合同范本-</option>
                        <?php if($contract){?>
                            <?php foreach($contract as $v){?>
                                <?php if($v['id'] == $contractid['contract_id']) {?>
                                    <?php echo "<option value='".$v['id']."'selected='selected'>".$v['title']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$v['id']."'>".$v['title']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>咨询服务合同范本</label>
                </dt>
                <dd class="opt">
                    <select name="scontract_id" id="scontract_id" value="-1">
                        <option value="-1">-请选择合同范本-</option>
                        <?php if($contract){?>
                            <?php foreach($contract as $v){?>
                                <?php if($v['id'] == $contractid['scontract_id']) {?>
                                    <?php echo "<option value='".$v['id']."'selected='selected'>".$v['title']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$v['id']."'>".$v['title']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>转让合同范本</label>
                </dt>
                <dd class="opt">
                    <select name="tcontract_id" id="tcontract_id" value="-1">
                        <option value="-1">-请选择合同范本-</option>
                        <?php if($contract){?>
                            <?php foreach($contract as $v){?>
                                <?php if($v['id'] == $contractid['tcontract_id']) {?>
                                    <?php echo "<option value='".$v['id']."'selected='selected'>".$v['title']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$v['id']."'>".$v['title']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
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
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" readonly="readonly" value="<?php echo $l_guarantees_amt;?>">
                    <p class="notic">冻结借款人的金额，满标放款时从用户账户扣除</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投标类型</label>
                </dt>
                <dd class="opt">
                    <select name="uloadtype" id="uloadtype" value="-1">
                        <option value="0" <?php echo $loanbid['uloadtype']?'':'selected="selected"';?>>按金额</option>
                        <option value="1" <?php echo $loanbid['uloadtype']?'selected="selected"':'';?>>按份额</option>
                    </select>
                </dd>
            </dl>
            <dl class="row loan_money" <?php echo $loanbid['uloadtype']?'style="display:none;"':'';?>>
                <dt class="tit">
                    <label>最低投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" value="<?php echo $loanbid['min_loan_money'];?>">
                </dd>
            </dl>
            <dl class="row loan_money" <?php echo $loanbid['uloadtype']?'style="display:none;"':'';?>>
                <dt class="tit">
                    <label>最高投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" value="<?php echo $loanbid['max_loan_money'];?>">
                </dd>
            </dl>
            <dl class="row loan_portion" <?php echo $loanbid['uloadtype']?'':'style="display:none;"';?>>
                <dt class="tit">
                    <label>分成多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" value="<?php echo $loanbid['portion'];?>">
                </dd>
            </dl>
            <dl class="row loan_portion" <?php echo $loanbid['uloadtype']?'':'style="display:none;"';?>>
                <dt class="tit">
                    <label>最高买多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" value="<?php echo $loanbid['max_portion'];?>">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款期限</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="borrow_amount" id="borrow_amount"  readonly="readonly" style="width: 80px;" value="<?php echo $loanbase['repay_time'];?>">
                    <?php echo $loanbase['repay_time_type']?'月':'天';?>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>年化利率</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="rate" id="rate" style="width: 80px;" readonly="readonly" value="<?php echo $loanbase['rate'];?>">%
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>筹标期限</label>
                </dt>
                <dd class="opt">
                    <?php echo ($loanbid['start_time'] == 0 || $loanbid['end_time'] == 0)?0:ceil(($loanbid['start_time'] - $loanbid['end_time'])/(24*60*60));?>天
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
                    <select name="use_ecv" id="use_ecv" value="-1">
                        <option value="0" <?php echo $loanbid['use_ecv']?'':'selected="selected"';?>>否</option>
                        <option value="1" <?php echo $loanbid['use_ecv']?'selected="selected"':'';?>>是</option>
                    </select>
                    <p class="notic">选“是”请将“最低投标金额”设置大于最大红包额度</p>
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
                    <select name="risk_rank">
                        <option value="0" <?php echo ($loanbid['risk_rank'] == 0)?'selected="selected"':'';?>>低</option>
                        <option value="1" <?php echo ($loanbid['risk_rank'] == 1)?'selected="selected"':'';?>>中</option>
                        <option value="2" <?php echo ($loanbid['risk_rank'] == 2)?'selected="selected"':'';?>>高</option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险控制</label>
                </dt>
                <dd class="opt">
                    <textarea  cols="80" style="height: 100px;"></textarea>
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
                    <select name="use_type" id="use_type" value="-1">
                        <option value="-1">-请选择类型-</option>
                        <?php if($dealusetype){?>
                            <?php foreach($dealusetype as $k=>$v){?>
                                <?php if($k == $loanbase['use_type']) {?>
                                    <?php echo "<option value='".$k."' selected='selected'>".$v['name']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$k."'>".$v['name']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款状态</label>
                </dt>
                <dd class="opt">
                    <?php echo $loanbid['deal_status'] == 1?'进行中':(($loanbid['deal_status']==2)?'满标':(($loanbid['deal_status']==3)?'流标':(($loanbid['deal_status']==4)?'还款中':(($loanbid['deal_status']==5)?'已还清':''))));?>
                    <a href="<?php echo adminUrl('loan_loan','detail',array('loan_id'=>$loanbid['loan_id']));?>">投标详情</a>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sort" id="sort" style="width: 80px;"  value="0">
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
                    <input type="text" name="services_fee" readonly="readonly" id="services_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('services_fee',$commonConfig)?\Core::arrayGet($commonConfig,'services_fee'):0;?>">%
                    <p class="notic">按发布时的会员等级</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_fee" readonly="readonly" id="manage_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('manage_fee',$commonConfig)?\Core::arrayGet($commonConfig,'manage_fee'):0;?>">%
                    <p class="notic">管理费 = 本金总额×管理费率 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_loan_manage_fee" readonly="readonly" id="user_loan_manage_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('user_loan_manage_fee',$commonConfig)?\Core::arrayGet($commonConfig,'user_loan_manage_fee'):0;?>">%
                    <p class="notic">管理费 = 投资总额×管理费率 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者利息管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_loan_interest_manage_fee" readonly="readonly" id="user_loan_interest_manage_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('user_loan_interest_manage_fee',$commonConfig)?\Core::arrayGet($commonConfig,'user_loan_interest_manage_fee'):0;?>">%
                    <p class="notic">管理费 = 实际得到的利息×管理费率 0即不收取(如果是VIP会员将从VIP会员配置里读取)</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资者提前还款利息管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_loan_early_interest_manage_fee" readonly="readonly" id="user_loan_early_interest_manage_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('user_loan_early_interest_manage_fee',$commonConfig)?\Core::arrayGet($commonConfig,'user_loan_early_interest_manage_fee'):0;?>">%
                    <p class="notic">管理费 = 提前还款的利息×管理费率 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>普通逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day1" readonly="readonly" id="manage_impose_fee_day1" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('manage_impose_fee_day1',$commonConfig)?\Core::arrayGet($commonConfig,'manage_impose_fee_day1'):0;?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>严重逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day2" readonly="readonly" id="manage_impose_fee_day2" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('manage_impose_fee_day2',$commonConfig)?\Core::arrayGet($commonConfig,'manage_impose_fee_day2'):0;?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额×对应逾期管理费率×逾期天数 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>普通逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day1" readonly="readonly" id="impose_fee_day1" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('impose_fee_day1',$commonConfig)?\Core::arrayGet($commonConfig,'impose_fee_day1'):0;?>">%
                    <p class="notic">罚息总额 = 逾期本息总额×对应罚息利率×逾期天数 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>严重逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day2" readonly="readonly" id="impose_fee_day2" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('impose_fee_day2',$commonConfig)?\Core::arrayGet($commonConfig,'impose_fee_day2'):0;?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额×对应罚息利率×逾期天数 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>债权转让管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_load_transfer_fee" readonly="readonly" id="user_load_transfer_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('user_load_transfer_fee',$commonConfig)?\Core::arrayGet($commonConfig,'user_load_transfer_fee'):0;?>">%
                    <p class="notic">管理费 = 转让金额×管理费率 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>提前还款补偿</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="compensate_fee" readonly="readonly" id="compensate_fee" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('compensate_fee',$commonConfig)?\Core::arrayGet($commonConfig,'compensate_fee'):0;?>">%
                    <p class="notic">补偿金额 = 剩余本金×补偿年化利率 0即不收取</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>投资人返利</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_bid_rebate" readonly="readonly" id="user_bid_rebate" style="width: 80px;" value="<?php echo \Core::arrayKeyExists('user_bid_rebate',$commonConfig)?\Core::arrayGet($commonConfig,'user_bid_rebate'):0;?>">%
                    <p class="notic">返利金额=投标金额×返利百分比【需满标】</p>
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
                    <label>借款签约合同 [ <a href="javascript:void(0);" onclick="add_mortgage_img('contract');">+</a> ] </label>
                </dt>
                <dd class="opt" id="contract">

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>抵押物图片 [ <a href="javascript:void(0);" onclick="add_mortgage_img('infos');">+</a> ] </label>

                </dt>
                <dd class="opt" id="infos">

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
    var contractnum = 1;
    var infosnum = 1;
    function help(ctrl){
        var d = dialog({
            content: help_content,
            quickClose: true
        });
        d.show(ctrl);
    };

    $('#uloadtype').on('change',function(){
        var uloadtype = $("#uloadtype option:selected").val();
        if(uloadtype == 0) {
            $(".loan_money").css('display','block');
            $(".loan_portion").css('display','none');
        }else if(uloadtype == 1) {
            $(".loan_money").css('display','none');
            $(".loan_portion").css('display','block');
        }else {
            return false;
        }
    });
    $(function () {
        //初始化表单验证
        $("#form1").initValidform();
    });
    function add_mortgage_img(type){
        var str = '';
        str += '名称：<input type="text" size="10" name="mortgage_'+type+'_name_';
        if(type == 'contract'){
            str += contractnum +'" id="mortgage_'+type+'_name_'+contractnum+'" value="">&nbsp;';
            str += '图片：<input type="file" name="mortgage_'+type+'_name_'+contractnum+'"><br>';
            contractnum = contractnum+1;
        }
        if(type == 'infos'){
            str += infosnum +'" id="mortgage_'+type+'_name_'+infosnum+'" value="">&nbsp;';
            str += '图片：<input type="file" name="mortgage_'+type+'_name_'+infosnum+'"><br>';
            infosnum = infosnum+1;
        }
        $("#"+type).append(str);
    }
</script>
</body>
</html>