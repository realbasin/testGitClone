<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/**
 * 队列配置文件
 */
return array(
	//默认队列模式
	'default_queue' => 'amqp',
	'amqp' => array(
			'type' => 'Amqp',
			'config' => array(
				'host' => 'localhost',
            	'port' => 5672,
            	'vhost' => '/',
            	'user' => 'guest',
            	'pass' => 'guest',
            	'read_timeout' => null,
            	'write_timeout' => null,
            	'connect_timeout' => null,
            	'persisted' => false,
            	'lazy' => true,
            	'pre_fetch_count' => null,
            	'pre_fetch_size' => null,
            	'receive_method' => 'basic_get',
			)
		),
	'redis' => array(
			'type' => 'Redis',
			'config' => array(
				'host' => '127.0.0.1',
            	'port' => 6379,
            	'timeout' => 3000,
            	'reserved' => null,
            	'retry_interval' => 100,
            	'vendor' => 'phpredis',
            	'persisted' => false,
            	'lazy' => true,
			)
		)
);
