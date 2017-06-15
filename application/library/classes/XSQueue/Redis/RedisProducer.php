<?php

namespace XSQueue\Redis;

use XSQueue\InvalidDestinationException;
use XSQueue\InvalidMessageException;
use XSQueue\Destination;
use XSQueue\Message;
use XSQueue\Producer;

class RedisProducer implements Producer
{
    private $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function send(Destination $destination, Message $message)
    {
        InvalidDestinationException::assertDestinationInstanceOf($destination, RedisDestination::class);
        InvalidMessageException::assertMessageInstanceOf($message, RedisMessage::class);

        $this->redis->lpush($destination->getName(), json_encode($message));
    }
}
