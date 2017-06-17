<?php

namespace XSQueue\Client;

class Config
{
    const PARAMETER_TOPIC_NAME = 'xsqueue.topic_name';
    const PARAMETER_PROCESSOR_NAME = 'xsqueue.processor_name';
    const PARAMETER_PROCESSOR_QUEUE_NAME = 'xsqueue.processor_queue_name';
    const DEFAULT_PROCESSOR_QUEUE_NAME = 'default';
    const COMMAND_TOPIC = '__command__';

    private $prefix;
    private $appName;
    private $routerTopicName;
    private $routerQueueName;
    private $defaultProcessorQueueName;
    private $routerProcessorName;
    private $transportConfig;
	private $loggerDirPath;

    public function __construct($prefix, $appName, $routerTopicName, $routerQueueName, $defaultProcessorQueueName, $routerProcessorName, array $transportConfig = array())
    {
        $this->prefix = $prefix;
        $this->appName = $appName;
        $this->routerTopicName = $routerTopicName;
        $this->routerQueueName = $routerQueueName;
        $this->defaultProcessorQueueName = $defaultProcessorQueueName;
        $this->routerProcessorName = $routerProcessorName;
        $this->transportConfig = $transportConfig;
    }

    public function getRouterTopicName()
    {
        return $this->routerTopicName;
    }

    public function getRouterQueueName()
    {
        return $this->routerQueueName;
    }

    public function getDefaultProcessorQueueName()
    {
        return $this->defaultProcessorQueueName;
    }

    public function getRouterProcessorName()
    {
        return $this->routerProcessorName;
    }

    public function createTransportRouterTopicName($name)
    {
        return strtolower(implode('.', array_filter(array(trim($this->prefix), trim($name)))));
    }

    public function createTransportQueueName($name)
    {
        return strtolower(implode('.', array_filter(array(trim($this->prefix), trim($this->appName), trim($name)))));
    }

    public function getTransportOption($name, $default = null)
    {
        return array_key_exists($name, $this->transportConfig) ? $this->transportConfig[$name] : $default;
    }

    /**
     * @param string|null $prefix
     * @param string|null $appName
     * @param string|null $routerTopicName
     * @param string|null $routerQueueName
     * @param string|null $defaultProcessorQueueName
     * @param string|null $routerProcessorName
     * @param array       $transportConfig
     *
     * @return static
     */
    public static function create(
        $prefix = null,
        $appName = null,
        $routerTopicName = null,
        $routerQueueName = null,
        $defaultProcessorQueueName = null,
        $routerProcessorName = null,
        array $transportConfig = array()
    ) {
        return new static(
            $prefix ?: '',
            $appName ?: '',
            $routerTopicName ?: 'router',
            $routerQueueName ?: 'default',
            $defaultProcessorQueueName ?: 'default',
            $routerProcessorName ?: 'router',
            $transportConfig
        );
    }
}
