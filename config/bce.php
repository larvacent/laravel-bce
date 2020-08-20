<?php

return [
    /**
     * 公共的
     */
    'access_id' => env('BCE_ACCESS_ID'),
    'access_key' => env('BCE_ACCESS_KEY'),

    /**
     * 特定服务的可单独定义
     */
    'services' => [
        'cdn' => [
            'driver' => 'cdn',
            'access_id' => env('BCE_ACCESS_ID'),
            'access_key' => env('BCE_ACCESS_KEY'),
        ],

    ],

];