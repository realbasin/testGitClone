<?php

namespace XSQueue\AmqpExt;

use XSQueue\ConnectionFactory;

class AmqpConnectionFactory implements ConnectionFactory
{
    private $config;
    private $connection;

    /**
     * 配置可以是array，DSN字符串或者Null
	 * 如果传入Null则使用本机默认设置
     *
     * @param array|string $config
     */
    public function __construct($config = 'amqp://')
    {
        if (empty($config) || 'amqp://' === $config) {
            $config = array();
        } elseif (is_string($config)) {
            $config = $this->parseDsn($config);
        } elseif (is_array($config)) {
        } else {
            throw new \LogicException('The config must be either an array of options, a DSN string or null');
        }

        $this->config = array_replace($this->defaultConfig(), $config);

        $supportedMethods = array('basic_get', 'basic_consume');
        if (false == in_array($this->config['receive_method'], $supportedMethods, true)) {
            throw new \LogicException(sprintf(
                'Invalid "receive_method" option value "%s". It could be only "%s"',
                $this->config['receive_method'],
                implode('", "', $supportedMethods)
            ));
        }

        if ('basic_consume' == $this->config['receive_method']) {
            if (false == (version_compare(phpversion('amqp'), '1.9.1', '>=') || phpversion('amqp') == '1.9.1-dev')) {
                throw new \LogicException('The "basic_consume" method does not work on amqp extension prior 1.9.1 version.');
            }
        }
    }

    /**
     *
     * @return AmqpContext
     */
    public function createContext()
    {
        if ($this->config['lazy']) {
            return new AmqpContext(function () {
                return $this->createExtContext($this->establishConnection());
            }, $this->config['receive_method']);
        }

        return new AmqpContext($this->createExtContext($this->establishConnection()), $this->config['receive_method']);
    }

    /**
     * @param \AMQPConnection $extConnection
     *
     * @return \AMQPChannel
     */
    private function createExtContext(\AMQPConnection $extConnection)
    {
        $channel = new \AMQPChannel($extConnection);
        if (false == empty($this->config['pre_fetch_count'])) {
            $channel->setPrefetchCount((int) $this->config['pre_fetch_count']);
        }

        if (false == empty($this->config['pre_fetch_size'])) {
            $channel->setPrefetchSize((int) $this->config['pre_fetch_size']);
        }

        return $channel;
    }

    /**
     * @return \AMQPConnection
     */
    private function establishConnection()
    {
        if (false == $this->connection) {
            $config = $this->config;
            $config['login'] = $this->config['user'];
            $config['password'] = $this->config['pass'];

            $this->connection = new \AMQPConnection($config);

            $this->config['persisted'] ? $this->connection->pconnect() : $this->connection->connect();
        }
        if (false == $this->connection->isConnected()) {
            $this->config['persisted'] ? $this->connection->preconnect() : $this->connection->reconnect();
        }

        return $this->connection;
    }

    /**
     * @param string $dsn
     *
     * @return array
     */
    private function parseDsn($dsn)
    {
        $dsnConfig = parse_url($dsn);
        if (false === $dsnConfig) {
            throw new \LogicException(sprintf('Failed to parse DSN "%s"', $dsn));
        }

        $dsnConfig = array_replace(array(
            'scheme' => null,
            'host' => null,
            'port' => null,
            'user' => null,
            'pass' => null,
            'path' => null,
            'query' => null,
        ), $dsnConfig);

        if ('amqp' !== $dsnConfig['scheme']) {
            throw new \LogicException(sprintf('The given DSN scheme "%s" is not supported. Could be "amqp" only.',$dsnConfig['scheme']));
        }

        if ($dsnConfig['query']) {
            $query = array();
            parse_str($dsnConfig['query'], $query);

            $dsnConfig = array_replace($query, $dsnConfig);
        }

        $dsnConfig['vhost'] = ltrim($dsnConfig['path'], '/');

        unset($dsnConfig['scheme'], $dsnConfig['query'], $dsnConfig['fragment'], $dsnConfig['path']);

        $config = array_replace($this->defaultConfig(), $dsnConfig);
        $config = array_map(function ($value) {
            return urldecode($value);
        }, $config);

        return $config;
    }

    /**
     * @return array
     */
    private function defaultConfig()
    {
        return array(
            'host' => 'localhost',
            'port' => 5672,
            'vhost' => '/',
            'user' => 'guest',
            'pass' => 'guest',
            'read_timeout' => null,
            'write_timeout' => null,
            'connect_timeout' => null,
            'persisted' => false,
            'lazy' => true,
            'pre_fetch_count' => null,
            'pre_fetch_size' => null,
            'receive_method' => 'basic_get',
        );
    }
}
