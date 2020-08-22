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
            'access_id' => env('BCE_ACCESS_ID'),
            'access_key' => env('BCE_ACCESS_KEY'),
        ],
        'aip' => [//AIP自然语言处理
            'app_id' => env('AIP_APP_ID'),
            'app_key' => env('AIP_APP_KEY'),
            'secret_key' => env('AIP_SECRET_KEY'),
        ],
    ],

];