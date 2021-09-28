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
        'buckets' => 40, // 桶大小
        'k' => 1.5 // 倍值
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
        foreach (self::$defaultSettings as $defaultSettingKey => $defaultSettingValue) {
            self::$globalSettings[$defaultSettingKey] = (int)($settings[$defaultSettingKey] ?? $defaultSettingValue);
        }
    }

    /**
     * @return array
     */
    public static function getGlobalSettings(): array
    {
        return self::$globalSettings;
    }

    /**
     * Set custom settings for each service
     *
     * @param string $service
     * @param array $settings
     */
    public static function setServiceSettings(string $service, array $settings): void
    {
        foreach (self::$defaultSettings as $defaultSettingKey => $defaultSettingValue) {
            self::$servicesSettings[$service][$defaultSettingKey] =
                (int)($settings[$defaultSettingKey] ?? self::$globalSettings[$defaultSettingKey] ?? $defaultSettingValue);
        }
    }

    /**
     * Get setting for a service, if not set, get from default settings
     *
     * @param string $service
     * @param string $setting
     * @return mixed
     */
    public static function getServiceSetting(string $service, string $setting)
    {
        return self::$servicesSettings[$service][$setting]
            ?? self::$globalSettings[$setting]
            ?? self::$defaultSettings[$setting];
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