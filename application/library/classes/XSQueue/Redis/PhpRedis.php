<?php

namespace XSQueue\Redis;

class PhpRedis implements Redis
{
    private $redis;
    private $config;

    public function __construct(array $config)
    {
        $this->config = array_replace(array(
            'host' => null,
            'port' => null,
            'timeout' => null,
            'reserved' => null,
            'retry_interval' => null,
            'persisted' => false,//是否持久连接
        ), $config);
    }

    public function lpush($key, $value)
    {
        if (false == $this->redis->lPush($key, $value)) {
            throw new ServerException($this->redis->getLastError());
        }
    }

    public function brpop($key, $timeout)
    {
        if ($result = $this->redis->brPop([$key], $timeout)) {
            return $result[1];
        }
    }

    public function rpop($key)
    {
        return $this->redis->rPop($key);
    }

    public function connect()
    {
        if (false == $this->redis) {
            $this->redis = new \Redis();

            if ($this->config['persisted']) {
                $this->redis->pconnect(
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['timeout']
                );
            } else {
                $this->redis->connect(
                    $this->config['host'],
                    $this->config['port'],
                    $this->config['timeout'],
                    $this->config['reserved'],
                    $this->config['retry_interval']
                );
            }
        }

        return $this->redis;
    }

    public function disconnect()
    {
        if ($this->redis) {
            $this->redis->close();
        }
    }

    public function del($key)
    {
        $this->redis->del($key);
    }
}
