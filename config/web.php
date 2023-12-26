<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db-local.php';

$config = [
    'id' => 'basic',
    'name' => 'yii2-ticket-system',
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'doc',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'requests-ui' => [
            'class' => 'app\controllers\RequestsController',
            'viewPath' => '@app/views/requests',
        ],
        'requests' => [
            'class' => 'app\controllers\RequestsApiController',
        ]
    ],
    'on afterRequest' => function ($event) {
        $response = Yii::$app->response;
        $request = Yii::$app->request;
        $statusCode = $response->statusCode;
        $method = $request->method;
        $url = $request->getUrl();
        Yii::error("Request completed with status code {$statusCode}, method: {$method}, URL: {$url}");
    },
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'tinRcs7xsDwpIa56WVzCauW0FhPLaBBd',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '/',
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'pluralize' => false,
                    'controller' => [
                        'requests'
                    ],
                    'extraPatterns' => [
                        'GET index' => 'index',
                        'POST index' => 'create',
                        'PUT {id}' => 'update',
                    ],
                ],
            ],
            'normalizer' => [
                'class' => \yii\web\UrlNormalizer::class,
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
                'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_PERMANENT
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
