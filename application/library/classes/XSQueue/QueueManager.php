<?php
namespace XSQueue;

use XSQueue\AmqpExt\AmqpConnectionFactory;
use XSQueue\Redis\RedisConnectionFactory;


/**
 * 
 * 路由模式AMQP_EX_TYPE_DIRECT, AMQP_EX_TYPE_FANOUT, AMQP_EX_TYPE_HEADER, AMQP_EX_TYPE_TOPIC
 */
class QueueManager{
	protected $config;
	
	public function __construct()
	{
		//正式部署后，去掉第2个参数或者设置为true
		$this->config=\Core::config('queue',false);
	}
	
	public function queue($name=''){
		$name = $name ? $name : $this->getDefaultDriver();
		return $this->get($name);
	}
	
	protected function get($name)
	{
		$config = $this->getConfig($name);
		$classType=$config['type'];
		$classConfig=$config['config'];
		return new AmqpConnectionFactory($classConfig);
	}
	
	public function getDefaultDriver()
	{
		$config=\Core::arrayGet($this->config,'default_queue');
		if($config){
			return $config;
		}else{
			throw new \Xs_Exception_500("Can not find default queue config !");
		}
	}
}
?>