<?php

namespace XSQueue;

/**
 * 消息处理接口
 */
interface Processor
{
    /**
     * 在功处理消息通知消息可以从队列中删除。
     */
    const ACK = 'enqueue.ack';

    /**
     * 当消息无效或无法处理时，通知消息从队列中删除
     */
    const REJECT = 'enqueue.reject';

    /**
     * 消息无效或者无法立即处理，通知重试
     * 删除原始消息并发送消息的副本到队列
     */
    const REQUEUE = 'enqueue.requeue';

    /**
     * 返回self::ACK, self::REJECT, self::REQUEUE或者一个对象
     *
     * 如果返回对象
     * 对象必须继承 __toString 方法，并返回上述常量
     *
     * @param Message $message
     * @param Context $context
     *
     * @return string|object
     */
    public function process(Message $message, Context $context);
}
