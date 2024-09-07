<?php

namespace WebmanMicro\PhpBreaker\RollingWindow\Collection;

use WebmanMicro\PhpBreaker\RollingWindow\Time\Timex;

class RollingWindow
{
    /**
     * @var null|Window
     */
    public ?Window $windows = null;

    public int $size = 0;

    public int $interval = 0;

    public int $offset = 0;

    public int $lastTime = 0;

    public bool $ignoreCurrent = false;

    public float $proba = 1;

    /**
     * @param int $size
     * @param int $interval
     * @param bool $ignoreCurrent
     */
    public function __construct(int $size, int $interval, bool $ignoreCurrent = false)
    {
        if ($size < 1) {
            throw new \RuntimeException("size must be greater than 0");
        }

        $this->size = $size;

        if ($interval > 0) {
            $this->interval = $interval;
        }

        // Initialize window
        $this->windows = new Window($size);

        // Initialize current nano time
        $this->lastTime = Timex::now();

        $this->ignoreCurrent = $ignoreCurrent;

        $this->proba = $this->randFloat();
    }

    /**
     * @param int $min
     * @param int $max
     * @return float|int
     */
    private function randFloat(int $min = 0, int $max = 1)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * @param int $v
     */
    public function add(int $v)
    {
        $this->updateOffset();
        $this->windows->add($this->offset, $v);
    }

    /**
     * @param callable $callback
     */
    public function reduce(callable $callback)
    {
        $span = $this->span();

        if ($span === 0 && $this->ignoreCurrent) {
            $diff = $this->size - 1;
        } else {
            $diff = $this->size - $span;
        }

        if ($diff > 0) {
            $offset = ($this->offset + $span + 1) % $this->size;

            $this->windows->reduce($offset, $diff, $callback);
        }
    }

    /**
     * @return int
     */
    private function span(): int
    {
        $offset = intval(Timex::since($this->lastTime) / $this->interval);

        if ($offset >= 0 && $offset < $this->size) {
            return $offset;
        }

        return $this->size;
    }

    private function updateOffset()
    {
        $span = $this->span();
        if ($span <= 0) {
            return;
        }
        $offset = $this->offset;

        // reset expired buckets
        for ($i = 0; $i < $span; $i++) {
            $this->windows->resetBucket(($offset + $i + 1) % $this->size);
        }

        $this->offset = ($offset + $span) % $this->size;

        $now = Timex::Now();
        // align to interval time boundary
        $this->lastTime = $now - ($now - $this->lastTime) % $this->interval;
    }
}
