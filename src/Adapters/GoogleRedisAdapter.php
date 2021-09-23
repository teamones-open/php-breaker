<?php

namespace Teamones\Breaker\Adapters;

use Redis;

class GoogleRedisAdapter implements AdapterInterface
{

    protected array $bucket = [
        'accepts_total' => 0, // accepts
        'requests_total' => 0 // total request
    ];

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
        $this->bucket['accepts_total'] = 0;
        $this->bucket['requests_total'] = 0;
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
