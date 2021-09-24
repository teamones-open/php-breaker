<?php

namespace Teamones\Breaker\Driver;

use Teamones\Breaker\Adapters\GoogleAdapterInterface;

interface BreakerInterface
{
    public static function setAdapter(GoogleAdapterInterface $adapter): void;

    public static function getAdapter(): GoogleAdapterInterface;

    public static function setGlobalSettings(array $settings): void;

    public static function getGlobalSettings(): array;

    public static function setServiceSettings(string $service, array $settings): void;

    public static function getServiceSetting(string $service, string $setting);

    public static function isAvailable(string $service): bool;

    public static function failure(string $service): bool;

    public static function success(string $service);
}