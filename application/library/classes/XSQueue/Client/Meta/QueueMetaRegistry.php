<?php

namespace XSQueue\Client\Meta;

use XSQueue\Client\Config;

class QueueMetaRegistry
{
    private $meta;
    private $config;

    /**
     * $meta = array(
     *   'aQueueName' => array(
     *     'transportName' => 'aTransportQueueName',
     *     'processors' => array('aFooProcessorName', 'aBarProcessorName'),
     *   )
     * )
	 * 
     */
    public function __construct(Config $config, array $meta)
    {
        $this->config = $config;
        $this->meta = $meta;
    }

    public function add($queueName, $transportName = null)
    {
        $this->meta[$queueName] = array(
            'transportName' => $transportName,
            'processors' => array(),
        );
    }

    public function addProcessor($queueName, $processorName)
    {
        if (false == array_key_exists($queueName, $this->meta)) {
            $this->add($queueName);
        }

        $this->meta[$queueName]['processors'][] = $processorName;
    }

    public function getQueueMeta($queueName)
    {
        if (false == array_key_exists($queueName, $this->meta)) {
            throw new \InvalidArgumentException(sprintf(
                'The queue meta not found. Requested name `%s`',
                $queueName
            ));
        }

        $transportName = $this->config->createTransportQueueName($queueName);

        $meta = array_replace(array(
            'processors' => array(),
            'transportName' => $transportName,
        ), array_filter($this->meta[$queueName]));

        return new QueueMeta($queueName, $meta['transportName'], $meta['processors']);
    }

    /**
     * @return \Generator|QueueMeta[]
     */
    public function getQueuesMeta()
    {
        foreach (array_keys($this->meta) as $queueName) {
            yield $this->getQueueMeta($queueName);
        }
    }
}
