<?php defined("IN_XIAOSHU") or exit("Access Invalid!"); ?>
	
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo \Base::getConfig()->getLanguageCharset()?>" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<title><?php echo \Core::L("admin_center");?></title>
<link href="<?php echo RS_PATH?>artdialog/ui-dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo RS_PATH?>admin/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery-1.11.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>jquery/jquery.nicescroll.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>artdialog/dialog-plus-min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/layindex.js?date=20174281530"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo RS_PATH?>admin/js/common.js"></script>
</head>
<body class="indexbody">
  <a class="btn-paograms" onclick="togglePopMenu();"></a>
  <div id="pop-menu" class="pop-menu">
    <div class="pop-box">
      <h1 class="title"><i></i><?php echo \Core::L("navigation_menu");?></h1>
      <i class="close" onclick="togglePopMenu();"><?php echo \Core::L("close");?></i>
      <div class="list-box"></div>
    </div>
    <i class="arrow"><?php echo \Core::L("arrow");?></i>
  </div>
  
  <div class="main-top">
    <a class="icon-menu"></a>
    <div id="main-nav" class="main-nav"></div>
    <div class="nav-right">
      <div class="info">
        <i></i>
        <span>
          <?php echo \Core::L("welcome");?>，<?php echo $admininfo['name'];?><br>
          <?php echo $admininfo['gname'];?>
        </span>
      </div>
      <div class="option">
        <i></i>
        <div class="drop-wrap">
          <div class="arrow"></div>
          <ul class="item">
            <li>
              <a href="/" target="_blank"><?php echo \Core::L("sys_index");?></a>
            </li>
            <li>
              <a href="#" onclick="linkMenuTree(false, '');" target="mainframe"><?php echo \Core::L("modify_pwd");?></a>
            </li>
            <li>
              <a id="lbtnExit" href="<?php echo \Core::getUrl('login','logout',\Core::config()->getAdminModule())?>"><?php echo \Core::L("admin_logout");?></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

    <div class="main-left">
    <h1 class="logo"></h1>
    <div id="sidebar-nav" class="sidebar-nav"></div>
  </div>
  
  <div class="main-container">
    <iframe id="mainframe" name="mainframe" frameborder="0" src="<?php echo \Core::getUrl('dashboard','',\Core::config()->getAdminModule())?>"></iframe>
  </div>
</body>
</html>