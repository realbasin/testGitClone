<?php
namespace XSQueue;

/**
 * 队列操作接口
 * 
 */
interface Context
{
    /**
     * 创建消息
	 * 
	 * @param string $body
     * @param array  $properties
     * @param array  $headers
     *
     * @return Message
     */
    public function createMessage($body = '', array $properties = [], array $headers = []);

    /**
     * 创建主题
	 * 
	 * @param string $topicName
     *
     * @return Topic
     */
    public function createTopic($topicName);

    /**
     * 创建队列
	 * 
	 * @param string $queueName
     *
     * @return Queue
     */
    public function createQueue($queueName);

    /**
     * 创建临时队列
     * 临时队列仅本次连接可用
     * 连接关闭时临时队列会被自动删除
     *
     * @return Queue
     */
    public function createTemporaryQueue();

    /**
     * 创建生产者
	 * 
	 * @return Producer
     */
    public function createProducer();

    /**
     * 创建消费者
	 * 
	 * @param Destination $destination
     *
     * @return Consumer
     */
    public function createConsumer(Destination $destination);

	/**
	 * 关闭
	 */
    public function close();
}
