<?php

namespace XSQueue;

/**
 * 队列接口
 */
interface Queue extends Destination
{
    /**
     * 获取队列的名称
     *
     * @return string
     */
    public function getQueueName();
}
