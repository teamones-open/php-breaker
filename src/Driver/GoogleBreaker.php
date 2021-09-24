<?php

namespace Teamones\Breaker\Driver;

use Teamones\Breaker\Adapters\GoogleAdapterInterface;

class GoogleBreaker implements BreakerInterface
{

    /**
     * @var GoogleAdapterInterface
     */
    protected static GoogleAdapterInterface $adapter;

    /**
     * @var array
     */
    protected static array $servicesSettings;

    /**
     * @var array
     */
    protected static array $globalSettings;

    /**
     * 250ms for bucket duration
     * @var array
     */
    protected static array $defaultSettings = [
        'timeWindow' => 10, // 10s
        'buckets' => 40,
        'k' => 1.5
    ];

    /**
     * @param GoogleAdapterInterface $adapter
     */
    public static function setAdapter(GoogleAdapterInterface $adapter): void
    {
        self::$adapter = $adapter;
    }

    /**
     * @return GoogleAdapterInterface
     */
    public static function getAdapter(): GoogleAdapterInterface
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