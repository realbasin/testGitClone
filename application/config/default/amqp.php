<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/**
 * 队列配置文件
 */
return array(
		'log_path'=> STORAGE_PATH.'amqp',
		'host' => 'localhost',
        'port' => 5672,
        'vhost' => '/',
        'user' => 'guest',
        'pass' => 'guest',
        'debug' => true,
);
