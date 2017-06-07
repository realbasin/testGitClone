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
    <link href="<?php echo RS_PATH?>jquery/jquery.daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/laymain.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/Validform_v5.3.2_min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.autocomplete.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
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
                    <li><a href="javascript:;">信用等级</a></li>
                    <li><a href="javascript:;">审核设置</a></li>
                </ul>
            </div>
        </div>
    </div>
    <form method="post" id="form1" name="form1" action="?c=sys_loan&a=type_edit&m=admin" enctype="multipart/form-data">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="id" value="<?php echo $dealLoanType['id']; ?>" />

        <!--借款类型编辑start-->
        <div class="tab-content">
            <dl class="row">
                <dt class="tit">
                    <label>分类名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="name" id="name" class="input-txt" value="<?php echo $dealLoanType['name']; ?>" datatype="*" nullmsg="请填写分类名称！">
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>贷款缩略图</label>
                </dt>
                <dd class="opt">
                    <input type="file" name="icon" >
                    <?php if($dealLoanType['icon'] != ''){ ?>
                        <?php $imgPath =  './upload/'.$dealLoanType['icon']; ?>
                        <?php echo '<img src="'.$imgPath.'" width="30" height="30" />'; ?>
                    <?php } ?>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>适宜人群</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="applyto" class="input-txt" value="<?php echo $dealLoanType['applyto']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>背景色</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="uname" id="uname" class="input-txt" value="<?php echo $dealLoanType['uname']; ?>">
                    <p class="notic">不填即为默认颜色</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>简单描述</label>
                </dt>
                <dd class="opt">
                    <textarea name="brief" cols="80" style="height: 100px;" datatype="*" nullmsg="请填写简单描述！"><?php echo $dealLoanType['brief']; ?></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>申请条件</label>
                </dt>
                <dd class="opt">
                    <textarea name="condition" cols="80" style="height: 100px;" datatype="*" nullmsg="请填写申请条件！"><?php echo $dealLoanType['condition']; ?></textarea>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label>借款用途</label>
                </dt>
                <dd class="opt">
                    <?php $selectedUserTypeList = explode(',',$dealLoanType['usetypes']); ?>
                    <?php foreach($dealUserTypeList as $dealUserType){?>
                        <?php if(in_array($dealUserType['id'],$selectedUserTypeList)){ ?>
                            <?php echo "<input type=\"checkbox\" name=\"usetypes[]\" value='".$dealUserType['id']."' checked />".$dealUserType['name'];?>
                        <?php }else{ ?>
                            <?php echo "<input type=\"checkbox\" name=\"usetypes[]\" value='".$dealUserType['id']."'  />".$dealUserType['name'];?>
                        <?php } ?>
                    <?php }?>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>抵押物</label>
                </dt>
                <dd class="opt">
                    <?php $selectedCollateralList = explode(',',$dealLoanType['collaterals']); ?>
                    <?php foreach($collateralList as $collateral){?>
                        <?php if(in_array($collateral['id'],$selectedCollateralList)){ ?>
                            <?php echo "<input type=\"checkbox\" name=\"collaterals[]\" value='".$collateral['id']."' checked />".$collateral['name'];?>
                        <?php }else{ ?>
                            <?php echo "<input type=\"checkbox\" name=\"collaterals[]\" value='".$collateral['id']."' />".$collateral['name'];?>
                        <?php } ?>
                    <?php }?>
                    <p class="notic">（借款端借款申请页面的借款金额最大最小额度根据此项来获取：空-信用贷；不空-抵押贷）</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>类别</label>
                </dt>
                <dd class="opt">
                    <?php $selectedLoanTypeList = explode(',',$dealLoanType['types']); ?>
                    <?php foreach($loanTypeList as $loanType){?>
                        <?php if(in_array($loanType['id'],$selectedLoanTypeList)){ ?>
                            <?php echo "<input type=\"radio\" name=\"types\" value='".$loanType['id']."' checked />".$loanType['name'];?>
                        <?php }else{ ?>
                            <?php echo "<input type=\"radio\" name=\"types\" value='".$loanType['id']."' />".$loanType['name'];?>
                        <?php } ?>
                    <?php }?>
                    <p class="notic">（理财端信用标、抵押标的区分，根据此处的选择来确定；学生贷+信用贷=信用贷）</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否需要额度</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_quota" value="1" <?php echo $dealLoanType['is_quota']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否启用</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_effect" value="1" <?php echo $dealLoanType['is_effect']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否显示</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_display" value="1" <?php echo $dealLoanType['is_display']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否启用自动投标</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_autobid" value="1" <?php echo $dealLoanType['is_autobid']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否可使用借款红包</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_use_ecv" value="1" <?php echo $dealLoanType['is_use_ecv']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否可使用理财优惠券</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_use_bonus" value="1" <?php echo $dealLoanType['is_use_bonus']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否纳入推荐奖励</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_referral_award" value="1" <?php echo $dealLoanType['is_referral_award']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否开启还清限制</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_pay_off_limit" value="1" <?php echo $dealLoanType['is_pay_off_limit']?'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>排序</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="sort" class="input-txt" value="<?php echo $dealLoanType['sort']; ?>" datatype="n" nullmsg="请输入排序！">
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
                    <?php $identityAuth = json_decode($dealLoanType['identity_auth'],true); ?>
                    <input type="checkbox" class="js-switch blue" name="id_is_effect" value="1" <?php echo $identityAuth['id_is_effect']?'checked':''; ?>><br/>
                    <input type="checkbox" name="idcard_name" value="1" <?php echo $identityAuth['idcard_name']?'checked':''; ?>>姓名<input type="checkbox" name="idcard_name_norequired" value="1" <?php echo $identityAuth['idcard_name_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="idcard_number" value="1" <?php echo $identityAuth['idcard_number']?'checked':''; ?>>身份证号码<input type="checkbox" name="idcard_number_norequired" value="1" <?php echo $identityAuth['idcard_number_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="idcard_front" value="1" <?php echo $identityAuth['idcard_front']?'checked':''; ?>>身份证正面照<input type="checkbox" name="idcard_front_norequired" value="1" <?php echo $identityAuth['idcard_front_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="home_addr" value="1" <?php echo $identityAuth['home_addr']?'checked':''; ?>>家庭住址<input type="checkbox" name="home_addr_norequired" value="1" <?php echo $identityAuth['home_addr_norequired']?'checked':''; ?>>选填
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>教育认证</label>
                </dt>
                <dd class="opt">
                    <?php $educationAuth = json_decode($dealLoanType['education_auth'],true); ?>
                    <input type="checkbox" class="js-switch blue" name="edu_is_effect" value="1" <?php echo $educationAuth['edu_is_effect']?'checked':''; ?>><br/>
                    <input type="checkbox" name="hs_info" value="1" <?php echo $educationAuth['hs_info']?'checked':''; ?>>高中学校<input type="checkbox" name="hs_info_norequired" value="1" <?php echo $educationAuth['hs_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="college_info" value="1" <?php echo $educationAuth['college_info']?'checked':''; ?>>大学学校<input type="checkbox" name="college_info_norequired" value="1" <?php echo $educationAuth['college_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="xx_info" value="1" <?php echo $educationAuth['xx_info']?'checked':''; ?>>学信网信息<input type="checkbox" name="xx_info_norequired" value="1" <?php echo $educationAuth['xx_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="jw_info" value="1" <?php echo $educationAuth['jw_info']?'checked':''; ?>>教务管理系统信息<input type="checkbox" name="jw_info_norequired" value="1" <?php echo $educationAuth['jw_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="tb_info" value="1" <?php echo $educationAuth['tb_info']?'checked':''; ?>>学费缴费单<input type="checkbox" name="tb_info_norequired" value="1" <?php echo $educationAuth['tb_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="notice_info" value="1" <?php echo $educationAuth['notice_info']?'checked':''; ?>>录取通知书<input type="checkbox" name="notice_info_norequired" value="1" <?php echo $educationAuth['notice_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="studentIdCard_info" value="1" <?php echo $educationAuth['studentIdCard_info']?'checked':''; ?>>学生证<input type="checkbox" name="studentIdCard_info_norequired" value="1" <?php echo $educationAuth['studentIdCard_info_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="campus_card_info" value="1" <?php echo $educationAuth['campus_card_info']?'checked':''; ?>>一卡通<input type="checkbox" name="campus_card_info_norequired" value="1" <?php echo $educationAuth['campus_card_info_norequired']?'checked':''; ?>>选填
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>联系信息</label>
                </dt>
                <dd class="opt">
                    <?php $relationInfo = json_decode($dealLoanType['relation_info'],true); ?>
                    <input type="checkbox" class="js-switch blue" name="contact_is_effect" value="1" <?php echo $relationInfo['contact_is_effect']?'checked':''; ?>><br/>
                    <input type="checkbox" name="contact_qq" value="1" <?php echo $relationInfo['contact_qq']?'checked':''; ?>>QQ号码<input type="checkbox" name="contact_qq_norequired" value="1" <?php echo $relationInfo['contact_qq_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="contact_wx" value="1" <?php echo $relationInfo['contact_wx']?'checked':''; ?>>微信号码<input type="checkbox" name="contact_wx_norequired" value="1" <?php echo $relationInfo['contact_wx_norequired']?'checked':''; ?>>选填<br/>
                    <input type="checkbox" name="emergency_contact" value="1" <?php echo $relationInfo['emergency_contact']?'checked':''; ?>>紧急联系人<br/>

                    <div id="contact_div">
                        <?php $contactInfoList = $relationInfo['contact_info']; ?>
                        <?php foreach($contactInfoList as $key => $value){ ?>
                            <div class="contact_wrap" data-index="<?php echo $key + 1; ?>">
                                第<?php echo $key + 1; ?>联系人&nbsp;
                                <input type="text" name="contact_arr[<?php echo $key; ?>]" value="<?php echo $value['contact']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="company_arr[<?php echo $key; ?>]" value="1" <?php echo $value['company'] ? 'checked':''; ?>>工作单位&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="contact_norequired_arr[<?php echo $key; ?>]" value="1" <?php echo $value['contact_norequired'] ? 'checked':''; ?>>选填&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="button" class="input-btn" value="删除" onclick="contact_del(this);">
                            </div>
                        <?php } ?>
                    </div>
                    <input type="button" class="input-btn" id="contact_add" value="添加">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>工作信息</label>
                </dt>
                <dd class="opt">
                    <?php $workInfo = json_decode($dealLoanType['work_info'],true); ?>
                    <input type="checkbox" class="js-switch blue" name="work_is_effect" value="1" <?php echo $workInfo['work_is_effect'] ? 'checked':''; ?>><br/>
                    <input type="checkbox" name="company_name" value="1" <?php echo $workInfo['company_name'] ? 'checked':''; ?>>公司全称<br/>
                    <input type="checkbox" name="company_addr" value="1" <?php echo $workInfo['company_addr'] ? 'checked':''; ?>>公司地址<br/>
                    <input type="checkbox" name="company_station" value="1" <?php echo $workInfo['company_station'] ? 'checked':''; ?>>公司岗位<br/>
                    <input type="checkbox" name="company_telephone" value="1" <?php echo $workInfo['company_telephone'] ? 'checked':''; ?>>公司固话<br/>
                    <input type="checkbox" name="industry" value="1" <?php echo $workInfo['industry'] ? 'checked':''; ?>>行业职业<br/>
                    <input type="checkbox" name="income_range" value="1" <?php echo $workInfo['income_range'] ? 'checked':''; ?>>收入范围
                    <p class="notic"></p>
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
                    <textarea id="content" name="content" style="width:700px;height:300px;"><?php echo $dealLoanType['content']; ?></textarea>
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
                    <input type="checkbox" class="js-switch blue" name="is_extend_effect" value="1" <?php echo $dealLoanType['is_extend_effect'] ? 'checked':''; ?>>
                    <p class="notic">此开关亦控制信用等级参数 & SEO参数(信用等级请添加后再进行编辑)</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>发布城市</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="city_ids" class="input-txt" value="<?php echo $dealLoanTypeExtern['city_ids']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <span id="daterange">
            <dl class="row">
                <dt class="tit">
                    <label>开始时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="start_time" id="start_time" class="input-txt" value="<?php echo date('Y-m-d',$dealLoanTypeExtern['start_time']); ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>结束时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="end_time" id="end_time" class="input-txt" value="<?php echo date('Y-m-d',$dealLoanTypeExtern['end_time']); ?>">
                    <p class="notic"></p>
                </dd>
            </dl>
            </span>

            <dl class="row">
                <dt class="tit">
                    <label>最小还款期限（月）</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="min_deadline" class="input-txt" value="<?php echo $dealLoanTypeExtern['min_deadline']; ?>">
                    <p class="notic">0表示不限制</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>还款期限（月）</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="deadline" class="input-txt" value="<?php echo $dealLoanTypeExtern['deadline']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>是否推荐</label>
                </dt>
                <dd class="opt">
                    <input type="checkbox" class="js-switch blue" name="is_recommend" value="1" <?php echo $dealLoanTypeExtern['is_recommend']? 'checked':''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>Banner图</label>
                </dt>
                <dd class="opt">
                    <input type="file" name="banner">
                    <?php if($dealLoanTypeExtern['banner'] != ''){ ?>
                        <?php $imgPath =  './upload/'.$dealLoanTypeExtern['banner']; ?>
                        <?php echo '<img src="'.$imgPath.'" width="30" height="30" />'; ?>
                    <?php } ?>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款保证金</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantees_amt" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['guarantees_amt']; ?>">%
                    <p class="notic">借款保证金 = 借款金额 × 借款保证金比率【放款时冻结，如无逾期记录，还款完成时返还至用户账户】</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>担保金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantor_amt" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['guarantor_amt']; ?>">
                    <p class="notic">担保方，担保金额(代偿金额累计不能大于担保金额)</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>担保收益</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="guarantor_pro_fit_amt" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['guarantor_pro_fit_amt']; ?>">%
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_fee" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['manage_fee']; ?>">%
                    <p class="notic">管理费 = 本金总额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投资者管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_loan_manage_fee" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['user_loan_manage_fee']; ?>">%
                    <p class="notic">管理费 = 投资总额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>普通逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day1" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['manage_impose_fee_day1']; ?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>严重逾期管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="manage_impose_fee_day2" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['manage_impose_fee_day2']; ?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>普通逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day1" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['impose_fee_day1']; ?>">%
                    <p class="notic">罚息总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>严重逾期罚息</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="impose_fee_day2" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['impose_fee_day2']; ?>">%
                    <p class="notic">逾期管理费总额 = 逾期本息总额 × 对应逾期管理费率 × 逾期天数 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最小额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="minimum" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['minimum']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最大额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="maximum" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['maximum']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>债权转让管理费</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_load_transfer_fee" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['user_load_transfer_fee']; ?>">%
                    <p class="notic">管理费 = 转让金额 × 管理费率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>提前还款补偿</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="compensate_fee" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['compensate_fee']; ?>">%
                    <p class="notic">补偿金额 = 剩余本金 × 补偿年化利率 0即不收取</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投资人返利</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="user_bid_rebate" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['user_bid_rebate']; ?>">%
                    <p class="notic">返利金额 = 投标金额 × 返利百分比【需满标】</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最低投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="min_loan_money" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['min_loan_money']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最高投标金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="max_loan_money" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['max_loan_money']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>申请限制金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="limit_loan_money" placeholder="100" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['limit_loan_money']; ?>">
                    <p class="notic">限制申请金额是否满足此数的整数倍，使用全局配置请填0</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>投标限制金额</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="limit_bid_money" placeholder="100" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['limit_bid_money']; ?>">
                    <p class="notic">限制投标金额是否满足此数的整数倍（此限制将会覆盖最低投标金额），用全局配置请填0</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>借款限制时间</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="loan_limit_time" placeholder="5" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['loan_limit_time']; ?>">天
                    <p class="notic">如有未通过审核的借款，则在此限制之前将不能再提交新的申请</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>申请延期的额度</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="generation_position" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['generation_position']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>用户投标类型</label>
                </dt>
                <dd class="opt">
                    按金额<input type="radio" name="uloadtype" value="0" <?php echo $dealLoanTypeExtern['uloadtype'] == 0 ? 'checked' : ''; ?>>
                    按份数<input type="radio" name="uloadtype" value="1" <?php echo $dealLoanTypeExtern['uloadtype'] == 1 ? 'checked' : ''; ?>>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>分成多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="portion" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['portion']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>最多买多少份</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="max_portion" style="width: 80px;" value="<?php echo $dealLoanTypeExtern['max_portion']; ?>">
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
                    <textarea name="seo_title" cols="80" style="height: 100px;"><?php echo $dealLoanTypeExtern['seo_title']; ?></textarea>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>贷款SEO自定义关键词</label>
                </dt>
                <dd class="opt">
                    <textarea name="seo_keyword" cols="80" style="height: 100px;"><?php echo $dealLoanTypeExtern['seo_keyword']; ?></textarea>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>贷款SEO自定义描述</label>
                </dt>
                <dd class="opt">
                    <textarea name="seo_description" cols="80" style="height: 100px;"><?php echo $dealLoanTypeExtern['seo_description']; ?></textarea>
                </dd>
            </dl>
        </div>
        <!--SEO设置end-->

        <!--信用等级start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>信用等级：</label>
                </dt>
                <dd class="opt">
                    <div id="flexitable" class="flexitable">
                        <table class="flexigrid">
                            <thead>
                            <tr>
                                <th width="24" style="width: 24px;" align="center" class="sign"><i class="ico-check"></i></th>
                                <th width="150" style="width: 150px;" align="center">编号</th>
                                <th width="150" style="width: 150px;" align="center">类型ID</th>
                                <th width="150" style="width: 150px;" align="center">等级名称</th>
                                <th width="150" style="width: 150px;" align="center">所需信用积分</th>
                                <th width="150" style="width: 150px;" align="center">服务费用</th>
                                <th width="300" style="width: 300px;" align="center">借款期限</th>
                                <th width="150" style="width: 150px;" align="center">筹款期限</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($userLevelList as $userLevel){ ?>
                                <tr>
                                    <td width="24" style="width: 24px;" align="center" class="sign"><i class="ico-check"></i></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['id']; ?></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['loan_type_id']; ?></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['name']; ?></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['point']; ?></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['services_fee']; ?></td>
                                    <td width="300" style="width: 300px;"><?php echo $userLevel['repaytime']; ?></td>
                                    <td width="150" style="width: 150px;" align="center"><?php echo $userLevel['enddate']; ?></td>
                                    <td><a href="<?php echo adminUrl('sys_loan','loan_type_user_level_edit',array('id'=>$userLevel['id']));?>">编辑</a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </dd>
            </dl>
        </div>
        <!--信用等级end-->

        <!--信用等级start-->
        <div class="tab-content" style="display: none;">
            <dl class="row">
                <dt class="tit">
                    <label>同盾规则限制</label>
                </dt>
                <dd class="opt">
                    风险分数≥<input type="text" name="tongdun_limit_score" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_limit_score']; ?>">同盾规则大于此设定将被自动拒绝<br/>
                    3个月内身份证关联多个申请信息≥<input type="text" name="tongdun_three_month_idno_relevance" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_three_month_idno_relevance']; ?>"><br/>
                    7天内申请人在多个平台申请借款≥<input type="text" name="tongdun_seven_day_apply_num" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_seven_day_apply_num']; ?>"><br/>
                    1个月内申请人在多个平台申请借款≥<input type="text" name="tongdun_one_month_apply_num" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_one_month_apply_num']; ?>"><br/>
                    3个月内申请人在多个平台申请借款≥<input type="text" name="tongdun_three_month_apply_num" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_three_month_apply_num']; ?>">
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>年龄限制</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="tongdun_limit_minage" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_limit_minage']; ?>">-
                    <input type="text" name="tongdun_limit_maxage" style="width: 80px;" value="<?php echo $dealLoanType['tongdun_limit_maxage']; ?>">
                    <p class="notic">年龄超出此设定的将被自动拒绝</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>学信网限制</label>
                </dt>
                <dd class="opt">
                    <?php $chkStatus = explode(',', $dealLoanType['xuex_chk_status']); ?>
                    <input type="checkbox" name="xuex_chk_status[]" value="0" <?php echo in_array(0,$chkStatus) ? 'checked' : '' ?>>未验证
                    <input type="checkbox" name="xuex_chk_status[]" value="1" <?php echo in_array(1,$chkStatus) ? 'checked' : '' ?>>正确
                    <input type="checkbox" name="xuex_chk_status[]" value="2" <?php echo in_array(2,$chkStatus) ? 'checked' : '' ?>>错误
                    <p class="notic">学信网状态未勾选的将被自动拒绝</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>芝麻信用限制</label>
                </dt>
                <dd class="opt">
                    芝麻信用分<<input type="text" name="zm_point_limit" style="width: 80px;" value="<?php echo $dealLoanType['zm_point_limit']; ?>">
                    <p class="notic">芝麻信用分低于此设定的将被自动拒绝</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>高风险城市</label>
                </dt>
                <dd class="opt">
                    <div id="province_city_div">

                    </div>
                    <input type="button" value="添加" class="input-btn" onclick="province_city_add();">
                    <p class="notic">身份证、同盾申请IP任一在此城市的将被自动拒绝</p>
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
    KindEditor.ready(function(K) {
        window.editor = K.create('#content');
    });

    var provinceCity = <?php echo $provinceCity; ?>;
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
            '第'+maxIndex+'联系人&nbsp;'+
        '<input type="text" name="contact_arr['+maxIndex+']" placeholder="">&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<input type="checkbox" name="company_arr['+maxIndex+']" value="1">工作单位&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<input type="checkbox" name="contact_norequired_arr['+maxIndex+']" value="1">选填&nbsp;&nbsp;&nbsp;&nbsp;'+
        '<input type="button" class="input-btn" value="删除" onclick="contact_del(this);">'+
        '</div>';
        $('#contact_div').append(html);
    });

    function contact_del(obj){
        $(obj).parent().remove();
    }

    function loadCity(obj){
        var province = $(obj).find('option:selected');
        var provinceId = province.data('province_id');
        var cityList = null;
        for(var i=0;i<provinceCity.length;i++){
            if(provinceCity[i].id == provinceId){
                cityList = provinceCity[i].city_list;
                break;
            }
        }

        var citySelectObj = $(obj).next();
        citySelectObj.empty();

        var html = '<option value="全部">全部</option>';
        for(var i=0;i<cityList.length;i++){
            html+='<option value="'+cityList[i].name+'">'+cityList[i].name+'</option>';
        }
        citySelectObj.append(html);
    }

    function province_city_add(){
        var maxIndex = 0;
        $.each($('.province_city_wrap'),function(i,obj){
            var currentIndex = parseInt($(obj).data('index'));
            if(currentIndex > maxIndex){
                maxIndex = currentIndex;
            }
        });

        maxIndex +=1;
        var html = '<div class="province_city_wrap" data-index="'+maxIndex+'">'+
            '<select name="tongdun_limit_province['+maxIndex+']" onchange="loadCity(this);">'+
        '<option value="">请选择省份</option>'+
            <?php foreach($provinceList as $province){ ?>
        '<option value="<?php echo $province['name'] ?>" data-province_id="<?php echo $province['id'] ?>"><?php echo $province['name'] ?></option>'+
            <?php } ?>
        '</select>'+
        '<select name="tongdun_limit_city['+maxIndex+']">'+
        '<option value="">请选择城市</option>'+
        '</select>'+
        '<input type="button" value="删除" class="input-btn" onclick="province_city_del(this);">'+
        '</div>';
        $('#province_city_div').append(html);

    }

    function province_city_del(obj){
        $(obj).parent().remove();
    }

    $(function(){
        //初始化表单验证
        $("#form1").initValidform();

        $('#daterange').dateRangePicker({
            shortcuts:
                {
                    'next-days':[365,1095,1825]
                },
            startDate:'<?php echo date('Y-m-d',time());?>',
            endDate:false,
            getValue: function()
            {
                if ($('#start_time').val() && $('#end_time').val() )
                    return $('#start_time').val() + ' to ' + $('#end_time').val();
                else
                    return '';
            },
            setValue: function(s,s1,s2)
            {
                $('#start_time').val(s1);
                $('#end_time').val(s2);
            }
        });

        //加载高风险城市
        var tongdun_limit_city = '<?php echo $dealLoanType['tongdun_limit_city'] ?>';
        var tongdun_limit_province = '<?php echo $dealLoanType['tongdun_limit_province'] ?>';
        if(tongdun_limit_province != '' && tongdun_limit_city != ''){
            var provinceArr = tongdun_limit_province.split(',');
            var cityArr = tongdun_limit_city.split(',');
            for(var i=0;i<provinceArr.length;i++){
                province_city_add()
                var citySelect = $('#province_city_div select').last();
                var provinceSelect = $(citySelect).prev();
                provinceSelect.find("option[value='"+provinceArr[i]+"']").attr("selected",true);
                loadCity(provinceSelect.get(0));
                $(citySelect).find("option[value='"+cityArr[i]+"']").attr("selected",true);
            }
        }else{
            province_city_add();
        }
    });
</script>
</body>
</html>