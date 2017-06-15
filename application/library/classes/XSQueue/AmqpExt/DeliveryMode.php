<?php

namespace XSQueue\AmqpExt;

/**
 * 消息传递模式
 */
final class DeliveryMode
{
	//非持久化
    const NON_PERSISTENT = 1;
	//持久化
    const PERSISTENT = 2;
}
