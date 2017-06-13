<?php
defined("IN_XIAOSHU") or exit("Access Invalid!");
/**
 * 锁配置文件
 */
return array(
	//默认redis锁
	'default_lock' => 'redis',

	'stores' => array(
	//文件锁配置
		'file' => array(
			'type' => 'FileStore',
			'config' => STORAGE_PATH.'lock/'
		),
	//memcached锁配置，不能做分布式，memcached的特性做分布式会出现异常问题
		'memcached' => array(
			'type' => 'MemcachedStore',
			'class'=>'I_Cache_Memcached',
			'config' => array(
			//memcached服务器信息
				array("127.0.0.1", 11211)
	    	)
		),
	//redis锁配置
		'redis' => array(
			'type' => 'RedisStore',
			'class'=>'I_Cache_Redis',
			'config' =>
	    		array(
				//redis服务器信息，支持集群。
				//原理是：读写的时候根据算法sprintf('%u',crc32($key))%count($nodeCount)
				//把$key分散到下面不同的master服务器上，负载均衡，而且还支持单个key的主从负载均衡。
					array(
		    			'master' => array(
						//sock,tcp;连接类型，tcp：使用host port连接，sock：本地sock文件连接
						'type' => 'tcp',
						//type是sock的时候，需要在这里指定sock文件的完整路径
						'sock' => '',
						//type是tcp的时候，需要在这里指定host，port，password，timeout，retry
						//主机地址
						'host' => '127.0.0.1',
						//端口
						'port' => 6379,
						//密码，如果没有,保持null
						'password' => NULL,
						//0意味着没有超时限制，单位秒
						'timeout' => 3000,
						//连接失败后的重试时间间隔，单位毫秒
						'retry' => 100,
						// 数据库序号，默认0, 参考 http://redis.io/commands/select
						'db' => 0,
                            'prefix'=>null,
		    			),
		    			'slaves' => array(
//							array(
//							'type' => 'tcp',
//			    			'sock' => '',
//			    			'host' => '127.0.0.1',
//			    			'port' => 6380,
//			    			'password' => NULL,
//			    			'timeout' => 3000,
//			    			'retry' => 100,
//			    			'db' => 0,
//							),
		    			)
					),
//					array(
//		    			'master' => array(
//						'type' => 'tcp',
//						'sock' => '',
//						'host' => '10.69.112.34',
//						'port' => 6379,
//						'password' => NULL,
//						'timeout' => 3000,
//						'retry' => 100,
//						'db' => 0,
//		    		),
//		    		'slaves' => array(
//		    		)
//				),
	    	)
		),
		'redis_cluster' => array(
			'type' => 'RedisClusterStore',
			'class'=>'I_Cache_Redis_Cluster',
			'config' => array(
				'hosts'=>array(//集群中所有master主机信息
		    		//'127.0.0.1:7001',
		    		//'127.0.0.1:7002',
		    		//'127.0.0.1:7003',
				),
				'timeout'=>1.5,//连接超时，单位秒
				'read_timeout'=>1.5,//读超时，单位秒
				'persistent'=>false,//是否持久化连接
                'prefix'=>null,
	    	)
		)
	),

	'prefix' => 'lock',

	// 锁超时时间（秒）
	'timeout' => 30,

	// 上锁最大超时时间（秒）
	'max_timeout' => 300,

	// 重试等待时间（微秒）
	'retry_wait_usec' => 100000
);
