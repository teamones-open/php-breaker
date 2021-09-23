<?php

namespace Teamones\Breaker\Driver;

use Teamones\Breaker\Adapters\AdapterInterface;

class GoogleBreaker implements BreakerInterface
{

    /**
     * @var AdapterInterface
     */
    protected static AdapterInterface $adapter;

    /**
     * @param AdapterInterface $adapter
     */
    public static function setAdapter(AdapterInterface $adapter): void
    {
        self::$adapter = $adapter;
    }

    /**
     * @return AdapterInterface
     */
    public static function getAdapter(): AdapterInterface
    {
        return self::$adapter;
    }

    /**
     * @param array $settings
     */
    public static function setGlobalSettings(array $settings): void
    {

    }

    /**
     * @return array
     */
    public static function getGlobalSettings(): array
    {
        return [];
    }

    /**
     * @param string $service
     * @param array $settings
     */
    public static function setServiceSettings(string $service, array $settings): void
    {

    }

    /**
     * @param string $service
     * @param string $setting
     */
    public static function getServiceSetting(string $service, string $setting)
    {

    }

    /**
     * @param string $service
     * @return bool
     */
    public static function isAvailable(string $service): bool
    {
        return true;
    }

    /**
     * @param string $service
     * @return bool
     */
    public static function failure(string $service): bool
    {
        return true;
    }

    public static function success(string $service)
    {

    }
}