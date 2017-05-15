<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

//微信基础设置
$lang['wechat_url']='Url';
$lang['wechat_url_notice']='微信开发者中心需要提供的服务器地址';
$lang['wechat_app_id']='AppId';
$lang['wechat_app_id_notice']='微信接入的App Id';
$lang['wechat_app_secret']='AppSecret';
$lang['wechat_app_secret_notice']='微信接入的密钥';
$lang['wechat_aes_key']='EncodingAESKey';
$lang['wechat_aes_key_notice']='消息加解密密钥，非明文模式需要提供，必须为英文或者数字，长度为43个字符，请保证公众号开发者中心中的EncodingAESKey与系统一致，一旦接入请不要随意更改';
$lang['wechat_token']='Token';
$lang['wechat_token_notice']='访问令牌，必须为英文或者数字，长度为3到32个字符，请保证公众号开发者中心的Token与系统一致，一旦接入请不要随意更改';

$lang['wechat_access_help']='<li>请正确填写相关参数才可以使用微信功能';

//微信支付设置
$lang['wechat_mch_id']='商户ID(mch_id)';
$lang['wechat_mch_id_notice']='微信支付商户ID';
$lang['wechat_partner_key']='商户密钥(paternerkey)';
$lang['wechat_partner_key_notice']='微信支付密钥';
$lang['wechat_ssl_cer']='微信支付cert证书';
$lang['wechat_ssl_cer_notice']='需要使用微信退款或者打款功能则需要上传证书，名称为 apiclient_cert.pem，如无需更改请不要选择文件';
$lang['wechat_ssl_key']='微信支付key证书';
$lang['wechat_ssl_key_notice']='需要使用微信退款或者打款功能则需要上传证书，名称为 apiclient_key.pem，如无需更改请不要选择文件';

$lang['wechat_ssl_upload_error']='请上传后缀名为.pem的证书文件';
$lang['wechat_ssl_cer_upload_error']='上传cert证书失败';
$lang['wechat_ssl_key_upload_error']='上传key证书失败';

$lang['wechat_payment_help']='<li>请正确填写相关参数才可以使用微信支付功能';

//微信菜单 
$lang['wechat_menu_drag']='拖动排序';
$lang['wechat_menu_add']='添加菜单';
$lang['wechat_menu_add_emotion']='添加表情';
$lang['wechat_menu_action']='菜单动作';
$lang['wechat_menu_setting']='菜单设置';
$lang['wechat_menu_title']='菜单名称';
$lang['wechat_menu_title_notice']='1、自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。<br>2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。';
$lang['wechat_menu_view']='跳转URL';
$lang['wechat_menu_media_id']='回复素材';
$lang['wechat_menu_view_limited']='跳转图文';
$lang['wechat_menu_click']='点击事件';
$lang['wechat_menu_scancode_push']='扫码';
$lang['wechat_menu_scancode_waitmsg']='扫码等待';
$lang['wechat_menu_pic_sysphoto']='系统拍照发图';
$lang['wechat_menu_pic_photo_or_album']='拍照或相册发图';
$lang['wechat_menu_pic_weixin']='微信相册发图';
$lang['wechat_menu_location_select']='地理位置';
$lang['wechat_menu_url']='链接URL';
$lang['wechat_menu_url_notice']='击此菜单时要跳转的链接，需要以http://开头';
$lang['wechat_menu_media_id']='素材ID';
$lang['wechat_menu_media_id_notice']='微信公众号的素材ID';
$lang['wechat_key']='关键词';
$lang['wechat_key_notice']='系统设定的关键词回复内容，选择某个关键词，则回复设定的该关键词的内容';

$lang['wechat_menu_delete']='删除选定目录';
$lang['wechat_menu_deleteall']='删除默认菜单并提交';
$lang['wechat_menu_deleteall_confirm']='删除默认菜单将会同时删除所有个性菜单，确定要删除吗？';

$lang['wechat_menu_help']='<li>使用左边的微信菜单编辑器进行菜单编辑<li>点选具体菜单，在右边内容区域进行内容设置<li><font color=red>点击删除整个菜单按钮，将删除默认菜单以及设置的所有个性菜单并提交生效</font><li>菜单动作3-10项，系统模式使用关键词回复模式，请先设定关键词<li>微信菜单提交后，将在24小时内生效';

//个性菜单
$lang['wechat_cond_menuid']='菜单ID';
$lang['wechat_cond_group']='用户分组';
$lang['wechat_cond_sex']='姓别';
$lang['wechat_cond_area']='地区';
$lang['wechat_cond_client']='手机操作系统';
$lang['wechat_cond_language']='语言';

$lang['wechat_cond_add']='添加个性菜单';

$lang['wechat_cond_help']='<li>个性菜单一旦创建，则无法修改<li>如果要修改某个个性菜单，需要现删除该个性菜单，再增加<li>如果删除默认菜单，个性菜单将被自动删除';