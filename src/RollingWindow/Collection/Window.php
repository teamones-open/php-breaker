<?php

namespace WebmanMicro\PhpBreaker\RollingWindow\Collection;

class Window
{

    /**
     * @var Bucket[]
     */
    public array $windowBuckets = [];

    // windows size
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
     * reduce
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
     * reset bucket
     * @param int $offset
     */
    public function resetBucket(int $offset): void
    {
        $this->windowBuckets[$offset % $this->windowSize]->reset();
    }

    /**
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
     * destroy
     */
    public function destroy(): void
    {
        foreach ($this->windowBuckets as $bucket) {
            unset($bucket);
        };

        $this->windowBuckets = [];
    }
}
