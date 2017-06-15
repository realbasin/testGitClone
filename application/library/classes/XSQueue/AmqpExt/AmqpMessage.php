<?php

namespace XSQueue\AmqpExt;

use XSQueue\Message;

class AmqpMessage implements Message
{
    private $body;
    private $properties;
    private $headers;
    private $deliveryTag;
    private $consumerTag;
    private $redelivered;
    private $flags;

    /**
     * @param string $body
     * @param array  $properties
     * @param array  $headers
     */
    public function __construct($body = '', array $properties = [], array $headers = [])
    {
        $this->body = $body;
        $this->properties = $properties;
        $this->headers = $headers;

        $this->redelivered = false;
        $this->flags = AMQP_NOPARAM;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    public function getProperty($name, $default = null)
    {
        return array_key_exists($name, $this->properties) ? $this->properties[$name] : $default;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeader($name, $default = null)
    {
        return array_key_exists($name, $this->headers) ? $this->headers[$name] : $default;
    }

    public function setRedelivered($redelivered)
    {
        $this->redelivered = (bool) $redelivered;
    }

    public function isRedelivered()
    {
        return $this->redelivered;
    }

    public function setCorrelationId($correlationId)
    {
        $this->setHeader('correlation_id', $correlationId);
    }

    public function getCorrelationId()
    {
        return $this->getHeader('correlation_id');
    }

    public function setMessageId($messageId)
    {
        $this->setHeader('message_id', $messageId);
    }

    public function getMessageId()
    {
        return $this->getHeader('message_id');
    }

    public function getTimestamp()
    {
        $value = $this->getHeader('timestamp');

        return $value === null ? null : (int) $value;
    }

    public function setTimestamp($timestamp)
    {
        $this->setHeader('timestamp', $timestamp);
    }

    public function setReplyTo($replyTo)
    {
        $this->setHeader('reply_to', $replyTo);
    }

    public function getReplyTo()
    {
        return $this->getHeader('reply_to');
    }

    public function getDeliveryTag()
    {
        return $this->deliveryTag;
    }

    public function setDeliveryTag($deliveryTag)
    {
        $this->deliveryTag = $deliveryTag;
    }

    public function getConsumerTag()
    {
        return $this->consumerTag;
    }

    public function setConsumerTag($consumerTag)
    {
        $this->consumerTag = $consumerTag;
    }

    public function clearFlags()
    {
        $this->flags = AMQP_NOPARAM;
    }

    public function addFlag($flag)
    {
        $this->flags = $this->flags | $flag;
    }

    public function getFlags()
    {
        return $this->flags;
    }
}
