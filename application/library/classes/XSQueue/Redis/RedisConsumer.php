<?php

namespace XSQueue\Redis;

use XSQueue\InvalidMessageException;
use XSQueue\Consumer;
use XSQueue\Message;

class RedisConsumer implements Consumer
{
    private $queue;
    private $context;

    /**
     * @param RedisContext     $context
     * @param RedisDestination $queue
     */
    public function __construct(RedisContext $context, RedisDestination $queue)
    {
        $this->context = $context;
        $this->queue = $queue;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function receive($timeout = 0)
    {
        $timeout = (int) ($timeout / 1000);
        if (empty($timeout)) {
            return $this->receiveNoWait();
        }

        if ($message = $this->getRedis()->brpop($this->queue->getName(), $timeout)) {
            return RedisMessage::jsonUnserialize($message);
        }
    }

    public function receiveNoWait()
    {
        if ($message = $this->getRedis()->rpop($this->queue->getName())) {
            return RedisMessage::jsonUnserialize($message);
        }
    }

    public function acknowledge(Message $message)
    {
        // redis默认自动应答
    }

    public function reject(PsrMessage $message, $requeue = false)
    {
        InvalidMessageException::assertMessageInstanceOf($message, RedisMessage::class);
        // redis默认自动应答
        if ($requeue) {
            $this->context->createProducer()->send($this->queue, $message);
        }
    }

    private function getRedis()
    {
        return $this->context->getRedis();
    }
}
