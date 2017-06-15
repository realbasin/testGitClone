<?php
namespace XSQueue\AmqpExt;

use XSQueue\InvalidDestinationException;
use XSQueue\Context;
use XSQueue\Destination;
use XSQueue\Queue;
use XSQueue\Topic;

class AmqpContext implements Context
{
    private $extChannel;
    private $extChannelFactory;
    private $buffer;
    private $receiveMethod;

    /**
     *
     * @param \AMQPChannel|callable $extChannel
     * @param string                $receiveMethod
     */
    public function __construct($extChannel, $receiveMethod)
    {
        $this->receiveMethod = $receiveMethod;

        if ($extChannel instanceof \AMQPChannel) {
            $this->extChannel = $extChannel;
        } elseif (is_callable($extChannel)) {
            $this->extChannelFactory = $extChannel;
        } else {
            throw new \InvalidArgumentException('The extChannel argument must be either AMQPChannel or callable that return AMQPChannel.');
        }

        $this->buffer = new Buffer();
    }

    /**
     *
     * @return AmqpMessage
     */
    public function createMessage($body = '', Array $properties = array(), Array $headers = array())
    {
        return new AmqpMessage($body, $properties, $headers);
    }

    /**
     *
     * @return AmqpTopic
     */
    public function createTopic($topicName)
    {
        return new AmqpTopic($topicName);
    }

    /**
     * @param AmqpTopic|Destination $destination
     */
    public function deleteTopic(Destination $destination)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpTopic::class);

        $extExchange = new \AMQPExchange($this->getExtChannel());
        $extExchange->delete($destination->getTopicName(), $destination->getFlags());
    }

    /**
     * @param AmqpTopic|Destination $destination
     */
    public function declareTopic(Destination $destination)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpTopic::class);

        $extExchange = new \AMQPExchange($this->getExtChannel());
        $extExchange->setName($destination->getTopicName());
        $extExchange->setType($destination->getType());
        $extExchange->setArguments($destination->getArguments());
        $extExchange->setFlags($destination->getFlags());

        $extExchange->declareExchange();
    }

    /**
     *
     * @return AmqpQueue
     */
    public function createQueue($queueName)
    {
        return new AmqpQueue($queueName);
    }

    /**
     * @param AmqpQueue|Destination $destination
     */
    public function deleteQueue(Destination $destination)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpQueue::class);

        $extQueue = new \AMQPQueue($this->getExtChannel());
        $extQueue->setName($destination->getQueueName());
        $extQueue->delete($destination->getFlags());
    }

    /**
     * @param AmqpQueue|Destination $destination
     *
     * @return int
     */
    public function declareQueue(Destination $destination)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpQueue::class);

        $extQueue = new \AMQPQueue($this->getExtChannel());
        $extQueue->setFlags($destination->getFlags());
        $extQueue->setArguments($destination->getArguments());

        if ($destination->getQueueName()) {
            $extQueue->setName($destination->getQueueName());
        }

        $count = $extQueue->declareQueue();

        if (false == $destination->getQueueName()) {
            $destination->setQueueName($extQueue->getName());
        }

        return $count;
    }

    /**
     * 创建临时队列
     * @return AmqpQueue
     */
    public function createTemporaryQueue()
    {
        $queue = $this->createQueue(null);
        $queue->addFlag(AMQP_EXCLUSIVE);

        $this->declareQueue($queue);

        return $queue;
    }

    /**
     * 创建生产者
     * @return AmqpProducer
     */
    public function createProducer()
    {
        return new AmqpProducer($this->getExtChannel());
    }

    /**
     * 创建消费者
     *
     * @param Destination|AmqpQueue $destination
     *
     * @return AmqpConsumer
     */
    public function createConsumer(Destination $destination)
    {
        $destination instanceof Topic
            ? InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpTopic::class)
            : InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpQueue::class)
        ;

        if ($destination instanceof AmqpTopic) {
            $queue = $this->createTemporaryQueue();
            $this->bind($destination, $queue);

            return new AmqpConsumer($this, $queue, $this->buffer, $this->receiveMethod);
        }

        return new AmqpConsumer($this, $destination, $this->buffer, $this->receiveMethod);
    }

    public function close()
    {
        $extConnection = $this->getExtChannel()->getConnection();
        if ($extConnection->isConnected()) {
            $extConnection->isPersistent() ? $extConnection->pdisconnect() : $extConnection->disconnect();
        }
    }

    /**
     * @param AmqpTopic|Destination $source
     * @param AmqpQueue|Destination $target
     */
    public function bind(Destination $source, Destination $target)
    {
        InvalidDestinationException::assertDestinationInstanceOf($source, AmqpTopic::class);
        InvalidDestinationException::assertDestinationInstanceOf($target, AmqpQueue::class);

        $amqpQueue = new \AMQPQueue($this->getExtChannel());
        $amqpQueue->setName($target->getQueueName());
        $amqpQueue->bind($source->getTopicName(), $amqpQueue->getName(), $target->getBindArguments());
    }

    /**
     * @return \AMQPConnection
     */
    public function getExtConnection()
    {
        return $this->getExtChannel()->getConnection();
    }

    /**
     * @return \AMQPChannel
     */
    public function getExtChannel()
    {
        if (false == $this->extChannel) {
            $extChannel = call_user_func($this->extChannelFactory);
            if (false == $extChannel instanceof \AMQPChannel) {
                throw new \LogicException(sprintf(
                    'The factory must return instance of AMQPChannel. It returns %s',
                    is_object($extChannel) ? get_class($extChannel) : gettype($extChannel)
                ));
            }

            $this->extChannel = $extChannel;
        }

        return $this->extChannel;
    }

    /**
     * 清除指定队列的全部消息
     *
     * @param Queue $queue
     */
    public function purge(Queue $queue)
    {
        InvalidDestinationException::assertDestinationInstanceOf($queue, AmqpQueue::class);

        $amqpQueue = new \AMQPQueue($this->getExtChannel());
        $amqpQueue->setName($queue->getQueueName());
        $amqpQueue->purge();
    }
}
