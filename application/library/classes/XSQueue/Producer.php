<?php

namespace XSQueue;

/**
 * 生产者接口
 */
interface Producer
{
    /**
     * @param Destination $destination
     * @param Message     $message
     *
     * @throws Exception                   内部错误而无法发送消息
     * @throws InvalidDestinationException 客户端发送目标错误
     * @throws InvalidMessageException     无效的消息
     */
    public function send(Destination $destination, Message $message);
}
