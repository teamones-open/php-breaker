<?php

namespace Teamones\Breaker\RollingWindow\Time;

class Timex
{
    /**
     * 当前纳秒时间
     * @return array|false|float|int|int[]
     */
    public static function now()
    {
        return hrtime(true);
    }

    /**
     *
     * @return array|false|float|int|int[]
     */
    public static function since($time)
    {
        return hrtime(true) - $time;
    }
}