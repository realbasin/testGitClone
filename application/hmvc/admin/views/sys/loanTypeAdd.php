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
    <link href="<?php echo RS_PATH?>jquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.datetimepicker.full.min.js"></script>
    <script charset="utf-8" src="<?php echo RS_PATH?>kindeditor-4.1.7/kindeditor.js"></script>

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
    <span><?php echo '新增贷款类型';?></span>
</div>
<div class="line10"></div>
<div class="page">
    <div id="floatHead"  class="content-tab-wrap">
        <div class="content-tab" >
            <div class="content-tab-ul-wrap" >
                <ul>
                    <li><a class="selected" href="javascript:;">借款类型编辑</a></li>
                    <li><a href="javascript:;">申请资料</a></li>
                    <li><a href="javascript:;">产品简介</a></li>
                    <li><a href="javascript:;">拓展配置</a></li>
                    <li><a href="javascript:;">SEO设置</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form method="post" id="form1" name="form1" action="?c=sys_loan&a=type_add&m=admin" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />

        <!--借款类型编辑start-->
        <div class="tab-content">
            <dl class="row">
                <dt class="tit">
                    <label>分类名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="name" id="name" class="input-txt" value="" datatype="*" nullmsg="请填写分类名称！">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>贷款缩略图</label>
                </dt>
                <dd class="opt">
                    <input type="file" name="icon" >
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>适宜人群</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="applyto" id="applyto" class="input-txt" value="">
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>背景色</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="uname" id="uname" class="input-txt" value="">
                    <p class="notic">不填即为默认颜色</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>简单描述</label>
                </dt>
                <dd class="opt">
                    <textarea name="brief" cols="80" style="height: 100px;" datatype="*" nullmsg="请填写简单描述！"></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>申请条件</label>
                </dt>
                <dd class="opt">
                    <textarea name="condition" cols="80" style="height: 100px;" datatype="*" nullmsg="请填写申请条件！"></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款用途</label>
                </dt>
                <dd class="opt">
                    <?php foreach($dealUserTypeList as $dealUserType){?>
                        <input type="checkbox" id="usetypes_<?php echo $dealUserType['id']; ?>" name="usetypes[]" value="<?php echo $dealUserType['id']; ?>" datatype="*" nullmsg="请选择借款用途" />
                        <label for="usetypes_<?php echo $dealUserType['id']; ?>"><?php echo $dealUserType['name'];?></label>&nbsp;&nbsp;
                    <?php }?>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>抵押物</label>
                </dt>
                <dd class="opt">
                    <?php foreach($collateralList as $collateral){?>
                        <input type="checkbox" id="collaterals_<?php echo $collateral['id']; ?>" name="collaterals[]" value="<?php echo $collateral['id']; ?>" />
                        <label for="collaterals_<?php echo $collateral['id']; ?>"><?php echo $collateral['name'];?></label>&nbsp;&nbsp;
                    <?php }?>
                    <p class="notic">（借款端借款申请页面的借款金额最大最小额度根据此项来获取：空-信用贷；不空-抵押贷）</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>类别</label>
                </dt>
                <dd class="opt">
                    <?php foreach($loanTypeList as $loanType){?>
                        <input type="radio" id="types_<?php echo $loanType['id']; ?>" name="types" value="<?php echo $loanType['id']; ?>" checked datatype="n" nullmsg="请选择类别" />
                        <label for="types_<?php echo $loanType['id']; ?>"><?php echo $loanType['name'];?></label>&nbsp;&nbsp;
                    <?php }?>
                    <p class="notic">（理财端信用标、抵押标的区分，根据此处的选择来确定；学生贷+信用贷=信用贷）</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否需要额度</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_quota" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否启用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_effect" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否显示</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_display" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否启用自动投标</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_autobid" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否可使用借款红包</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_use_ecv" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否可使用理财优惠券</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_use_bonus" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否纳入推荐奖励</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_referral_award" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否开启还清限制</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_pay_off_limit" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sort" class="input-txt" value="<?php echo $maxSort; ?>" datatype="n" nullmsg="请输入排序！">
                </dd>
            </dl>
        </div>
        <!--借款相关类型编辑end-->

        <!--申请资料start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>身份认证</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="id_is_effect" value="1" ><br/>

                    <input type="checkbox" id="idcard_name" name="idcard_name" value="1" ><label for="idcard_name">姓名</label>&nbsp;&nbsp;
                    <input type="checkbox" id="idcard_name_norequired" name="idcard_name_norequired" value="1" ><label for="idcard_name_norequired">选填</label>&nbsp;&nbsp;<br/>

                    <input type="checkbox" id="idcard_number" name="idcard_number" value="1" ><label for="idcard_number">身份证号码</label>&nbsp;&nbsp;
                    <input type="checkbox" id="idcard_number_norequired" name="idcard_number_norequired" value="1" ><label for="idcard_number_norequired">选填</label>&nbsp;&nbsp;<br/>

                    <input type="checkbox" id="idcard_front" name="idcard_front" value="1" ><label for="idcard_front">身份证正面照</label>&nbsp;&nbsp;
                    <input type="checkbox" id="idcard_front_norequired" name="idcard_front_norequired" value="1" ><label for="idcard_front_norequired">选填</label>&nbsp;&nbsp;<br/>

                    <input type="checkbox" id="home_addr" name="home_addr" value="1" ><label for="home_addr">家庭住址</label>&nbsp;&nbsp;
                    <input type="checkbox" id="home_addr_norequired" name="home_addr_norequired" value="1" ><label for="home_addr_norequired">选填</label>&nbsp;&nbsp;
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>教育认证</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="edu_is_effect" value="1" ><br/>

                    <input type="checkbox" id="hs_info" name="hs_info" value="1" ><label for="hs_info">高中学校</label>&nbsp;&nbsp;
                    <input type="checkbox" id="hs_info_norequired" name="hs_info_norequired" value="1" ><label for="hs_info_norequired">选填</label><br/>

                    <input type="checkbox" id="college_info" name="college_info" value="1" ><label for="college_info">大学学校</label>&nbsp;&nbsp;
                    <input type="checkbox" id="college_info_norequired" name="college_info_norequired" value="1" ><label for="college_info_norequired">选填</label><br/>

                    <input type="checkbox" id="xx_info" name="xx_info" value="1" ><label for="xx_info">学信网信息</label>&nbsp;&nbsp;
                    <input type="checkbox" id="xx_info_norequired" name="xx_info_norequired" value="1" ><label for="xx_info_norequired">选填</label><br/>

                    <input type="checkbox" id="jw_info" name="jw_info" value="1" ><label for="jw_info">教务管理系统信息</label>&nbsp;&nbsp;
                    <input type="checkbox" id="jw_info_norequired" name="jw_info_norequired" value="1" ><label for="jw_info_norequired">选填</label><br/>

                    <input type="checkbox" id="tb_info" name="tb_info" value="1" ><label for="tb_info">学费缴费单</label>&nbsp;&nbsp;
                    <input type="checkbox" id="tb_info_norequired" name="tb_info_norequired" value="1" ><label for="tb_info_norequired">选填</label><br/>

                    <input type="checkbox" id="notice_info" name="notice_info" value="1" ><label for="notice_info">录取通知书</label>&nbsp;&nbsp;
                    <input type="checkbox" id="notice_info_norequired" name="notice_info_norequired" value="1" ><label for="notice_info_norequired">选填</label><br/>

                    <input type="checkbox" id="studentIdCard_info" name="studentIdCard_info" value="1" ><label for="studentIdCard_info">学生证</label>&nbsp;&nbsp;
                    <input type="checkbox" id="studentIdCard_info_norequired" name="studentIdCard_info_norequired" value="1" ><label for="studentIdCard_info_norequired">选填</label><br/>

                    <input type="checkbox" id="campus_card_info" name="campus_card_info" value="1" ><label for="campus_card_info">一卡通</label>&nbsp;&nbsp;
                    <input type="checkbox" id="campus_card_info_norequired" name="campus_card_info_norequired" value="1" ><label for="campus_card_info_norequired">选填</label>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>联系信息</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="contact_is_effect" value="1" ><br/>

                    <input type="checkbox" id="contact_qq" name="contact_qq" value="1" ><label for="contact_qq">QQ号码</label>&nbsp;&nbsp;
                    <input type="checkbox" id="contact_qq_norequired" name="contact_qq_norequired" value="1" ><label for="contact_qq_norequired">选填</label>&nbsp;&nbsp;<br/>

                    <input type="checkbox" id="contact_wx" name="contact_wx" value="1" ><label for="contact_wx">微信号码</label>&nbsp;&nbsp;
                    <input type="checkbox" id="contact_wx_norequired" name="contact_wx_norequired" value="1" ><label for="contact_wx_norequired">选填</label>&nbsp;&nbsp;<br/>

                    <input type="checkbox" id="emergency_contact" name="emergency_contact" value="1" ><label for="emergency_contact">紧急联系人</label>&nbsp;&nbsp;<br/>

                    <div id="contact_div">
                        <div class="contact_wrap" data-index="1">
                            第1联系人&nbsp;&nbsp;
                            <input type="text" name="contact_arr[0]" value="父亲">&nbsp;&nbsp;
                            <input type="checkbox" id="company_arr[0]" name="company_arr[0]" value="1" ><label for="company_arr[0]">工作单位</label>&nbsp;&nbsp;
                            <input type="checkbox" id="contact_norequired_arr[0]" name="contact_norequired_arr[0]" value="1" ><label for="contact_norequired_arr[0]">选填</label>&nbsp;&nbsp;
                            <input type="button" class="input-btn" value="删除" onclick="contact_del(this);">
                        </div>

                        <div class="contact_wrap" data-index="2">
                            第2联系人&nbsp;&nbsp;
                            <input type="text" name="contact_arr[1]" value="母亲">&nbsp;&nbsp;
                            <input type="checkbox" id="company_arr[1]" name="company_arr[1]" value="1" ><label for="company_arr[1]">工作单位</label>&nbsp;&nbsp;
                            <input type="checkbox" id="contact_norequired_arr[1]" name="contact_norequired_arr[1]" value="1" ><label for="contact_norequired_arr[1]">选填</label>&nbsp;&nbsp;
                            <input type="button" class="input-btn" value="删除" onclick="contact_del(this);">
                        </div>

                        <div class="contact_wrap" data-index="3">
                            第3联系人&nbsp;&nbsp;
                            <input type="text" name="contact_arr[2]" value="直属主管">&nbsp;&nbsp;
                            <input type="checkbox" id="company_arr[2]" name="company_arr[2]" value="1" ><label for="company_arr[2]">工作单位</label>&nbsp;&nbsp;
                            <input type="checkbox" id="contact_norequired_arr[2]" name="contact_norequired_arr[2]" value="1" ><label for="contact_norequired_arr[2]">选填</label>&nbsp;&nbsp;
                            <input type="button" class="input-btn" value="删除" onclick="contact_del(this);">
                        </div>
                    </div>
                    <input type="button" class="input-btn" id="contact_add" value="添加">
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>工作信息</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="work_is_effect" value="1" ><br/>

                    <input type="checkbox" id="company_name" name="company_name" value="1" ><label for="company_name">公司全称</label><br/>

                    <input type="checkbox" id="company_addr" name="company_addr" value="1" ><label for="company_addr">公司地址</label><br/>

                    <input type="checkbox" id="company_station" name="company_station" value="1" ><label for="company_station">公司岗位</label><br/>

                    <input type="checkbox" id="company_telephone" name="company_telephone" value="1" ><label for="company_telephone">公司固话</label><br/>

                    <input type="checkbox" id="industry" name="industry" value="1" ><label for="industry">行业职业</label><br/>

                    <input type="checkbox" id="income_range" name="income_range" value="1" ><label for="income_range">收入范围</label>
                </dd>
            </dl>
        </div>
        <!--申请资料end-->

        <!--产品简介start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>产品简介</label>
                </dt>
                <dd class="opt">
                    <textarea id="content" name="content" style="width:700px;height:300px;"></textarea>
                </dd>
            </dl>
        </div>
        <!--产品简介end-->

        <!--拓展配置start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>是否启用配置</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_extend_effect" value="1">
                    <p class="notic">此开关亦控制信用等级参数 & SEO参数(信用等级请添加后再进行编辑)</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>发布城市</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="city_ids" class="input-txt" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <span id="daterange">
            <dl class="row">
                <dt class="tit">
                    <label>开始时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="start_time" id="start_time" class="input-txt" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>结束时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="end_time" id="end_time" class="input-txt" value="">
                    <p class="notic"></p>
                </dd>
            </dl>
            </span>

            <dl class="row">
                <dt class="tit">
                    <label>最小还款期限（月）</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="min_deadline" class="input-txt" value="">
                    <p class="notic">0表示不限制</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>还款期限（月）</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deadline" class="input-txt" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否推荐</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_recommend" value="1">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>Banner图</label>
                </dt>
                <dd class="opt">
                    <input type="file" name="banner">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款保证金</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantees_amt" style="width: 80px;" value="">%
                    <p class="notic">借款保证金 = 借款金额 × 借款保证金比率【放款时冻结，如无逾期记录，还款完成时返还至用户账户】</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>担保金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantor_amt" style="width: 80px;" value="">
                    <p class="notic">担保方，担保金额(代偿金额累计不能大于担保金额)</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>担保收益</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantor_pro_fit_amt" style="width: 80px;" value="">%
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_fee" style="width: 80px;" value="">%
                    <p class="notic">管理费 = 本金总额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投资者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_loan_manage_fee" style="width: 80px;" value="">%
                    <p class="notic">管理费 = 投资总额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>普通逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day1" style="width: 80px;" value="">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>严重逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day2" style="width: 80px;" value="">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>普通逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day1" style="width: 80px;" value="">%
                    <p class="notic">罚息总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>严重逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day2" style="width: 80px;" value="">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最小额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="minimum" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最大额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="maximum" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>债权转让管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_load_transfer_fee" style="width: 80px;" value="">%
                    <p class="notic">管理费 = 转让金额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>提前还款补偿</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="compensate_fee" style="width: 80px;" value="">%
                    <p class="notic">补偿金额 = 剩余本金 × 补偿年化利率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投资人返利</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_bid_rebate" style="width: 80px;" value="">%
                    <p class="notic">返利金额 = 投标金额 × 返利百分比【需满标】</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最低投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="min_loan_money" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最高投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="max_loan_money" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>申请限制金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="limit_loan_money" placeholder="100" style="width: 80px;" value="">
                    <p class="notic">限制申请金额是否满足此数的整数倍，使用全局配置请填0</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投标限制金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="limit_bid_money" placeholder="100" style="width: 80px;" value="">
                    <p class="notic">限制投标金额是否满足此数的整数倍（此限制将会覆盖最低投标金额），用全局配置请填0</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款限制时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="loan_limit_time" placeholder="5" style="width: 80px;" value="">天
                    <p class="notic">如有未通过审核的借款，则在此限制之前将不能再提交新的申请</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>申请延期的额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="generation_position" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>用户投标类型</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="uloadtype" id="uloadtype_0" value="0" ><label for="uloadtype_0">按金额</label>&nbsp;&nbsp;
                    <input type="radio" name="uloadtype" id="uloadtype_1" value="1" ><label for="uloadtype_1">按份数</label>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>分成多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="portion" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最多买多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="max_portion" style="width: 80px;" value="">
                    <p class="notic"></p>
                </dd>
            </dl>
        </div>
        <!--拓展配置end-->

        <!--SEO设置start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>贷款SEO自定义标题</label>
                </dt>
                <dd class="opt">
                    <textarea name="seo_title" cols="80" style="height: 100px;"></textarea>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>贷款SEO自定义关键词</label>
                </dt>
                <dd class="opt">
                    <textarea name="seo_keyword" cols="80" style="height: 100px;"></textarea>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>贷款SEO自定义描述</label>
                </dt>
                <dd class="opt">
                    <textarea name="seo_description" cols="80" style="height: 100px;"></textarea>
                </dd>
            </dl>
        </div>
        <!--SEO设置end-->

        <div class="page-footer">
            <div class="btn-wrap">
                <input type="submit" name="btnSubmit" value="<?php echo \Core::L('submit');?>" id="btnSubmit" class="btn" />
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    KindEditor.ready(function(K) {
        window.editor = K.create('#content');
    });

    $.datetimepicker.setLocale('ch');
    $('#start_time,#end_time').datetimepicker({format:"Y-m-d",timepicker:false,todayButton:false});

    var help_content="<?php echo \Core::L('loan_add_help');?>";
    function help(ctrl){
        var d = dialog({
            content: help_content,
            quickClose: true
        });
        d.show(ctrl);
    }

    $(function () {
        //初始化表单验证
        $("#form1").initValidform();
    });

    $('#contact_add').on('click',function(){
        var maxIndex = 0;
        $.each( $('.contact_wrap'), function(i, obj){
            var currentIndex = parseInt($(obj).data('index'));
            if(currentIndex > maxIndex){
                maxIndex = currentIndex;
            }
        });
        maxIndex+=1;
        var html = '<div class="contact_wrap" data-index="'+maxIndex+'">'+
            '第'+maxIndex+'联系人&nbsp;&nbsp;'+
        '<input type="text" id="contact_arr['+maxIndex+']" name="contact_arr['+maxIndex+']" placeholder="">&nbsp;&nbsp;'+
        '<input type="checkbox" id="company_arr['+maxIndex+']" name="company_arr['+maxIndex+']" value="1"><label for="company_arr['+maxIndex+']">工作单位</label>&nbsp;&nbsp;'+
        '<input type="checkbox" id="contact_norequired_arr['+maxIndex+']" name="contact_norequired_arr['+maxIndex+']" value="1"><label for="contact_norequired_arr['+maxIndex+']">选填</label>&nbsp;&nbsp;'+
        '<input type="button" class="input-btn" value="删除" onclick="contact_del(this);">'+
        '</div>';
        $('#contact_div').append(html);
    });

    function contact_del(obj){
        $(obj).parent().remove();
    }

</script>
</body>
</html>