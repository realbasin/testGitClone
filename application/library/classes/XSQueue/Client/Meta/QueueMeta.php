<?php

namespace XSQueue\Client\Meta;

class QueueMeta
{
    private $clientName;
    private $transportName;
    private $processors;

    public function __construct($clientName, $transportName, array $processors = array())
    {
        $this->clientName = $clientName;
        $this->transportName = $transportName;
        $this->processors = $processors;
    }

    public function getClientName()
    {
        return $this->clientName;
    }

    public function getTransportName()
    {
        return $this->transportName;
    }

    public function getProcessors()
    {
        return $this->processors;
    }
}
