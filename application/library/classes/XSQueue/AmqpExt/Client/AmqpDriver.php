<?php

namespace  XSQueue\AmqpExt\Client;

use XSQueue\ClientMessage;
use XSQueue\AmqpExt\AmqpContext;
use XSQueue\AmqpExt\AmqpMessage;
use XSQueue\AmqpExt\AmqpQueue;
use XSQueue\AmqpExt\AmqpTopic;
use XSQueue\AmqpExt\DeliveryMode;
use XSQueue\Client\Config;
use XSQueue\Client\DriverInterface;
use XSQueue\Client\Message;
use XSQueue\Client\Logger;
use XSQueue\Client\LoggerInterface;
use XSQueue\Client\Meta\QueueMetaRegistry;

class AmqpDriver implements DriverInterface
{
    private $context;
    private $config;
    private $queueMetaRegistry;

    /**
	 * 初始化Amqp驱动类
	 *
     * @param AmqpContext       $context
     * @param Config            $config
     * @param QueueMetaRegistry $queueMetaRegistry
     */
    public function __construct(AmqpContext $context, Config $config, QueueMetaRegistry $queueMetaRegistry)
    {
        $this->context = $context;
        $this->config = $config;
        $this->queueMetaRegistry = $queueMetaRegistry;
    }
	
	/**
	 * 发送消息到AMQP_EX_TYPE_FANOUT路由
	 */
    public function sendToRouter(Message $message)
    {
        if (false == $message->getProperty(Config::PARAMETER_TOPIC_NAME)) {
            throw new \LogicException('Topic name parameter is required but is not set');
        }

        $topic = $this->createRouterTopic();
        $transportMessage = $this->createTransportMessage($message);

        $this->context->createProducer()->send($topic, $transportMessage);
    }

    /**
     * 发送Message到队列
	 * 队列名称由$message->getProperty指定
     */
    public function sendToProcessor(Message $message)
    {
        if (false == $message->getProperty(Config::PARAMETER_PROCESSOR_NAME)) {
            throw new \LogicException('Processor name parameter is required but is not set');
        }

        if (false == $queueName = $message->getProperty(Config::PARAMETER_PROCESSOR_QUEUE_NAME)) {
            throw new \LogicException('Queue name parameter is required but is not set');
        }

        $transportMessage = $this->createTransportMessage($message);
        $destination = $this->createQueue($queueName);

        $this->context->createProducer()->send($destination, $transportMessage);
    }

    /**
     * 初始化代理
     */
    public function setupBroker(LoggerInterface $logger = null)
    {
        $logger = $logger ?: new Logger();
        $log = function ($text, $args) use ($logger) {
            $logger->write(sprintf('[AmqpDriver] '.$text, $args));
        };

        $routerTopic = $this->createRouterTopic();
        $routerQueue = $this->createQueue($this->config->getRouterQueueName());

        $log('Declare router exchange: %s', $routerTopic->getTopicName());
        $this->context->declareTopic($routerTopic);
        $log('Declare router queue: %s', $routerQueue->getQueueName());
        $this->context->declareQueue($routerQueue);
        $log('Bind router queue to exchange: %s -> %s', $routerQueue->getQueueName(), $routerTopic->getTopicName());
        $this->context->bind($routerTopic, $routerQueue);

        foreach ($this->queueMetaRegistry->getQueuesMeta() as $meta) {
            $queue = $this->createQueue($meta->getClientName());

            $log('Declare processor queue: %s', $queue->getQueueName());
            $this->context->declareQueue($queue);
        }
    }

    /**
     * 创建持久化队列
	 * 
     * @return AmqpQueue
     */
    public function createQueue($queueName)
    {
        $transportName = $this->queueMetaRegistry->getQueueMeta($queueName)->getTransportName();

        $queue = $this->context->createQueue($transportName);
        $queue->addFlag(AMQP_DURABLE);

        return $queue;
    }

    /**
     * 创建可持久化Message
	 * 可设置过期时间
	 * 
     * @return AmqpMessage
     */
    public function createTransportMessage(Message $message)
    {
        $headers = $message->getHeaders();
        $properties = $message->getProperties();

        $headers['content_type'] = $message->getContentType();

        if ($message->getExpire()) {
            $headers['expiration'] = (string) ($message->getExpire() * 1000);
        }

        $headers['delivery_mode'] = DeliveryMode::PERSISTENT;

        $transportMessage = $this->context->createMessage();
        $transportMessage->setBody($message->getBody());
        $transportMessage->setHeaders($headers);
        $transportMessage->setProperties($properties);
        $transportMessage->setMessageId($message->getMessageId());
        $transportMessage->setTimestamp($message->getTimestamp());
        $transportMessage->setReplyTo($message->getReplyTo());
        $transportMessage->setCorrelationId($message->getCorrelationId());

        return $transportMessage;
    }

    /**
     * 从ClientMessage中创建一个Message
	 * 
	 * @param AmqpMessage $message
     *
     */
    public function createClientMessage(ClientMessage $message)
    {
        $clientMessage = new Message();

        $clientMessage->setBody($message->getBody());
        $clientMessage->setHeaders($message->getHeaders());
        $clientMessage->setProperties($message->getProperties());

        $clientMessage->setContentType($message->getHeader('content_type'));

        if ($expiration = $message->getHeader('expiration')) {
            if (false == is_numeric($expiration)) {
                throw new \LogicException(sprintf('expiration header is not numeric. "%s"', $expiration));
            }

            $clientMessage->setExpire((int) ((int) $expiration) / 1000);
        }

        $clientMessage->setMessageId($message->getMessageId());
        $clientMessage->setTimestamp($message->getTimestamp());
        $clientMessage->setReplyTo($message->getReplyTo());
        $clientMessage->setCorrelationId($message->getCorrelationId());

        return $clientMessage;
    }

    /**
	 * 获取配置类
	 *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
	 * 创建AMQP_EX_TYPE_FANOUT类型TOPIC
	 *
     * @return AmqpTopic
     */
    private function createRouterTopic()
    {
        $topic = $this->context->createTopic(
            $this->config->createTransportRouterTopicName($this->config->getRouterTopicName())
        );
        $topic->setType(AMQP_EX_TYPE_FANOUT);
        $topic->addFlag(AMQP_DURABLE);

        return $topic;
    }
}
