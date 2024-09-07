<?php

namespace WebmanMicro\PhpBreaker\RollingWindow\Time;

class Timex
{
    protected static $initTime = 0;

    public function __construct()
    {
        self::$initTime = strtotime("-1 year -1 month -1 day");
    }

    /**
     * @return float
     */
    public static function now(): float
    {
        $nanoTime = hrtime();
        $secondDate = (self::$initTime + $nanoTime[0]) . $nanoTime[1];
        return floatval($secondDate);
    }

    /**
     * @param $time
     * @return float
     */
    public static function since($time): float
    {
        $space = self::now() - $time;
        return $space / 1e+6;
    }
}
