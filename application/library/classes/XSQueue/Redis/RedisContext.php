<?php

namespace XSQueue\Redis;

use XSQueue\InvalidDestinationException;
use XSQueue\Context;
use XSQueue\Destination;
use XSQueue\Queue;
use XSQueue\Topic;

class RedisContext implements Context
{
    private $redis;
    private $redisFactory;

    public function __construct($redis)
    {
        if ($redis instanceof Redis) {
            $this->redis = $redis;
        } elseif (is_callable($redis)) {
            $this->redisFactory = $redis;
        } else {
            throw new \InvalidArgumentException(sprintf(
                'The $redis argument must be either %s or callable that returns %s once called.',
                Redis::class,
                Redis::class
            ));
        }
    }

    public function createMessage($body = '', array $properties = [], array $headers = [])
    {
        return new RedisMessage($body, $properties, $headers);
    }


    public function createTopic($topicName)
    {
        return new RedisDestination($topicName);
    }


    public function createQueue($queueName)
    {
        return new RedisDestination($queueName);
    }


    public function deleteQueue(Queue $queue)
    {
        InvalidDestinationException::assertDestinationInstanceOf($queue, RedisDestination::class);

        $this->getRedis()->del($queue->getName());
    }


    public function deleteTopic(Topic $topic)
    {
        InvalidDestinationException::assertDestinationInstanceOf($topic, RedisDestination::class);

        $this->getRedis()->del($topic->getName());
    }


    public function createTemporaryQueue()
    {
        throw new \LogicException('Not implemented');
    }


    public function createProducer()
    {
        return new RedisProducer($this->getRedis());
    }


    public function createConsumer(Destination $destination)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, RedisDestination::class);

        return new RedisConsumer($this, $destination);
    }

    public function close()
    {
        $this->getRedis()->disconnect();
    }


    public function getRedis()
    {
        if (false == $this->redis) {
            $redis = call_user_func($this->redisFactory);
            if (false == $redis instanceof Redis) {
                throw new \LogicException(sprintf(
                    'The factory must return instance of %s. It returned %s',
                    Redis::class,
                    is_object($redis) ? get_class($redis) : gettype($redis)
                ));
            }

            $this->redis = $redis;
        }

        return $this->redis;
    }
}
