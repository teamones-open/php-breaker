<?php

namespace WebmanMicro\PhpBreaker\Driver;

use WebmanMicro\PhpBreaker\RollingWindow\Collection\RollingWindow;
use WebmanMicro\PhpBreaker\RollingWindow\Collection\Bucket;

class GoogleBreaker
{
    /**
     * @var RollingWindow[]
     */
    protected static array $services;

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
        'interval' => 250, // 250ms
        'buckets' => 40,
        'k' => 1.5, // 倍值
        'protection' => 5,
        'ignore_current' => false
    ];

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
     * 初始化服务
     * @param array $services
     */
    public static function instanceServices(array $services)
    {
        foreach ($services as $service) {
            if (empty(self::$services[$service])) {
                self::$services[$service] = new RollingWindow(self::$globalSettings['buckets'], self::$globalSettings['interval'], self::$globalSettings['ignore_current']);
            }
        }
    }

    /**
     *
     * @param string $service
     * @return bool
     */
    public static function isAvailable(string $service): bool
    {
        list($accepts, $total) = self::history($service);
        $weightedAccepts = self::$globalSettings['k'] * floatval($accepts);
        // https://landing.google.com/sre/sre-book/chapters/handling-overload/#eq2101
        $dropRatio = max(0, (floatval($total - self::$globalSettings['protection']) - $weightedAccepts) / floatval($total + 1));

        if ($dropRatio <= 0) {
            return true;
        }

        // When the dropratio is close to 1, the request probability will be blocked by the fuse
        if ($dropRatio > self::$services[$service]->proba) {
            return false;
        }

        return true;
    }

    /**
     * @param string $service
     */
    public static function failure(string $service)
    {
        self::$services[$service]->add(0);
    }

    /**
     * @param string $service
     */
    public static function success(string $service)
    {
        self::$services[$service]->add(1);
    }

    /**
     * @param string $service
     * @return array
     */
    private static function history(string $service)
    {
        $accepts = 0;
        $total = 0;
        self::$services[$service]->reduce(function (Bucket $bucket) use (&$accepts, &$total) {
            $accepts += intval($bucket->bucket['sum']);
            $total += $bucket->bucket['count'];
        });

        return [$accepts, $total];
    }
}
