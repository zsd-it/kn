<?php
return [
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache'    => [
            'class' => 'yii\caching\FileCache',
        ],
        'response' => [
            'on beforeSend' => function ($event) {
                /* @var $response \yii\web\Response */
                $response         = $event->sender;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->getHeaders()->set('Access-Control-Allow-Origin', '*');
                $response->getHeaders()->set('Access-Control-Allow-Headers', 'token', 'content-type');
                if ($response->data !== null) {
                    $response->data = [
                        'message' => isset($response->data['message']) ? $response->data['message'] : "",
                        'code'    => $response->statusCode == 200 ? 0 : $response->statusCode,
                        'data'    => $response->statusCode == 200 ? $response->data : (YII_DEBUG ? $response->data : null),
                    ];
                }
                $response->statusCode = 200;
            },
        ],
    ],
];
