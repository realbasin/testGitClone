<?php

namespace XSQueue;

/**
 * 队列连接接口
 */
interface ConnectionFactory
{
	/**
	 * 创建队列对象
	 */
    public function createContext();
}
