<?php

namespace XSQueue\Client;

use XSQueue\Message;
use XSQueue\Queue;
use XSQueue\Client\LoggerInterface;

interface DriverInterface
{
    public function createTransportMessage(Message $message);

    public function createClientMessage(PsrMessage $message);

    public function sendToRouter(Message $message);

    public function sendToProcessor(Message $message);

    public function createQueue($queueName);

    public function setupBroker(LoggerInterface $logger = null);

    public function getConfig();
}
