<?php

namespace XSQueue\Redis;

use XSQueue\Queue;
use XSQueue\Topic;

class RedisDestination implements Queue, Topic
{
    private $name;


    public function __construct($name)
    {
        $this->name = $name;
    }


    public function getName()
    {
        return $this->name;
    }


    public function getQueueName()
    {
        return $this->getName();
    }


    public function getTopicName()
    {
        return $this->getName();
    }
}
