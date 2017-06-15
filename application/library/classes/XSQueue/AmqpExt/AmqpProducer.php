<?php

namespace XSQueue\AmqpExt;

use XSQueue\InvalidDestinationException;
use XSQueue\InvalidMessageException;
use XSQueue\Destination;
use XSQueue\Message;
use XSQueue\Producer;
use XSQueue\Topic;

class AmqpProducer implements Producer
{
    private $amqpChannel;

    /**
     * @param \AMQPChannel $ampqChannel
     */
    public function __construct(\AMQPChannel $ampqChannel)
    {
        $this->amqpChannel = $ampqChannel;
    }

    /**
     *
     * @param AmqpTopic|AmqpQueue $destination
     * @param AmqpMessage         $message
     */
    public function send(Destination $destination, Message $message)
    {
        $destination instanceof Topic
            ? InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpTopic::class)
            : InvalidDestinationException::assertDestinationInstanceOf($destination, AmqpQueue::class)
        ;

        InvalidMessageException::assertMessageInstanceOf($message, AmqpMessage::class);

        $amqpAttributes = $message->getHeaders();

        if ($message->getProperties()) {
            $amqpAttributes['headers'] = $message->getProperties();
        }

        if ($destination instanceof AmqpTopic) {
            $amqpExchange = new \AMQPExchange($this->amqpChannel);
            $amqpExchange->setType($destination->getType());
            $amqpExchange->setName($destination->getTopicName());
            $amqpExchange->setFlags($destination->getFlags());
            $amqpExchange->setArguments($destination->getArguments());

            $amqpExchange->publish(
                $message->getBody(),
                $destination->getRoutingKey(),
                $message->getFlags(),
                $amqpAttributes
            );
        } else {
            $amqpExchange = new \AMQPExchange($this->amqpChannel);
            $amqpExchange->setType(AMQP_EX_TYPE_DIRECT);
            $amqpExchange->setName('');

            $amqpExchange->publish(
                $message->getBody(),
                $destination->getQueueName(),
                $message->getFlags(),
                $amqpAttributes
            );
        }
    }
}
