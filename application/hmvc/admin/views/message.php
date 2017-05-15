<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes" />
<title>消息提示</title>
<link href="<?php echo RS_PATH;?>/admin/css/style.css" rel="stylesheet" type="text/css" />
<style>
.msgBlock{
border: 1px solid #999999;text-align: center; width: 420px;min-height:150px;
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
-webkit-box-shadow: #666 0px 0px 5px;
-moz-box-shadow: #666 0px 0px 5px;
box-shadow: #666 0px 0px 5px;background: #FFFFFF;behavior: url(/PIE.htc);
position:absolute; top:50%; left:50%; margin-left:-200px;margin-top:-100px;
text-align:center;
}
.leftimg {
    float:left;margin:15px;
	width: 120px;height:120px;
}
.leftimg img {
width: 120px;height:120px;
}
.rightdiv {
width:240px;text-align:left;float:left;margin:15px;
}
.msgTxt {
padding-top:10px;
font-size:20px;
line-height:120%;
word-wrap:break-word;
}
.redictTip {
color:#999999;font-size:12px;padding-top:10px;
}
</style>
</head>
<body>
<div class="msgBlock">
<div class="leftimg tip"><img src="<?php echo RS_PATH;?>/admin/images/<?php echo $msgtype;?>.png"></div>
<div class="rightdiv">
<div class="msgTxt"><?php echo $msg;?></div>
<div class="redictTip">
	<?php if($time>0 && $showbtn){?>
	<?php echo $time;?><?php echo \Core::L("auto_redirect");?>
	<?php }?>
</div>
	<?php if($url){?>
		<?php if($showbtn){?>
		<input type="button" onclick="javascript:location.href='<?php echo $url;?>'" value="<?php echo \Core::L("go_prepage");?>" style="margin-top:10px;font-size:12px;color:#333333">
		<?php }?>
		<?php if($time>0){?>
		<script type="text/javascript"> window.setTimeout("javascript:location.href='<?php echo $url;?>'", <?php echo $time*1000;?>); </script>
		<?php }?>
		<?php }else{?>
			<?php if($showbtn){?>
			<input type="button" onclick="javascript:history.back()" value="<?php echo \Core::L("go_prepage");?>" style="margin-top:10px;font-size:12px;color:#333333">
			<?php }?>
			<?php if($time>0){?>
			<script type="text/javascript"> window.setTimeout("javascript:history.back()", <?php echo $time*1000;?>); </script>
			<?php }?>
	<?php }?>	
</div>
</div>
</div>
</body>
</html>
