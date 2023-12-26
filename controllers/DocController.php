<?php

namespace app\controllers;

/**
 * @OA\Info(title="My First API", version="0.1")
 */
class DocController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/doc/api'], true),
            ],
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    \Yii::getAlias('@app/controllers'),
                    // Yii::getAlias('@api/models'),
                ],
                'api_key' => 'balbalbal',
            ],
        ];
    }
}
