<?php

namespace XSQueue\Client\Meta;

class TopicMeta
{
    private $name;
    private $description;
    private $processors;

    /**
     * @param string   $name
     * @param string   $description
     * @param array $processors
     */
    public function __construct($name, $description = '', array $processors = array())
    {
        $this->name = $name;
        $this->description = $description;
        $this->processors = $processors;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getProcessors()
    {
        return $this->processors;
    }
}
