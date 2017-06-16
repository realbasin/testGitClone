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
    <span><?php echo \Core::L('audit');?></span>
    <i class="arrow"></i>
    <span><a href="<?php echo adminUrl('loan_audit',$action);?>"><?php echo $title;?></a></span>
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
    <form method="post" id="form1" name="form1" action="<?php echo adminUrl('loan_audit','first_publish_update',array('first_yn'=>$first_yn,'loan_id'=>$loanbase['id'])); ?>" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="loan_id" value="<?php echo $loanbase['id'];?>" />
        <input type="hidden" name="update_time" value="<?php echo $loanbase['update_time'];?>" />
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
                    <input type="text" name="name" id="name" class="input-txt" readonly="readonly" value="<?php echo $loanbase['name'];?>">
                    <a href="<?php echo adminUrl('loan_audit','publish_edit_loan_type',array('loan_id'=>$loanbase['id'],'first_yn'=>$first_yn));?>"><b>修改贷款类型</b></a>
                    <p class="notic">借款名称</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>简短名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sub_name" id="sub_name" class="input-txt" readonly="readonly" value="<?php echo $loanbase['sub_name'];?>">
                    <p class="notic">简短名称</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em>会员</label>
                    <input type="hidden" name="user_id" value="<?php echo $loanbase['user_id'];?>">
                </dt>
                <dd class="opt">
                    <?php echo $username;?>
                    <a>资料认证(<?php echo $passed; ?>)</a>
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
                    <label>分类</label>
                </dt>
                <dd class="opt">
                    <select name="dealcate" id="dealcate" onfocus="this.defaultIndex=this.selectedIndex;" onchange="this.selectedIndex=this.defaultIndex;" value="-1">
                        <option value="-1">-请选择分类-</option>
                        <?php if($dealcate){?>
                            <?php foreach($dealcate as $k=>$v){?>
                                <?php if($k == $loanbase['cate_id']) {?>
                                    <?php echo "<option value='".$k."'selected='selected'>".$v['name']."</option>";?>
                                <?php }?>
                                <?php echo "<option value='".$k."'>".$v['name']."</option>";?>
                            <?php }?>
                        <?php }?>
                    </select>
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
                                <?php if($v['id'] == \Core::arrayGet($contractid,'contractid','')) {?>
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
                                <?php if($v['id'] == \Core::arrayGet($contractid,'scontract_id','')) {?>
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
                                <?php if($v['id'] == \Core::arrayGet($contractid,'tcontract_id','')) {?>
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
                    <input type="text" name="borrow_amount" id="borrow_amount" class="input-txt" value="<?php echo $loanbase['borrow_amount'];?>">
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
                    <label>借款期限</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="repay_time" id="repay_time"  readonly="readonly" style="width: 80px;" value="<?php echo $loanbase['repay_time'];?>">
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
                    <label>是否纳入推荐奖励</label>
                </dt>
                <dd class="opt">
                    <?php echo $loanbase['is_referral_award']?'是':'否';?>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>贷款描述</label>
                </dt>
                <dd class="opt">
                    <textarea  cols="80" name="description" style="height: 100px;"></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险等级</label>
                </dt>
                <dd class="opt">
                    <select name="risk_rank">
                        <option value="0" <?php echo (\Core::arrayGet($loanbase,'risk_rank') == 0)?'selected="selected"':'';?>>低</option>
                        <option value="1" <?php echo (\Core::arrayGet($loanbase,'risk_rank') == 1)?'selected="selected"':'';?>>中</option>
                        <option value="2" <?php echo (\Core::arrayGet($loanbase,'risk_rank') == 2)?'selected="selected"':'';?>>高</option>
                    </select>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>风险控制</label>
                </dt>
                <dd class="opt">
                    <textarea  cols="80" name="risk_security" style="height: 100px;"></textarea>
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
            <!--<dl class="row">
                <dt class="tit">
                    <label>排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sort" id="sort" style="width: 80px;"  value="0">
                </dd>
            </dl>-->
            <dl class="row">
                <dt class="tit">
                    <label>审核状态</label>
                </dt>
                <dd class="opt">
                    <label>审核失败<input type="radio" name="is_delete" value="3" /> </label>
                    <label>审核成功<input type="radio" name="publish_wait" value="2" /> </label>
                </dd>
            </dl>
            <dl class="row" id="delele_msg_box" style="display:none">
                <dt class="tit">
                    <label>短信回复:</label>
                </dt>
                <dd class="opt">
                    <select name="delete_msg">
                        <option value="综合评分不足">综合评分不足</option>
                        <option value="资料不完备">资料不完备</option>
                        <option value="暂不支持成人高等教育">暂不支持成人高等教育</option>
                        <option value="借款意愿变更">借款意愿变更</option>
                    </select>
                </dd>
            </dl>
            <dl class="row" id="delete_real_msg_box" style="display:none">
                <dt class="tit">
                    <label>真实原因:</label>
                </dt>
                <dd class="opt">
                    <select name="delete_real_msg">
                        <option value="">请选择</option>
                        <option value="学籍与产品不符">学籍与产品不符</option>
                        <option value="已毕业">已毕业</option>
                        <option value="偿还能力不足">偿还能力不足</option>
                        <option value="成人教育">成人教育</option>
                        <option value="网络教育">网络教育</option>
                        <option value="待审核">待审核</option>
                        <option value="当前逾期">当前逾期</option>
                        <option value="电话审核失败">电话审核失败</option>
                        <option value="风险客户">风险客户</option>
                        <option value="网贷黑名单">网贷黑名单</option>
                        <option value="小树黑名单">小树黑名单</option>
                        <option value="客户不需要">客户不需要</option>
                        <option value="没接电话">没接电话</option>
                        <option value="没有学信网">没有学信网</option>
                        <option value="偏远地区">偏远地区</option>
                        <option value="已实习">已实习</option>
                        <option value="视频审核失败">视频审核失败</option>
                        <option value="资料虚假">资料虚假</option>
                        <option value="学信网没有照片">学信网没有照片</option>
                        <option value="资料不齐全">资料不齐全</option>
                        <option value="用户不需要">用户不需要</option>
                        <option value="客户不配合">客户不配合</option>
                        <option value="客户态度不好">客户态度不好</option>
                        <option value="严重逾期">严重逾期</option>
                        <option value="风险分数过高">风险分数过高</option>
                        <option value="学籍不符">学籍不符</option>
                        <option value="休学">休学</option>
                        <option value="借贷平台较多">借贷平台较多</option>
                        <option value="负债高">负债高</option>
                        <option value="客户不提供其他平台账号密码">客户不提供其他平台账号密码</option>
                        <option value="没有身份证">没有身份证</option>
                        <option value="没有服务密码">没有服务密码</option>
                        <option value="芝麻信用分低于550">芝麻信用分低于550</option>
                        <option value="没有还款记录">没有还款记录</option>
                        <option value="入学年份不符">入学年份不符</option>
                        <option value="未满18岁">未满18岁</option>
                        <option value="资料审核失败">资料审核失败</option>
                        <option value="资料不全">资料不全</option>
                        <option value="通话记录不足3个月">通话记录不足3个月</option>
                        <option value="手机号码非实名制">手机号码非实名制</option>
                        <option value="3个月内申请平台超过16家">3个月内申请平台超过16家</option>
                        <option value="联系人电话审核失败">联系人电话审核失败</option>
                        <option value="优才贷">优才贷</option>
                        <option value="毕业年份不符">毕业年份不符</option>
                        <option value="工作单位审核失败">工作单位审核失败</option>
                    </select>
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
                    <label>认证资料显示 [ <a href="javascript:void(0);" onclick="add_mortgage_img('view_info');">+</a> ] </label>
                </dt>
                <dd class="opt" id="view_info">

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
    var viewinfonum = 1;
    var contractnum = 1;
    var infosnum = 1;
    function help(ctrl){
        var d = dialog({
            content: help_content,
            quickClose: true
        });
        d.show(ctrl);
    };

    function goback(){
        window.history.back(-1);
    }
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
    function add_mortgage_img(type){
        var str = '';
        str += '名称：<input type="text" size="10" name="mortgage_'+type+'_name_';
        if(type == 'view_info'){
            str += viewinfonum +'" id="mortgage_'+type+'_name_'+viewinfonum+'" value="">&nbsp;';
            str += '图片：<input type="file" name="mortgage_'+type+'_name_'+viewinfonum+'"><br>';
            viewinfonum = viewinfonum+1;
        }
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
    $('input[name=is_delete]').click(function() {
        var no_region = $("#no_region");
        if (no_region.length > 0) {
            alert("确定不匹配贷款所在城市");
        }
    });
    $("input[name='publish_wait']").live("click",function(){
        $("input[name='is_delete']").attr("checked",false);
        $("#delele_msg_box,#delete_real_msg_box").hide();
    });

    $("input[name='is_delete']").click(function(){
        if ($(this).val() == "3") {
            $("input[name='publish_wait']").attr("checked",false);
            $("#delele_msg_box,#delete_real_msg_box").show();
        }
        return true;
    });
    //todo checkform

</script>
</body>
</html>