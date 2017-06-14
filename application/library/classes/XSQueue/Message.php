<?php

namespace XSQueue;

/**
 * 消息接口
 */
interface Message
{
    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @param array $properties
     */
    public function setProperties(array $properties);

    /**
     * @return array [name => value, ...]
     */
    public function getProperties();

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setProperty($name, $value);

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getProperty($name, $default = null);

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * @return array [name => value, ...]
     */
    public function getHeaders();

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setHeader($name, $value);

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getHeader($name, $default = null);

    /**
     * @param bool $redelivered
     */
    public function setRedelivered($redelivered);

    /**
     * @return bool
     */
    public function isRedelivered();

    /**
     *
     * @param string $correlationId
     *
     * @throws Exception
     */
    public function setCorrelationId($correlationId);

    /**
     *
     * @throws Exception
     *
     * @return string
     */
    public function getCorrelationId();

    /**
     *
     * @param string $messageId
     *
     * @throws Exception
     */
    public function setMessageId($messageId);

    /**
     *
     * @throws Exception 
     *
     * @return string
     */
    public function getMessageId();

    /**
     * @return int
     */
    public function getTimestamp();

    /**
     * @param int $timestamp
     *
     * @throws Exception
     */
    public function setTimestamp($timestamp);

    /**
     * @param string|null $replyTo
     */
    public function setReplyTo($replyTo);

    /**
     *
     * @return string|null
     */
    public function getReplyTo();
}
