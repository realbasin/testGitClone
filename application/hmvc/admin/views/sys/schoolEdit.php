<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <title><?php echo \Core::L('site_setting');?></title>
    <link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/style.css?v=201705041329" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>admin/css/flexigrid.css?v=201705031531" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo RS_PATH?>switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/flexigrid.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>switchery/switchery.min.js"></script>
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
    <div  class="right"><a href="javascript:void(null);" id="syshelp"   onfocus="this.blur();"><i class="help"></i><?php echo \Core::L('help');?></a></div>
    <i class="home"></i>
    <span>系统设置</span>
    <i class="arrow"></i>
    <span><?php if (!$school['id']){echo '添加';}else{echo '编辑';}?>院校</span>
</div>
<div class="line10"></div>
<div class="page">
    <form method="post" id="form1" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="id" value="<?php echo $school['id'];?>">
        <div class="form-default">
            <dl class="row">
                <dt class="tit">
                    <em>*</em>
                    <label>院校名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="name" class="input-txt" datatype="*" nullmsg="请填写院校名称！" value="<?php echo $school['name'];?>">
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <em>*</em>
                    <label>所在地</label>
                </dt>
                <dd class="opt">
                    <select name="province_id" id="province_id" onchange="load_city(this);" datatype="*" nullmsg="请选择所在地省份！">
                        <option value="" <?php if($school['province_id'] == 0) echo 'selected'; ?>>请选择</option>
                        <?php foreach($provinceList as $province){ ?>
                            <option value="<?php echo $province['id']; ?>" <?php if($school['province_id'] == $province['id']) echo 'selected'; ?>><?php echo $province['name']; ?></option>
                        <?php } ?>
                    </select>
                    <select name="region_id" id="region_id" datatype="*" nullmsg="请选择所在地城市！">
                        <option value="" <?php if($school['province_id'] == 0) echo 'selected'; ?>>请选择</option>

                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <em>*</em>
                    <label>院校等级</label>
                </dt>
                <dd class="opt">
                    <select name="grade_type" datatype="*" nullmsg="请选择院校等级！">
                        <option value="" <?php if($school['grade_type'] == 0) echo 'selected'; ?>>请选择</option>
                        <option value="1" <?php if($school['grade_type'] == 1) echo 'selected'; ?>>一本</option>
                        <option value="2" <?php if($school['grade_type'] == 2) echo 'selected'; ?>>二本</option>
                        <option value="3" <?php if($school['grade_type'] == 3) echo 'selected'; ?>>三本</option>
                        <option value="4" <?php if($school['grade_type'] == 4) echo 'selected'; ?>>大专</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <em>*</em>
                    <label>办学类型</label>
                </dt>
                <dd class="opt">
                    <select name="invest_type" datatype="*" nullmsg="请选择办学类型！">
                        <option value="" <?php if($school['invest_type'] == 0) echo 'selected'; ?>>请选择</option>
                        <option value="1" <?php if($school['invest_type'] == 1) echo 'selected'; ?>>国立</option>
                        <option value="2" <?php if($school['invest_type'] == 2) echo 'selected'; ?>>公立</option>
                        <option value="3" <?php if($school['invest_type'] == 3) echo 'selected'; ?>>私立</option>
                        <option value="4" <?php if($school['invest_type'] == 4) echo 'selected'; ?>>民办</option>
                        <option value="5" <?php if($school['invest_type'] == 5) echo 'selected'; ?>>中外合作办学</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>院校隶属</label>
                </dt>
                <dd class="opt">
                    <select name="owner_type">
                        <option value="0" <?php if($school['owner_type'] == 0) echo 'selected'; ?>>请选择</option>
                        <option value="1" <?php if($school['owner_type'] == 1) echo 'selected'; ?>>部委属</option>
                        <option value="2" <?php if($school['owner_type'] == 2) echo 'selected'; ?>>省(直辖市)属</option>
                        <option value="3" <?php if($school['owner_type'] == 3) echo 'selected'; ?>>地区级的院校</option>
                        <option value="4" <?php if($school['owner_type'] == 4) echo 'selected'; ?>>市管(省级)院校)</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>发展水平</label>
                </dt>
                <dd class="opt">
                    <select name="level_type">
                        <option value="0" <?php if($school['level_type'] == 0) echo 'selected'; ?>>请选择</option>
                        <option value="1" <?php if($school['level_type'] == 1) echo 'selected'; ?>>985工程</option>
                        <option value="2" <?php if($school['level_type'] == 2) echo 'selected'; ?>>211工程</option>
                        <option value="3" <?php if($school['level_type'] == 3) echo 'selected'; ?>>重点</option>
                        <option value="4" <?php if($school['level_type'] == 4) echo 'selected'; ?>>一般</option>
                    </select>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>学生规模</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="student_scale" class="input-txt" value="<?php echo $school['student_scale'];?>">
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>学校网址</label>
                </dt>
                <dd class="opt">
                    <input type="text" name="url" class="input-txt" value="<?php echo $school['url'];?>">
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label>学校介绍</label>
                </dt>
                <dd class="opt">
                    <textarea name="description" cols="80" style="height: 100px;"><?php echo $school['description']; ?></textarea>
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
    var provinceCityList = <?php echo $provinceCityList; ?>;
    function load_city(obj){
        var provinceId = $(obj).find("option:selected").val();
        for(var i=0;i<provinceCityList.length;i++){
            if(provinceCityList[i].id == provinceId){
                var cityList = provinceCityList[i].city_list;
                var html = '<option value="0">请选择</option>';
                for(var j=0;j<cityList.length;j++){
                    html += "<option value=\""+cityList[j].id+"\">"+cityList[j].name+"</option>";
                }

                $('#region_id').empty().append(html);
                break;
            }
        }
    }

    $(function(){
        //初始化表单验证
        $("#form1").initValidform();

        var provinceId = <?php echo $school['province_id']; ?>;
        var regionId = <?php echo $school['region_id']; ?>;
        if(provinceId !== ''){
            load_city(document.getElementById('province_id'));
        }

        if(regionId !== ''){
            $('#region_id').find("option[value='"+ regionId +"']").attr("selected",true);
        }
    });
</script>
</body>
</html>
