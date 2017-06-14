<?php

namespace XSQueue;

/**
 * 消费者接口
 * 客户端使用消费者接收消息。
 *
 */
interface Consumer
{
    /**
     * 获取与此队列接收器相关联的队列。
     *
     * @return Queue
     */
    public function getQueue();

    /**
     * 接收下一条消息
     *
     * @param int $timeout 过期时间 (毫秒)
     *
     * @return Message|null
     */
    public function receive($timeout = 0);

    /**
     * 立即接收下一条消息
     *
     * @return Message|null
     */
    public function receiveNoWait();

    /**
     * 成功处理消息
     *
     * @param Message $message
     */
    public function acknowledge(Message $message);

    /**
     * 消息被拒绝
     *
     * @param Message $message
     * @param bool       $requeue
     */
    public function reject(Message $message, $requeue = false);
}
