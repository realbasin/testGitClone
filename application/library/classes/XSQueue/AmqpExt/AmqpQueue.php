<?php

namespace XSQueue\AmqpExt;

use XSQueue\Queue;

class AmqpQueue implements Queue
{
    private $name;
    private $flags;
    private $arguments;
    private $bindArguments;
    private $consumerTag;

    public function __construct($name)
    {
        $this->name = $name;

        $this->arguments = [];
        $this->bindArguments = [];
        $this->flags = AMQP_NOPARAM;
    }

    public function getQueueName()
    {
        return $this->name;
    }

    public function setQueueName($name)
    {
        $this->name = $name;
    }

    public function getConsumerTag()
    {
        return $this->consumerTag;
    }

    public function setConsumerTag($consumerTag)
    {
        $this->consumerTag = $consumerTag;
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

    public function getBindArguments()
    {
        return $this->bindArguments;
    }

    public function setBindArguments(array $arguments = null)
    {
        $this->bindArguments = $arguments;
    }
}
