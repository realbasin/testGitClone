<?php

namespace  XSQueue\AmqpExt\Client;

use XSQueue\AmqpExt\AmqpContext;
use XSQueue\AmqpExt\AmqpMessage;
use XSQueue\AmqpExt\AmqpQueue;
use XSQueue\AmqpExt\AmqpTopic;
use XSQueue\Client\Config;
use XSQueue\Client\Message;
use XSQueue\Client\MessagePriority;
use XSQueue\Client\Meta\QueueMetaRegistry;
use XSQueue\ClientMessage;
use XSQueue\Client\Logger;
use XSQueue\Client\LoggerInterface;


/**
 * RabbitMqDrvier
 */
class RabbitMqDriver extends AmqpDriver
{
    private $context;
    private $config;
    private $queueMetaRegistry;
    private $priorityMap;

    public function __construct(AmqpContext $context, Config $config, QueueMetaRegistry $queueMetaRegistry)
    {
        parent::__construct($context, $config, $queueMetaRegistry);

        $this->config = $config;
        $this->context = $context;
        $this->queueMetaRegistry = $queueMetaRegistry;

        $this->priorityMap = array(
            MessagePriority::VERY_LOW => 0,
            MessagePriority::LOW => 1,
            MessagePriority::NORMAL => 2,
            MessagePriority::HIGH => 3,
            MessagePriority::VERY_HIGH => 4,
        );
    }

    /**
     * 发送信息到指定的队列
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

        if ($message->getDelay()) {
            $destination = $this->createDelayedTopic($destination);
        }

        $this->context->createProducer()->send($destination, $transportMessage);
    }

    /**
     * 创建最高优先权的持久化队列
     *
     * @return AmqpQueue
     */
    public function createQueue($queueName)
    {
        $queue = parent::createQueue($queueName);
        $queue->setArguments(['x-max-priority' => 4]);

        return $queue;
    }

    /**
     * 创建可持久化的Message
	 * 该Message可设置优先权
	 * 该Message可设置延迟
	 * 该Message可设置过期时间
     *
     * @return AmqpMessage
     */
    public function createTransportMessage(Message $message)
    {
        $transportMessage = parent::createTransportMessage($message);

        if ($priority = $message->getPriority()) {
            if (false == array_key_exists($priority, $this->priorityMap)) {
                throw new \InvalidArgumentException(sprintf(
                    'Given priority could not be converted to client\'s one. Got: %s',
                    $priority
                ));
            }

            $transportMessage->setHeader('priority', $this->priorityMap[$priority]);
        }

        if ($message->getDelay()) {
            if (false == $this->config->getTransportOption('delay_plugin_installed', false)) {
                throw new LogicException('The message delaying is not supported. In order to use delay feature install RabbitMQ delay plugin.');
            }

            $transportMessage->setProperty('x-delay', (string) ($message->getDelay() * 1000));
        }

        return $transportMessage;
    }

    /**
     * 根据已有的ClientMessage来创建一个Message
	 * 该Message可设置优先权
	 * 该Message可设置延迟
	 * 该Message可设置过期时间
	 * 
	 * @param AmqpMessage $message
     *
     */
    public function createClientMessage(ClientMessage $message)
    {
        $clientMessage = parent::createClientMessage($message);

        if ($priority = $message->getHeader('priority')) {
            if (false === $clientPriority = array_search($priority, $this->priorityMap, true)) {
                throw new \LogicException(sprintf('Cant convert transport priority to client: "%s"', $priority));
            }

            $clientMessage->setPriority($clientPriority);
        }

        if ($delay = $message->getProperty('x-delay')) {
            if (false == is_numeric($delay)) {
                throw new \LogicException(sprintf('x-delay header is not numeric. "%s"', $delay));
            }

            $clientMessage->setDelay((int) ((int) $delay) / 1000);
        }

        return $clientMessage;
    }

    /**
     * 初始化代理
     */
    public function setupBroker(LoggerInterface $logger = null)
    {
        $logger = $logger ?: new Logger();

        parent::setupBroker($logger);

        $log = function ($text, $args) use ($logger) {
            $logger->write(sprintf('[AmqpDriver] '.$text, $args));
        };

        // 设置延迟路由
        if ($this->config->getTransportOption('delay_plugin_installed', false)) {
            foreach ($this->queueMetaRegistry->getQueuesMeta() as $meta) {
                $queue = $this->createQueue($meta->getClientName());

                $delayTopic = $this->createDelayedTopic($queue);

                $log('Declare delay exchange: %s', $delayTopic->getTopicName());
                $this->context->declareTopic($delayTopic);

                $log('Bind processor queue to delay exchange: %s -> %s', $queue->getQueueName(), $delayTopic->getTopicName());
                $this->context->bind($delayTopic, $queue);
            }
        }
    }

    /**
     * 创建一个延时路由
	 * 
	 * @param AmqpQueue $queue
     *
     * @return AmqpTopic
     */
    private function createDelayedTopic(AmqpQueue $queue)
    {
        $queueName = $queue->getQueueName();

        // 要使用延时路由需要安装 rabbitmq_delayed_message_exchange 插件.
        $delayTopic = $this->context->createTopic($queueName.'.delayed');
        $delayTopic->setRoutingKey($queueName);
        $delayTopic->setType('x-delayed-message');
        $delayTopic->addFlag(AMQP_DURABLE);
        $delayTopic->setArguments(array(
            'x-delayed-type' => 'direct',
        ));

        return $delayTopic;
    }
}
