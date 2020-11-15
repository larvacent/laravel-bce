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
        'sms' => [
            'driver' => 'sms',
            'access_id' => env('BCE_ACCESS_ID'),
            'access_key' => env('BCE_ACCESS_KEY'),
        ],
        'aip' => [//AI自然语言处理(旧)
            'driver' => 'aip',
            'app_id' => env('AI_APP_ID'),
            'app_key' => env('AI_APP_KEY'),
            'secret_key' => env('AI_SECRET_KEY'),
        ],

        'nlp' => [//NLP 自然语言处理
            'driver' => 'aip',
            'app_id' => env('NLP_APP_ID'),
            'app_key' => env('NLP_APP_KEY'),
            'secret_key' => env('NLP_SECRET_KEY'),
        ],
    ],

];