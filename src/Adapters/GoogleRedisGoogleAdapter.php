<?php

namespace Teamones\Breaker\Adapters;

use Illuminate\Redis\Connections\Connection;

class GoogleRedisGoogleAdapter implements GoogleAdapterInterface
{

    /**
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected Connection $redis;

    /**
     * @var string
     */
    protected string $redisNamespace;

    /**
     * @var array
     */
    protected array $cachedService = [];

    // 流量计数器
    protected array $counter = [
        'accepts_total' => 0, // accepts
        'requests_total' => 0 // total request
    ];

    /**
     * Set settings for start circuit service
     * RedisAdapter constructor.
     * @param Connection $redis
     * @param string $redisNamespace
     */
    public function __construct(Connection $redis, string $redisNamespace)
    {
        $this->redis = $redis;
        $this->redisNamespace = $redisNamespace;
    }

    /**
     * 记录连接数
     * @param string $service
     */
    public function collectionAdd(string $service): void
    {

    }

    /**
     * 在设置的时间范围内重置连接数
     * @param string $service
     */
    public function collectionReset(string $service): void
    {
        $this->counter['accepts_total'] = 0;
        $this->counter['requests_total'] = 0;
    }

    /**
     * 把服务设置为失败
     * @param string $service
     */
    public function setFailure(string $service): void
    {

    }

    /**
     * 把服务设置为成功
     * @param string $service
     */
    public function setSuccess(string $service): void
    {

    }
}
