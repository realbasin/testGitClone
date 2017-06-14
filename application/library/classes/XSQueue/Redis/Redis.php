<?php

namespace XSQueue\Redis;

interface Redis
{
    public function lpush($key, $value);

    public function brpop($key, $timeout);

    public function rpop($key);

    public function connect();

    public function disconnect();

    public function del($key);
}
