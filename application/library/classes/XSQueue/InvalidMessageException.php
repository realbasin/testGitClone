<?php

namespace XSQueue;

class InvalidMessageException extends Exception
{
    public static function assertMessageInstanceOf(Message $message, $class)
    {
        if (!$message instanceof $class) {
            throw new static(sprintf(
                'The message must be an instance of %s but it is %s.',
                $class,
                get_class($message)
            ));
        }
    }
}
