<?php
namespace AmqpLib;

use AmqpLib\Connection\AMQPStreamConnection;
use AmqpLib\Message\AMQPMessage;
use AmqpLib\Wire\AMQPTable;


spl_autoload_register(function ($class) {
    if (0 === stripos($class, 'AmqpLib\\')) {
        $filename = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        file_exists($filename) && require_once($filename);
    }
});

//以下为服务自带常量定义解释
///**
// * 传递这个参数作为标志将完全禁用其他标志,如果你想临时禁用amqp.auto_ack设置起效
// */
//define('AMQP_NOPARAM', 0);
///**
// * 持久化交换机和队列,当代理重启动后依然存在,并包括它们中的完整数据
// */
//define('AMQP_DURABLE', 2);
///**
// * 被动模式的交换机和队列不能被重新定义,但是如果交换机和队列不存在,代理将扔出一个错误提示
// */
//define('AMQP_PASSIVE', 4);
///**
// * 仅对队列有效,这个人标志定义队列仅允许一个客户端连接并且从其消费消息
// */
//define('AMQP_EXCLUSIVE', 8);
///**
// * 对交换机而言,自动删除标志表示交换机将在没有队列绑定的情况下被自动删除,如果从没有队列和其绑定过,这个交换机将不会被删除.
// * 对队列而言,自动删除标志表示如果没有消费者和你绑定的话将被自动删除,如果从没有消费者和其绑定,将不被删除,独占队列在客户断
// * 开连接的时候将总是会被删除
// */
//define('AMQP_AUTODELETE', 16);
///**
// * 这个标志标识不允许自定义队列绑定到交换机上
// */
//define('AMQP_INTERNAL', 32);
///**
// * 在集群环境消费方法中传递这个参数,表示将不会从本地站点消费消息
// */
//define('AMQP_NOLOCAL', 64);
///**
// * 当在队列get方法中作为标志传递这个参数的时候,消息将在被服务器输出之前标志为acknowledged (已收到)
// */
//define('AMQP_AUTOACK', 128);
///**
// * 在队列建立时候传递这个参数,这个标志表示队列将在为空的时候被删除
// */
//define('AMQP_IFEMPTY', 256);
///**
// * 在交换机或者队列建立的时候传递这个参数,这个标志表示没有客户端连接的时候,交换机或者队列将被删除
// */
//define('AMQP_IFUNUSED', 512);
///**
// * 当发布消息的时候,消息必须被正确路由到一个有效的队列,否则将返回一个错误
// */
//define('AMQP_MANDATORY', 1024);
///**
// * 当发布消息时候,这个消息将被立即处理.
// */
//define('AMQP_IMMEDIATE', 2048);
///**
// * 当在调用AMQPQueue::ack时候设置这个标志,传递标签将被视为最大包含数量,以便通过单个方法标示多个消息为已收到,如果设置为0
// * 传递标签指向单个消息,如果设置了AMQP_MULTIPLE,并且传递标签是0,将所有未完成消息标示为已收到
// */
//define('AMQP_MULTIPLE', 4096);
///**
// * 当在调用AMQPExchange::bind()方法的时候,服务器将不响应请求,客户端将不应该等待响应,如果服务器无法完成该方法,将会抛出一个异常
// */
//define('AMQP_NOWAIT', 8192);
///**
// * 如果在调用AMQPQueue::nack方法时候设置,消息将会被传递回队列
// */
//define('AMQP_REQUEUE', 16384);
//
///**
// * direct类型交换机
// */
//define('AMQP_EX_TYPE_DIRECT', 'direct');
///**
// * fanout类型交换机
// */
//define('AMQP_EX_TYPE_FANOUT', 'fanout');
///**
// *  topic类型交换机
// */
//define('AMQP_EX_TYPE_TOPIC', 'topic');
///**
// * header类型交换机
// */
//define('AMQP_EX_TYPE_HEADERS', 'headers');
///**
// * socket连接超时设置
// */
//define('AMQP_OS_SOCKET_TIMEOUT_ERRNO', 536870947);

class AmqpManager{
	
	private $config;
	private $connection;
	private $channel;
	
	public function __construct()
	{
		$this->config=array_replace($this->getDefaultConfig(),\Core::config('amqp',false));
	}
	
	/**
	 * 关闭连接
	 * 必须记得关闭连接
	 */
	public function close()
	{
		if($this->channel){
			$this->channel->close();
		}
		if($this->connection){
			$this->connection->close();
		}
	}
	
	/**
	 * 绑定队列到Direct路由器
	 * 一个队列可以绑定到多个routing_key
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 * @param string $routing_key		      路由key
	 * @param int $priority				      最大优先级
	 */
	public function bindQueueToExchangeDirect($exchange,$queue,$routing_key='',$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_DIRECT, false, true, false);
		$channel->queue_bind($queue, $exchange,$routing_key);
	}
	
	/**
	 * 绑定队列到Delay路由器
	 * 一个队列可以绑定到多个routing_key
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 * @param string $routing_key		      路由key
	 */
	public function bindQueueToExchangeDelay($exchange,$queue,$routing_key='',$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->exchange_declare($exchange, "x-delayed-message", false, true, false,false,false,new AMQPTable(array('x-delayed-type'=>'direct')));
		$channel->queue_bind($queue, $exchange,$routing_key);
	}
	
	/**
	 * 绑定队列到Fanout路由器(
	 * 广播消息默认非持久化，并且自动删除
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 */
	public function bindQueueToExchangeFanout($exchange,$queue,$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_FANOUT, false, false, true);
		$channel->queue_bind($queue, $exchange);
	}
	
	/**
	 * 绑定队列到Topic路由器
	 * routing_key中*表示由.符号分隔的一个单词,#表示任意长度的内容
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 * @param string $routing_key		      路由key，类似于#.log,*.log
	 */
	public function bindQueueToExchangeTopic($exchange,$queue,$routing_key='',$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false ,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_TOPIC, false, true, false);
		$channel->queue_bind($queue, $exchange,$routing_key);
	}
	
	/**
	 * 发布消息到Direct路由
	 * 要接收Direct路由的信息，需要先声明一个queue，并绑定到指定的exchange，指定routing_key
	 * 如果发送Direct路由信息，不存在指定routing_key并与exchange进行绑定的queue，则该条信息将被抛弃
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 * @param string | array $messageBody  消息内容
	 * @param string $routing_key		      路由key
	 * @param int $priority				      优先级
	 * @param int $expiration			      过期时间（秒）
	 */
	public function publishDirect($exchange,$messageBody,$routing_key='',$priority=0,$expiration=0){
		$channel=$this->getChannel();
		$properties = array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT);
		if(is_numeric($priority) && $priority>0){
			$properties['priority']=$priority;
		}
		if(is_numeric($expiration) && $expiration>0){
			$properties['expiration']=$expiration;
		}
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_DIRECT, false, true, false);
		$messages=is_array($messageBody)?$messageBody:array($messageBody);
		
		foreach($messages as $v){
			$message = new AMQPMessage($v, $properties);
			$channel->basic_publish($message, $exchange,$routing_key);
		}
	}
	
	/**
	 * 发送延迟消息
	 * 延迟消息只能使用Direct
	 * 
	 * @param int $delay 延时时间(秒)
	 */
	public function publishDelay($exchange,$messageBody,$routing_key='',$priority=0,$expiration=0,$delay=0){
		$channel=$this->getChannel();
		$properties = array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT);
		if(is_numeric($priority) && $priority>0){
			$properties['priority']=$priority;
		}
		if(is_numeric($expiration) && $expiration>0){
			$properties['expiration']=$expiration;
		}
		$channel->exchange_declare($exchange, "x-delayed-message", false, true, false,false,false,new AMQPTable(array('x-delayed-type'=>'direct')));
		$messages=is_array($messageBody)?$messageBody:array($messageBody);
		
		$headers=null;
		if(is_numeric($delay) && $delay>0){
				$headers = new AMQPTable(array("x-delay" => $delay*1000));
		}
		
		foreach($messages as $v){
			$message = new AMQPMessage($v, $properties);
			if($headers){
				$message->set('application_headers', $headers);
			}
			$channel->basic_publish($message, $exchange,$routing_key);
		}
	}
	
	/**
	 * 发布广播消息
	 * 广播消息是非持久的
	 * 广播消息会自动删除
	 * 
	 * @param string $exchange
	 * @param string | array $messageBody
	 */
	public function publishFanout($exchange,$messageBody){
		$channel=$this->getChannel();
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_FANOUT, false, false, true);
		$messages=is_array($messageBody)?$messageBody:array($messageBody);
		$properties = array('content_type' => 'text/plain');
		foreach($messages as $v){
			$message = new AMQPMessage($v, $properties);
			$channel->basic_publish($message, $exchange);
		}
	}
	
	/**
	 * 发布消息到Topic路由
	 * routing_key的.符号为分隔符匹配
	 * 
	 * @param string $exchange             交换机名称
	 * @param string $queue                队列名称
	 * @param string | array $messageBody  消息内容
	 * @param string $routing_key		      路由key，如mail.log,mobile.log
	 * @param int $priority				      优先级
	 * @param int $expiration			      过期时间（秒）
	 */
	public function publishTopic($exchange,$messageBody,$routing_key='',$priority=0,$expiration=0){
		$channel=$this->getChannel();
		$properties = array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT);
		if(is_numeric($priority) && $priority>0){
			$properties['priority']=$priority;
		}
		if(is_numeric($expiration) && $expiration>0){
			$properties['expiration']=$expiration;
		}
		$channel->exchange_declare($exchange, AMQP_EX_TYPE_TOPIC, false, true, false);
		$messages=is_array($messageBody)?$messageBody:array($messageBody);
		
		foreach($messages as $v){
			$message = new AMQPMessage($v, $properties);
			$channel->basic_publish($message, $exchange,$routing_key);
		}
	}
	
	/**
	 * 阻塞接收一条队列信息
	 * 默认自动应答
	 * 
	 * @param string $callback 回调方法
	 * @param boolean $no_ack    不使用自动应答
	 */
	public function consume($queue,$callback,$no_ack=false,$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->basic_consume($queue, 'consumer', false, $no_ack, true, false, $callback);
		if(count($channel->callbacks)){
			$channel->wait();
		}
	}
	
	/**
	 * 循环阻塞接收信息
	 * 仅在task中调用
	 * 默认自动应答
	 * 
	 * @param string $callback 回调方法
	 */
	public function consumeWhile($queue,$callback,$no_ack=false,$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		$channel->basic_consume($queue, 'consumer', false, $no_ack, true, false, $callback);
		while(count($channel->callbacks)){
			$channel->wait();
		}
	}
	
	/**
	 * 非阻塞接收一条队列消息
	 * 
	 * @param boolean $no_ack    不使用自动应答，默认使用自动应答
	 */
	public function getOne($queue,$no_ack=false,$priority=5){
		$channel=$this->getChannel();
		$channel->queue_declare($queue, false, true, false, false,false, new AMQPTable(array("x-max-priority"=>$priority)));
		return $channel->basic_get($queue,$no_ack);
	}
	
	/**
	 * 获取Amqp连接
	 */
	private function getConnection(){
		if(!$this->connection){
			$this->connect();
		}
		return $this->connection;
	}
	
	/**
	 * 获取Amqp Channel
	 */
	private function getChannel(){
		if(!$this->channel){
			$this->channel=$this->getConnection()->channel();
		}
		return $this->channel;
	}
	
	/**
	 * Amqp连接
	 * 
	 * @param boolean $insist      是否是持久链接
	 */
	private function connect($insist=false){
		$this->connection=new AMQPStreamConnection($this->config['host'], 
				$this->config['port'], 
				$this->config['user'], 
				$this->config['pass'], 
				$this->config['vhost'],
				$insist
			);
	}
	
	/**
	 * 获取默认配置
	 */
	private function getDefaultConfig(){
		return array(
		'log_path'=>'',
		'host' => 'localhost',
        'port' => 5672,
        'vhost' => '/',
        'user' => 'guest',
        'pass' => 'guest',
        'debug' => true
		);
	}

}
?>