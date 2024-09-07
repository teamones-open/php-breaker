<?php

$circuit = [
    'enable' => true,
    // requires
    'type' => 'circuit', // 熔断器类型
    'server' => [
        'name' => getenv("ETCD_SERVER_NAME", '') // 当前服务名称
    ],
    // optional
    'time_window' => 20, // 开路窗口时间 默认值 20s
    'failure_rate_threshold' => 15, // 故障率阈值 默认值 15%
    'interval_to_half_open' => 10 // 半开路窗口时间 默认值 10s
];

$google = [
    'enable' => true,
    // requires
    'type' => 'google', // 熔断器类型
    'services' => explode(',', getenv("ETCD_DISCOVERY_SERVER", '')), // 访问服务列表
    // optional
    'time_window' => 10, // 滑动窗口时间 默认值 10s
    'buckets' => 40, // 小的窗口数量  默认 40 （默认每个小窗口的时间跨度为250ms）
    'k' => 1.5 // 倍值 默认1.5
];

return $google;
