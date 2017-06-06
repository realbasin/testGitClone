<?php
defined('IN_XIAOSHU') or exit('Access Invalid!');

$lang['site_name']='系统名称';
$lang['site_name_notice']='当前系统的名称';
$lang['site_icp']='ICP证书号';
$lang['site_icp_notice']='页面底部显示的 ICP 备案信息，内部系统无需备案 <a href="http://www.miitbeian.gov.cn" target="_blank">备案查询</a>';
$lang['statistics_code']='流量统计代码';
$lang['statistics_code_notice']='第三方流量统计代码，用于统计页面访问数据。推荐使用 <a href="http://www.umeng.com/" target="_blank">友盟统计</a>';
$lang['time_zone']='时区设置';
$lang['time_zone_notice']='系统使用的时区，中国默认为+8';
$lang['sys_log']='系统日志';
$lang['sys_log_notice']='启用系统日志，将会记录全部管理操作内容';
$lang['maintain_mode']='维护模式';
$lang['maintain_mode_notice']='维护模式开启后，系统将无法访问';
$lang['maintain_mode_tip']='提示内容';
$lang['maintain_mode_tip_notice']='维护模式下，用户看到的提示信息，支持HTML内容';
$lang['maintain_mode_white']='白名单';
$lang['maintain_mode_white_notice']='维护模式下，可以正常访问系统的IP，每个IP或IP段以英文的","号隔开。例：192.168.1.200,192.168.2.1/200';

$lang['base_setting_help']='<li>系统基本参数设置';

$lang['upload_driver']='启用功能';
$lang['upload_driver_local']='启用本地上传功能，启用本地上传功能后其它上传功能自动停止使用';
$lang['upload_driver_qiniu']='启用七牛上传功能，<a href=\'http://www.qiniu.com/\' target="_blank">申请账号</a>，启用七牛上传功能后其它上传功能自动停止使用';
$lang['upload_driver_oss']='启用阿里OSS上传功能，<a href=\'http://www.aliyun.com/\' target="_blank">申请账号</a>，启用阿里OSS上传功能后，其它上传功能自动停止使用';
$lang['upload_driver_upyun']='启用又拍云UPYUN上传功能，<a href=\'http://www.upyun.com/\' target="_blank">申请账号</a>，启用又拍云UPYUN上传功能后，其它上传功能自动停止使用';
$lang['upload_size']='上传文件大小';
$lang['upload_size_notice']='单位(k)，1024=1M，该设置不能覆盖服务器上传设置，如需上传大文件，请调整服务器最大上传大小';
$lang['upload_size_error']='请输入正确的上传大小';
$lang['upload_ext']='扩展名';
$lang['upload_ext_notice']='上传文件的扩展名，多个上传文件类型以英文的","号分开，后缀名不要带"."符号';
$lang['upload_ext_error']='请输入正确的文件扩展名';
$lang['upload_qiniu_bucket']='存储空间名称(Bucket)';
$lang['upload_qiniu_bucket_notice']='存储空间名称是千牛作为唯一的 Bucket 识别符';
$lang['upload_qiniu_accesskey']='访问码(AccessKey)';
$lang['upload_qiniu_accesskey_notice']='七牛个人中心获取密钥信息';
$lang['upload_qiniu_secretkey']='安全码(SecretKey)';
$lang['upload_qiniu_secretkey_notice']='七牛个人中心获取密钥信息';
$lang['upload_oss_bucket']='存储空间名称(Bucket)';
$lang['upload_oss_bucket_notice']='阿里云OSS的存储空间名称';
$lang['upload_oss_accesskey']='访问码(AccessKey)';
$lang['upload_oss_accesskey_notice']='阿里云OSS中心获取密钥信息';
$lang['upload_oss_secretkey']='安全码(SecretKey)';
$lang['upload_oss_secretkey_notice']='阿里云OSS中心获取密钥信息';
$lang['upload_oss_endpoint']='线路地址(EndPoint)';
$lang['upload_oss_endpoint_notice']='阿里云OSS的区域访问地址';
$lang['upload_upyun_bucket']='存储空间名称(Bucket)';
$lang['upload_upyun_bucket_notice']='又拍云的存储空间名称';
$lang['upload_upyun_username']='用户名称';
$lang['upload_upyun_username_notice']='又拍云的用户名称';
$lang['upload_upyun_password']='用户密码';
$lang['upload_upyun_password_notice']='又拍云的用户密码';
$lang['upload_upyun_endpoint']='线路地址(EndPoint)';
$lang['upload_upyun_endpoint_notice']='又拍云的区域访问地址';

$lang['upload_setting_help']='<li>如果上传文件大小超过服务器上传配置，则需要调整服务器最大上传文件大小<li>上传文件扩展名以英文逗号隔开<li>只能启用一种上传类型';
$lang['upload_setting_qiniu_help']='<li>如果没有七牛账号，点这里<a href=\'http://www.qiniu.com/\' target=\'_blank\'>申请账号</a>';
$lang['upload_setting_oss_help']='<li>如果要申请阿里云OSS，点这里<a href=\'http://www.aliyun.com/\' target=\'_blank\'>申请账号</a>';
$lang['upload_setting_upyun_help']='<li>如果要申请又拍云，点这里<a href=\'http://www.upyun.com/\' target=\'_blank\'>申请账号</a>';

$lang['upload_watermark_img']='启用图片水印';
$lang['upload_watermark_img_notice']='启用图片水印后，用户在上传图片到本地后可以使用自定义图片水印功能';
$lang['upload_watermark_text']='启用文字水印';
$lang['upload_watermark_text_notice']='启用文字水印后，用户在上传图片到本地后可以使用自定义文字水印功能';

$lang['watermark_setting_help']='<li>启用文字水印或图片水印后，用户上传图片到本地时可以使用图片水印功能<li>水印功能只有在 <font style=\'color:red\'>启用本地上传功能</font> 才生效';

$lang['sys_font']='系统';
$lang['upload_watermark_text_font_name']='字体名称';
$lang['upload_watermark_text_font_name_notice']='上传字体的名称';
$lang['upload_watermark_text_font_name_notice_null']='请填写字体名称';
$lang['upload_watermark_text_font_all']='当前水印字体';
$lang['upload_watermark_text_font']='文字水印字体';
$lang['upload_watermark_text_font_notice']='用户在使用文字使用功能时可以选择的字体，请上传.ttf字体文件';
$lang['upload_watermark_text_font_error']='请上传正确的.ttf字体文件';

$lang['delete_watermark_confirm']='确定要删除字体文件吗？';
$lang['delete_watermark_font_error_sys']='不能删除系统自带的水印字体文件';
$lang['delete_watermark_font_error_noexsits']='找不到对应字体文件';
$lang['delete_watermark_font_error']='删除水印文件失败';
$lang['delete_watermark_font_sucess']='删除水印文件成功';

$lang['upload_watermark_font_data_error']='上传文件数据错误';
$lang['upload_watermark_font_error']='上传失败';
$lang['upload_watermark_font_save_error']='保存失败';
$lang['upload_watermark_font_success']='上传成功';

$lang['upload_watermark_text_font_help']='<li>用户在使用文字水印功能时可以使用的字体';

$lang['smtp_server']='SMTP 服务器';
$lang['smtp_server_notice']='邮件服务器的地址，比如：smtp.163.com';
$lang['smtp_port']='SMTP 端口';
$lang['smtp_port_notice']='邮件服务器端口，一般是25';
$lang['smtp_protocol']='使用安全协议';
$lang['smtp_protocol_notice']='如果邮件服务器必须使用SSL/TLS安全协议，请勾选该选项，使用该协议请确认系统服务器是否安装了openssl扩展';
$lang['smtp_send']='发信邮件地址';
$lang['smtp_send_notice']='比如username@163.com';
$lang['smtp_user']='SMTP 用户名';
$lang['smtp_user_notice']='一般与邮箱用户名相同';
$lang['smtp_password']='SMTP 密码';
$lang['smtp_password_notice']='一般与邮箱密码相同';
$lang['smtp_test']='测试邮件地址';
$lang['smtp_test_notice']='填写要测试的邮件地址，并点击发送';

$lang['smtp_test_subject']='这是一封来自[%s]的测试邮件';
$lang['smtp_test_message']='这是一封来自[%s]的测试邮件，如果您收到该邮件，说明SMTP服务器已经配置成功';

$lang['smtp_server_null']='请填写SMTP服务器地址';
$lang['smtp_port_null']='请填写SMTP端口';
$lang['smtp_port_error']='请填写正确的SMTP端口';
$lang['smtp_send_null']='请填写发信邮件地址';
$lang['smtp_send_error']='请填写正确的发信邮件地址';
$lang['smtp_user_null']='请填写SMTP用户名';
$lang['smtp_password_null']='请填写SMTP密码';

$lang['smtp_test_success_process']='正在发送测试邮件...';
$lang['smtp_test_success']='恭喜，测试邮件发送成功！';
$lang['smtp_test_error_ajax']='测试邮件发送失败，可能是网络错误';
$lang['smtp_test_error']='测试邮件发送失败，SMTP服务器配置错误';

$lang['smtp_help']='<li>填写邮件服务器配置后点击“测试”按钮测试配置<li>具体邮件服务器配置请阅读相关文档';

$lang['sms_type']='短信服务商';
$lang['sms_type_notice']='短信网关<a target="_blank" href="http://www.movek.net/">申请</a>，如果需要指定的短信服务商，请联系我们进行集成';
$lang['sms_user_id']='企业ID或用户ID';
$lang['sms_user_id_notice']='短信服务商提供的企业ID或用户ID';
$lang['sms_acount']='用户账户';
$lang['sms_acount_notice']='短信服务商提供的用户账户';
$lang['sms_password']='用户密码';
$lang['sms_password_notice']='短信服务商提供的用户密码';
$lang['sms_sign']='短信签名';
$lang['sms_sign_notice']='短信内容所要求的短信签名，如【公司名】';
$lang['sms_sign_location']='签名位置';
$lang['sms_sign_location_start']='内容头部';
$lang['sms_sign_location_end']='内容尾部';
$lang['sms_sign_location_notice']='短信签名的位置，默认在内容头部，勾选则在内容尾部';
$lang['sms_test']='短信测试';
$lang['sms_test_notice']='请填写一个或者多个手机号码，多个号码用英文的","号隔开';
$lang['sms_test_process']='正在发送短信...';

$lang['sms_type_null']='请选择短信服务商';
$lang['sms_user_id_null']='请填写企业ID或用户ID';
$lang['sms_acount_null']='请填写用户账户';
$lang['sms_password_null']='请填写用户密码';
$lang['sms_sign_null']='请填写短信签名';

$lang['sms_test_text']='这是一条测试短信，当您收到这条短信，表示短信配置成功';

$lang['sms_test_fail']='测试短信发送失败，请查看具体错误原因';
$lang['sms_test_error']='测试短信发送错误';
$lang['sms_test_success']='测试短信发送成功';

$lang['sms_help']='<li>设置短信服务商提供的参数，然后进行测试<li>如果需要接入指定的短信服务商，请联系公司进行集成';

$lang['login_qq']='使用QQ登录';
$lang['login_qq_notice']='<a href=\'https://connect.qq.com/\' target=\'_blank\'>申请使用</a>';
$lang['login_sina']='使用新浪微博登录';
$lang['login_sina_notice']='<a href=\'http://open.weibo.com/\' target=\'_blank\'>申请使用</a>';
$lang['login_wechat']='使用微信登录';
$lang['login_wechat_notice']='<a href=\'https://open.weixin.qq.com/\' target=\'_blank\'>申请使用</a>';
$lang['login_sms']='使用短信登录';
$lang['login_sms_notice']='请先设置好短信网关再开启短信登录功能';
$lang['appid']='AppID';
$lang['appid_notice']='服务方提供的AppID';
$lang['appkey']='AppKey';
$lang['appkey_notice']='服务方提供的AppKey';
$lang['metacode']='验证META信息';
$lang['metacode_notice']='服务方要求放入页面的META信息';

$lang['login_qq_help']='<li>请先设置好相关参数再开启QQ登录';
$lang['login_sina_help']='<li>请先设置好相关参数再开启新浪微博登录';
$lang['login_wechat_help']='<li>请先设置好相关参数再开启微信登录';
$lang['login_sms_help']='<li>请先设置好短信网关再开启短信登录';

$lang['auth_list']='权限组列表';
$lang['auth_add']='新增权限组';
$lang['auth_name']='权限组名称名称';
$lang['auth_name_notice']='权限组名称';
$lang['auth_choose']='选择权限';
$lang['auth_info']='权限组说明';
$lang['auth_info_notice']='权限组的说明文字';
$lang['auth_name_null']='请填写权限组名称';
$lang['auth_name_repeat']='系统存在同样的权限组名称';
$lang['auth_info_null']='请填写权限组说明';
$lang['auth_permission_null']='您还没有选择任何权限';

$lang['auth_using']='权限组正在被使用';

$lang['auth_list_help']='<li>可以添加、修改、或者删除权限组';

$lang['admin']='管理员';
$lang['admin_list']='管理员列表';
$lang['admin_add']='新增管理员';
$lang['admin_name']='管理员名称';
$lang['admin_name_notice']='管理员登录所使用的名称';
$lang['admin_password']='管理员密码';
$lang['admin_password_notice']='管理员登录所使用的密码';
$lang['admin_password_confirm']='确认密码';
$lang['admin_password_confirm_notice']='请确认两次输入的密码是相同的';
$lang['admin_password_edit_notice']='如果不需要修改管理员密码请留空';
$lang['admin_login_time']='上次登录时间';
$lang['admin_login_num']='登录次数';
$lang['admin_gname']='权限组';
$lang['admin_gname_notice']='如果没有创建权限组，请先创建权限组';
$lang['admin_auth_info']='权限组说明';

$lang['admin_name_null']='请输入管理员名称';
$lang['admin_name_error']='管理员名称由5-20位字母、数字、或者下划线组成';
$lang['admin_password_null']='请输入管理员密码';
$lang['admin_password_error']='管理员密码由6-20位字母、数字、或者下划线组成';
$lang['admin_password_confirm_null']='请再次输入管理员密码';
$lang['admin_password_confirm_error']='两次输入的密码不一致';
$lang['admin_gname_null']='请选择权限组，如果没有权限组请先创建';

$lang['admin_name_repeat']='系统存在同样的管理员名称';

$lang['admin_list_help']='<li>可以添加、修改、或者删除管理员';
$lang['admin_add_help']='<li>添加一个管理员并赋予管理员响应权限<li>如果没有创建管理员权限组，请先创建权限组';
$lang['admin_edit_help']='<li>如果不需要修改密码请留空';

$lang['admin_log']='管理员日志';
$lang['admin_log_content']='操作内容';
$lang['admin_log_time']='时间';
$lang['admin_log_admin_name']='管理员';
$lang['admin_log_admin_ip']='管理员IP';
$lang['admin_log_type']='操作类型';
$lang['admin_log_control']='控制类';
$lang['admin_log_method']='方法';

$lang['admin_log_help']='<li>可以查看相关管理员操作日志<li>日志一旦删除无法恢复<li>建议保留半年或者一年的日志<li>如果不需要记录日志，请在基础设置内取消';

$lang['variable_list']='自定义变量列表';
$lang['variable_name']='变量名称';
$lang['variable_name_notice']='变量名称由字母开头，只能包含字母、数字和下划线';
$lang['variable_value']='变量值';
$lang['variable_value_notice']='变量值由任意字符组成，可以使用中文';
$lang['variable_info']='描述';
$lang['variable_info_notice']='变量的描述';
$lang['variable_add']='添加变量';
$lang['variable_edit']='编辑变量';

$lang['variable_name_err']='变量名称由字母开头，只能包含字母、数字和下划线。不能超过50个字符';

$lang['variable_help']='<li>可以定义变量，并在任意php页面内引用<li>引用变量使用方法:C(\'变量名称\')';

$lang['cache_list']='缓存列表';
$lang['cache_name']='缓存名称';
$lang['cache_des']='缓存描述';

$lang['cache_help']='<li>清理缓存后系统将自动进行缓存重建';

$lang['backup_type']='备份方式';
$lang['backup_name']='文件名称';
$lang['backup_list']='备份列表';
$lang['backup_name_notice']='数据表备份的文件名称';
$lang['backup_all']='备份全部';
$lang['backup_complete']='备份完成';
$lang['backup_tables']='备份指定表';
$lang['backup_volume_size']='分卷大小';
$lang['backup_volume_size_notice']='备份文件将按照分卷大小分割成多个文件进行保存';
$lang['backup_doing']='正在备份数据[分卷%u]，请稍候...';

$lang['backup_name_error']='文件名称只能包含数字和字母，最大长度100个字符';
$lang['backup_volume_size_error']='分卷大小在20-9999(kb)之间';
$lang['backup_mkdir_fail']='创建备份文件夹失败';
$lang['backup_mkfile_fail']='创建备份文件失败';
$lang['backup_name_exsits']='系统存在相同的备份名称';
$lang['backup_tables_null']='没有设置需要备份的表';

$lang['backup_help']='<li>勾选要备份的表进行数据备份<li>输入备份分卷大小，系统将自动备份成多个文件<li>建议每周备份一次数据';

$lang['restore_name']='备份名称';
$lang['restore_vol_num']='分卷数';
$lang['restore_time']='备份时间';
$lang['restore_size']='文件大小';
$lang['restore_data']='还原';
$lang['restore_data_tip']='请谨慎使用还原功能，一旦数据清除则无法恢复';
$lang['restore_ready']='正在准备恢复数据，请稍候...';
$lang['restore_doing']='正在恢复数据[分卷%u]，请稍候...';
$lang['restore_complete']='恢复数据库成功';
$lang['restore_dir_no_exsits']='找不到还原文件夹';
$lang['restore_file_no_exsits']='找不到还原文件';

$lang['restore_help']='<li>恢复数据库会先清除现有表的数据<li>请尽量不要赋予非系统管理员恢复权限';
$lang['loan_type_setting']='贷款类型设置';