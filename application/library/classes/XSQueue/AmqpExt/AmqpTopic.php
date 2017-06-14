<?php

namespace XSQueue\AmqpExt;

use XSQueue\Topic;

class AmqpTopic implements Topic
{
    private $name;
    private $type;
    private $flags;
    private $routingKey;
    private $arguments;

    public function __construct($name)
    {
        $this->name = $name;

        $this->type = AMQP_EX_TYPE_DIRECT;
        $this->flags = AMQP_NOPARAM;
        $this->arguments = [];
    }

    public function getTopicName()
    {
        return $this->name;
    }

    public function setTopicName($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function addFlag($flag)
    {
        $this->flags |= $flag;
    }

    public function clearFlags()
    {
        $this->flags = AMQP_NOPARAM;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments = null)
    {
        $this->arguments = $arguments;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;
    }
}
