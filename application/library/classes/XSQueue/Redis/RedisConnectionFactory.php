<?php
namespace XSQueue\Redis;

use XSQueue\ConnectionFactory;

class RedisConnectionFactory implements ConnectionFactory
{
    private $config;
    private $redis;

    public function __construct(array $config)
    {
        $this->config = array_replace(array(
            'host' => null,
            'port' => null,
            'timeout' => null,
            'reserved' => null,
            'retry_interval' => null,
            'vendor' => 'phpredis',//redis客户端有几种，默认只使用也只实现phpredis
            'persisted' => false,
            'lazy' => true,
        ), $config);

        $supportedVendors = array('phpredis');
        if (false == in_array($this->config['vendor'], $supportedVendors, true)) {
            throw new \LogicException(sprintf(
                'Unsupported redis vendor given. It must be either "%s". Got "%s"',
                implode('", "', $supportedVendors),
                $this->config['vendor']
            ));
        }
    }

    public function createContext()
    {
        if ($this->config['lazy']) {
            return new RedisContext(function () {
                return $this->createRedis();
            });
        }

        return new RedisContext($this->createRedis());
    }

    private function createRedis()
    {
        if (false == $this->redis) {
            if ('phpredis' == $this->config['vendor'] && false == $this->redis) {
                $this->redis = new PhpRedis($this->config);
            }

            $this->redis->connect();
        }

        return $this->redis;
    }
}
