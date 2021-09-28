<?php

namespace Teamones\Breaker\RollingWindow\Collection;

class Window
{

    /**
     * 窗口块
     * @var Bucket[]
     */
    public array $windowBuckets = [];

    // 窗口大小
    public int $windowSize = 0;

    /**
     * 增加
     * @param int $offset
     * @param int $v
     */
    public function add(int $offset, int $v): void
    {
        $this->windowBuckets[$offset % $this->windowSize]->add($v);
    }

    /**
     * 减少
     * @param int $start
     * @param int $count
     * @param callable $callback
     */
    public function reduce(int $start, int $count, callable $callback): void
    {
        for ($i = 0; $i < $count; $i++) {
            $callback($this->windowBuckets[($start + $i) % $this->windowSize]);
        }
    }

    /**
     * 重置指定的窗口块
     * @param int $offset
     */
    public function resetBucket(int $offset): void
    {
        $this->windowBuckets[$offset % $this->windowSize]->reset();
    }

    /**
     * 初始化窗口
     * @param int $size
     */
    public function __construct(int $size)
    {
        $this->windowSize = $size;
        for ($i = 0; $i < $size; $i++) {
            $this->windowBuckets[$i] = new Bucket();
        }
    }

    /**
     * 销毁创建对象
     */
    public function destroy(): void
    {
        foreach ($this->windowBuckets as $bucket) {
            unset($bucket);
        };

        $this->windowBuckets = [];
    }
}