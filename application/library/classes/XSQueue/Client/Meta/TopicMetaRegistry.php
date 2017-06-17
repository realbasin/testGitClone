<?php

namespace XSQueue\Client\Meta;

class TopicMetaRegistry
{
    protected $meta;

    /**
     * $meta = array(
     *   'aTopicName' => array(
     *     'description' => 'A desc',
     *     'processors' => array('aProcessorNameFoo', 'aProcessorNameBar),
     *   ),
     * ).
     *
     */
    public function __construct(array $meta)
    {
        $this->meta = $meta;
    }

    public function add($topicName, $description = null)
    {
        $this->meta[$topicName] = array(
            'description' => $description,
            'processors' => array(),
        );
    }

    public function addProcessor($topicName, $processorName)
    {
        if (false == array_key_exists($topicName, $this->meta)) {
            $this->add($topicName);
        }

        $this->meta[$topicName]['processors'][] = $processorName;
    }

    public function getTopicMeta($topicName)
    {
        if (false == array_key_exists($topicName, $this->meta)) {
            throw new \InvalidArgumentException(sprintf('The topic meta not found. Requested name `%s`', $topicName));
        }

        $topic = array_replace(array(
            'description' => '',
            'processors' => array(),
        ), $this->meta[$topicName]);

        return new TopicMeta($topicName, $topic['description'], $topic['processors']);
    }

    public function getTopicsMeta()
    {
        foreach (array_keys($this->meta) as $topicName) {
            yield $this->getTopicMeta($topicName);
        }
    }
}
