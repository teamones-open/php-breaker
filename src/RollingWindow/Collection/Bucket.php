<?php

namespace WebmanMicro\PhpBreaker\RollingWindow\Collection;

class Bucket
{
    public array $bucket = [
        'sum' => 0,
        'count' => 0
    ];

    /**
     * 增加
     * @param int $v
     */
    public function add(int $v): void
    {
        $this->bucket['sum'] += $v;
        $this->bucket['count']++;
    }

    /**
     * 重置
     */
    public function reset(): void
    {
        $this->bucket['sum'] = 0;
        $this->bucket['count'] = 0;
    }
}
