<?php

namespace Teamones\Breaker;

use Teamones\Breaker\Adapters\CircuitRedisAdapter;
use Teamones\Breaker\Driver\CircuitBreaker;
use Teamones\Breaker\Adapters\GoogleRedisGoogleAdapter;
use Teamones\Breaker\Driver\GoogleBreaker;
use Teamones\Breaker\Cache\Redis;

class BreakerFactory
{
    /**
     * @var CircuitBreaker|GoogleBreaker|null
     */
    protected static $_instance = null;

    /**
     * 初始化
     */
    protected static function instance()
    {

        if (!isset(static::$_instance)) {
            // redis连接句柄
            $redis = Redis::connection();

            // 设置当前熔断器命名空间，读取当前服务名称加上随机数
            $config = config('breaker', []);
            if (!isset($config)) {
                throw new \RuntimeException("Breaker config not found");
            }

            // redis namespace
            $redisNamespace = $config['discovery']['server_name'] . "_" . $config['discovery']['server_uuid'];


            // 判断使用哪种熔断器模型
            $breakerType =  !empty($config['type']) ? $config['type'] : 'circuit';
            switch ($breakerType){
                default:
                case 'circuit':
                    $adapter = new CircuitRedisAdapter($redis, $redisNamespace);

                    // Set redis adapter for CB
                    CircuitBreaker::setAdapter($adapter);

                    // Configure settings for CB
                    CircuitBreaker::setGlobalSettings([
                        'timeWindow' => 20, // 开路时间（秒）
                        'failureRateThreshold' => 15, // 开路故障率
                        'intervalToHalfOpen' => 10, // 半开时间（秒）重试
                    ]);
                    static::$_instance = CircuitBreaker::class;
                    break;
                case 'google':
                    // TODO 还在开发中
                    $adapter = new GoogleRedisGoogleAdapter($redis, $redisNamespace);

                    // Set redis adapter for CB
                    GoogleBreaker::setAdapter($adapter);

                    // Configure settings for CB
                    GoogleBreaker::setGlobalSettings([
                        'timeWindow' => 10, // 窗口时间（s）
                        'buckets' => 40, // 桶大小
                        'k' => 1.5 // 倍值
                    ]);
                    static::$_instance = CircuitBreaker::class;
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