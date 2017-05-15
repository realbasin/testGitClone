<?php

return array(

    'php_bin' => '/usr/local/bin/php', //php命令路径
    'enable' => true, //是否启用(总开关)
    'tasks' => array(
	array(
	    'class' => 'TaskTest', //task类名称不需要Task_前缀,还可以是task文件路径,比如:user/task,就是
	    'enable' => true, //是否启用task
	    'args' => '-f test -b demo --debug', //额外的传递给task的命令行参数
	    'pidfile' => '', //pid文件路径,留空会使用默认规则在storage下面生成pid文件
	    'cron' => '*/2 * * * * *', //执行周期,使用标准的crontab五位写法,另外支持六位写法,第一位是秒
	    'log' => true, //是否记录日志
	    'log_path' => '/tmp/xxx.log', //日志文件路径
	    'log_size' => 2 * 1024* 1024, //日志最大大小,单位字节
	),
    )
);