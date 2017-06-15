<?php

namespace XSQueue;

/**
 * 主题接口
 */
interface Topic extends Destination
{
    /**
     * 获取主题名称
     *
     * @return string
     */
    public function getTopicName();
}
