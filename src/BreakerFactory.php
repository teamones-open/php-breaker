<?php

namespace WebmanMicro\PhpBreaker;

use WebmanMicro\PhpBreaker\Cache\Redis;
use WebmanMicro\PhpBreaker\Adapters\CircuitRedisAdapter;
use WebmanMicro\PhpBreaker\Driver\CircuitBreaker;
use WebmanMicro\PhpBreaker\Driver\GoogleBreaker;
use Workbunny\WebmanSharedCache\Cache;

/**
 * Class Redis
 * @package support
 *
 *  * Strings methods
 * @method static void setGlobalSettings(array $settings)
 * @method static array getGlobalSettings()
 * @method static void setServiceSettings(string $service, array $settings)
 * @method static mixed getServiceSetting(string $service, string $setting)
 * @method static bool isAvailable(string $service)
 * @method static void failure(string $service)
 * @method static void success(string $service)
 */
class BreakerFactory
{
    // 1s = 1000ms
    const SECONDS = 1000;

    /**
     * @var CircuitBreaker|GoogleBreaker|null
     */
    protected static $_instance = null;

    /**
     * breaker config
     * @var array
     */
    protected static array $_config = [];

    /**
     * Netflix hysrtix breaker default windows config
     * @var int[]
     */
    protected static array $_circuitWindowConfigs = [
        'time_window' => 20, // Open circuit time (s)
        'failure_rate_threshold' => 15, // Open circuit failure rate
        'interval_to_half_open' => 10, // Half open time (seconds) retry
    ];

    /**
     * Google SRE breaker default windows config
     * @var int[]
     */
    protected static array $_googleWindowConfigs = [
        'time_window' => 10, // Window time (s)
        'buckets' => 15, // Window buckets
        'k' => 4, // Multiple value
    ];

    /**
     * 设置配置
     */
    public static function setConfig($config)
    {
        if (empty($config['type']) && !in_array($config['type'], ['google', 'circuit'])) {
            throw new \RuntimeException("Breaker config type error.");
        }

        // Set type param
        self::$_config['type'] = $config['type'];

        // Check whether the config parameters are complete
        if ($config['type'] === 'circuit') {
            /**
             * Netflix hysrtix breaker default config
             * [
             *      // requires
             *      'type' => 'circuit',
             *      'server' => [
             *          'name' => 'test',
             *          'uuid' => '00a5ca80-4fe9-11eb-875c-c3e67d5eac81'
             *      ],
             *      // optional
             *     'time_window' => 20, // default value 20s
             *     'failure_rate_threshold' => 15, // default value  15%
             *     'interval_to_half_open' => 10 // default value  10s
             * ]
             */

            // Server parameters is requires
            if (empty($config['server']['name'])) {
                throw new \RuntimeException("Circuit breaker config server param is requires.");
            }

            // Set server param
            self::$_config['server'] = $config['server'];

            // Set windows config
            foreach (self::$_circuitWindowConfigs as $key => $value) {
                self::$_config[$key] = $config[$key] ?? $value;
            }
        } else {
            /**
             * Google SRE breaker default config
             * [
             *      // requires
             *      'type' => 'google',
             *      'services' => ['im', 'log', 'order'],
             *      // optional
             *     'time_window' => 10, // default value 10s
             *     'buckets' => 40, // default value  40
             *     'k' => 1.5 // default value  10s
             * ]
             */

            // Server parameters is requires
            if (empty($config['services'])) {
                throw new \RuntimeException("Google breaker config services param is requires.");
            }

            // Set servers param
            self::$_config['services'] = $config['services'];

            // Set windows config
            foreach (self::$_googleWindowConfigs as $key => $value) {
                self::$_config[$key] = $config[$key] ?? $value;
            }
        }
    }

    /**
     * initialize
     * @return CircuitBreaker|GoogleBreaker|null
     */
    protected static function instance()
    {
        if (!isset(static::$_instance)) {

            if (empty(self::$_config)) {
                // Read independent config
                $config = config('plugin.webman-micro.php-breaker.app', []);

                if (empty($config)) {
                    throw new \RuntimeException("Breaker config not found.");
                }

                self::setConfig($config);
            }

            // Determine which breaker to use
            switch (self::$_config['type']) {
                case 'circuit':
                    // Netflix Hysrtix Breaker

                    // Set the current breaker namespace and read the current service name plus a random number
                    $redisNamespace = self::$_config['server']['name'] . "_" . Cache::Get('service_uuid');

                    // Init redis adapter
                    $adapter = new CircuitRedisAdapter(Redis::connection(), $redisNamespace);

                    // Set redis adapter for CB
                    CircuitBreaker::setAdapter($adapter);

                    // Configure settings for CB
                    CircuitBreaker::setGlobalSettings([
                        'timeWindow' => self::$_config['time_window'],
                        'failureRateThreshold' => self::$_config['failure_rate_threshold'],
                        'intervalToHalfOpen' => self::$_config['interval_to_half_open'],
                    ]);
                    static::$_instance = CircuitBreaker::class;
                    break;
                case 'google':
                    // Google SRE Breaker

                    // Configure settings for GB
                    GoogleBreaker::setGlobalSettings([
                        'interval' => intval((self::$_config['time_window'] * self::SECONDS) / self::$_config['buckets']),
                        'buckets' => intval(self::$_config['buckets']),
                        'k' => self::$_config['k'],
                    ]);

                    // Init services
                    GoogleBreaker::instanceServices(self::$_config['services']);

                    static::$_instance = GoogleBreaker::class;
                    break;
            }
        }

        return static::$_instance;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()::{$name}(... $arguments);
    }
}
