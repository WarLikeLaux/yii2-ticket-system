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

    /**
     * @OA\Get(
     *     path="/doc/hello-world",
     *     summary="Получение данных",
     *     description="Более подробное описание метода",
     *     tags={"API"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ресурс не найден"
     *     )
     * )
     */
    public function actionHelloWorld()
    {
        return 'Hello World';
    }
}
