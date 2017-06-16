<?php
namespace XSQueue;

use XSQueue\AmqpExt\AmqpConnectionFactory;
use XSQueue\Redis\RedisConnectionFactory;


spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'XSQueue\\')) {
        $filename = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require_once($filename);
    }
});

/**
 * 
 * Type:AMQP_EX_TYPE_DIRECT,AMQP_EX_TYPE_FANOUT,AMQP_EX_TYPE_HEADERS, AMQP_EX_TYPE_TOPIC
 * Flag:默认AMQP_NOPARAM,可选AMQP_DURABLE,AMQP_PASSIVE,AMQP_AUTODELETE
 */
class QueueManager{
	
	protected $config;
	
	public function __construct()
	{
		//第2个参数设置为true，表示缓存该config
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
		$dirName=$classType;
		if($dirName=='Amqp'){
			$dirName='AmqpExt';
		}
		$className="XSQueue\\{$dirName}\\{$classType}ConnectionFactory";
		return new $className($classConfig);
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
	
	protected function getConfig($name)
	{
		$config=\Core::arrayGet($this->config,$name);
		if($config){
			return $config;
		}else{
			throw new \Xs_Exception_500("Unknown lock config type {$name} !");
		}
	}
}
?>