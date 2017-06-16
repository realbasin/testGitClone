<?php
namespace XSQueue\AmqpExt;

use XSQueue\ConnectionFactory;


//以下为常量定义解释
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

class AmqpConnectionFactory implements ConnectionFactory
{
    private $config;
    private $connection;

    /**
     * 配置可以是array，DSN字符串或者Null
	 * 如果传入Null则使用本机默认设置
     *
     * @param array|string $config
     */
    public function __construct($config = 'amqp://')
    {
    	if(!extension_loaded("amqp")){
    		throw new \LogicException('Amqp is not installed on this server');
    	}
        if (empty($config) || 'amqp://' === $config) {
            $config = array();
        } elseif (is_string($config)) {
            $config = $this->parseDsn($config);
        } elseif (is_array($config)) {
        } else {
            throw new \LogicException('The config must be either an array of options, a DSN string or null');
        }

        $this->config = array_replace($this->defaultConfig(), $config);

        $supportedMethods = array('basic_get', 'basic_consume');
        if (false == in_array($this->config['receive_method'], $supportedMethods, true)) {
            throw new \LogicException(sprintf(
                'Invalid "receive_method" option value "%s". It could be only "%s"',
                $this->config['receive_method'],
                implode('", "', $supportedMethods)
            ));
        }

        if ('basic_consume' == $this->config['receive_method']) {
            if (false == (version_compare(phpversion('amqp'), '1.9.1', '>=') || phpversion('amqp') == '1.9.1-dev')) {
                throw new \LogicException('The "basic_consume" method does not work on amqp extension prior 1.9.1 version.');
            }
        }
    }

    /**
     *
     * @return AmqpContext
     */
    public function createContext()
    {
        if ($this->config['lazy']) {
            return new AmqpContext(function () {
                return $this->createExtContext($this->establishConnection());
            }, $this->config['receive_method']);
        }

        return new AmqpContext($this->createExtContext($this->establishConnection()), $this->config['receive_method']);
    }

    /**
     * @param \AMQPConnection $extConnection
     *
     * @return \AMQPChannel
     */
    private function createExtContext(\AMQPConnection $extConnection)
    {
        $channel = new \AMQPChannel($extConnection);
        if (false == empty($this->config['pre_fetch_count'])) {
            $channel->setPrefetchCount((int) $this->config['pre_fetch_count']);
        }

        if (false == empty($this->config['pre_fetch_size'])) {
            $channel->setPrefetchSize((int) $this->config['pre_fetch_size']);
        }

        return $channel;
    }

    /**
     * @return \AMQPConnection
     */
    private function establishConnection()
    {
        if (false == $this->connection) {
            $config = $this->config;
            $config['login'] = $this->config['user'];
            $config['password'] = $this->config['pass'];

            $this->connection = new \AMQPConnection($config);

            $this->config['persisted'] ? $this->connection->pconnect() : $this->connection->connect();
        }
        if (false == $this->connection->isConnected()) {
            $this->config['persisted'] ? $this->connection->preconnect() : $this->connection->reconnect();
        }

        return $this->connection;
    }

    /**
     * @param string $dsn
     *
     * @return array
     */
    private function parseDsn($dsn)
    {
        $dsnConfig = parse_url($dsn);
        if (false === $dsnConfig) {
            throw new \LogicException(sprintf('Failed to parse DSN "%s"', $dsn));
        }

        $dsnConfig = array_replace(array(
            'scheme' => null,
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'path' => null,
            'query' => null,
        ), $dsnConfig);

        if ('amqp' !== $dsnConfig['scheme']) {
            throw new \LogicException(sprintf('The given DSN scheme "%s" is not supported. Could be "amqp" only.',$dsnConfig['scheme']));
        }

        if ($dsnConfig['query']) {
            $query = array();
            parse_str($dsnConfig['query'], $query);

            $dsnConfig = array_replace($query, $dsnConfig);
        }

        $dsnConfig['vhost'] = ltrim($dsnConfig['path'], '/');

        unset($dsnConfig['scheme'], $dsnConfig['query'], $dsnConfig['fragment'], $dsnConfig['path']);

        $config = array_replace($this->defaultConfig(), $dsnConfig);
        $config = array_map(function ($value) {
            return urldecode($value);
        }, $config);

        return $config;
    }

    /**
     * @return array
     */
    private function defaultConfig()
    {
        return array(
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
        );
    }
}
