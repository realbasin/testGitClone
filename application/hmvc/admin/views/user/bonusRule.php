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
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/language_<?php echo strtolower(\Base::getConfig()->getLanguageTypeDirName());?>.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/perfect-scrollbar.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>moment.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.daterangepicker.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
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
    <span>优惠券管理</span>
    <i class="arrow"></i>
    <span><?php echo '编号:'.$type_id.' - '.$bonusTypeName;?></span>
    <i class="arrow"></i>
    <span>优惠券列表</span>
</div>
<div class="line10"></div>
<div class="page">
    <div class="flexitable" id="flexitable">
        <table class="flexigrid">
            <thead>
            <tr>
                <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
                <th width="50" align="center" class="handle"><?php echo \Core::L('operate');?></th>
                <th width="100" align="left">优惠券面额</th>
                <th width="100" align="left">最低使用金额</th>
                <th width="100" align="left">每次派送数量</th>
                <th width="150" align="left">适用月份</th>
                <th width="100" align="left">债权标可用</th>
                <th width="50" align="left">是否启用</th>
                <th width="50" align="left">是否删除</th>
                <th width="140" align="left">创建时间</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($datalist) && is_array($datalist)){ ?>
                <?php foreach($datalist as $k => $v){ ?>
                    <tr class="hover">
                        <td class="sign"><i class="ico-check"></i></td>
                        <td class="handle">
                            <?php if (!$v['is_delete']) {?>
                            <a class="btn red" href="javascript:flexDelete(<?php echo $v['id'];?>)" ><i class="fa fa-trash-o"></i> <?php echo \Core::L("delete");?></a>
                            <a class="btn blue" href="javascript:flexEdit(<?php echo $v['id'];?>)"><i class="fa fa-pencil-square-o"></i> <?php echo \Core::L('edit');?></a>
                            <?php } ?>
                        </td>
                        <td><?php echo $v['money'];?></td>
                        <td><?php echo $v['limit_amount'];?></td>
                        <td><?php echo $v['num'];?></td>
                        <td><?php echo $v['use_deal_month'];?></td>
                        <td><?php echo $v['use_deal_load'] ? '是' : '否';?></td>
                        <td><?php echo $v['is_effect'] ? '是' : '否';?></td>
                        <td><?php echo $v['is_delete'] ? '已删除' : '否';?></td>
                        <td><?php echo toDate($v['create_time']);?></td>
                        <td></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $('.flexigrid').flexigrid({
        usepager: false,
        reload: false,
        columnControl: false,
        title: '优惠券列表',
        buttons : [
            {display: '<i class="fa fa-plus"></i> <?php echo \Core::L("add")?>', name : 'add', bclass : 'add', title : '<?php echo \Core::L("add");?>', onpress : flexAdd }
        ]
    });

    function flexAdd() {
        var d=parent.dialog({
            title: '添加优惠券',
            url: '<?php echo adminUrl('user_bonus','bonus_add');?>'+'&type_id=<?php echo $type_id;?>'
        });
        d.width(700);
        d.addEventListener('close',function(){
            window.location.href = '<?php echo adminUrl('user_bonus','type_bonus');?>'+'&type_id=<?php echo $type_id;?>';
        });
        d.show();

    }
    function flexDelete(id){
        var ids = new Array();
        ids.push(id);
        parent.dialog({
            title: lang['tip'],
            content: lang['delete_confirm'],
            okValue: lang['ok'],
            ok: function () {
                ids = ids.join(',');
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "<?php echo adminUrl('user_bonus','bonus_delete');?>",
                    data: "bonus_type_name=<?php echo $bonusTypeName;?>&id="+ids,
                    success: function(data){
                        if (data.code==200){
                            window.location.href = '<?php echo adminUrl('user_bonus','type_bonus');?>'+'&type_id=<?php echo $type_id;?>';
                        } else {
                            jsprint(data.message);
                        }

                    }
                });
            },
            cancelValue: lang['cancel'],
            cancel: function () { }
        }).showModal();
    }

    function flexEdit(id){
        var d=parent.dialog({
            title: '修改优惠券',
            url: '<?php echo adminUrl('user_bonus','bonus_edit');?>'+'&rule_id='+id
        });
        d.width(700);
        d.addEventListener('close',function(){
            window.location.href = '<?php echo adminUrl('user_bonus','type_bonus');?>'+'&type_id=<?php echo $type_id;?>';
        });
        d.show();
    }
</script>
</body>
</html>
