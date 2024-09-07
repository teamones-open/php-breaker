<?php

namespace WebmanMicro\PhpBreaker\RollingWindow;

use WebmanMicro\PhpBreaker\RollingWindow\Collection\RollingWindow;

class Enforcer
{
    /**
     * @var RollingWindow|null
     */
    protected static ?RollingWindow $_manager = null;

    /**
     * @var array
     */
    protected static array $_config = [];


    /**
     * @param array $config
     */
    public static function setConfig(array $config = [])
    {
        self::$_config = $config;
    }

    /**
     * @return RollingWindow
     */
    public static function instance(): ?RollingWindow
    {
        if (!isset(static::$_manager)) {
            foreach (['size', 'interval'] as $key) {
                if (empty(self::$_config[$key])) {
                    throw new \RuntimeException("RollingWindow {$key} config not found");
                }
            }

            $ignoreCurrent = !empty(self::$_config['ignore_current']) ? self::$_config['ignore_current'] : false;
            static::$_manager = new RollingWindow(self::$_config['size'], self::$_config['interval'], $ignoreCurrent);
        }

        return static::$_manager;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}
