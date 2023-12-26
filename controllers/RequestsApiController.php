<?php

namespace app\controllers;

use Yii;
use app\models\Requests;
use yii\filters\Cors;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RequestsApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if ($this->enableCsrfValidation) {
            $this->enableCsrfValidation = false;
        }
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 3600,
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/requests/",
     *     summary="Получение заявок",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Фильтр по статусу заявки",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"Active", "Resolved"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="startDate",
     *         in="query",
     *         description="Начальная дата для фильтрации (в формате YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", pattern="^\d{4}-\d{2}-\d{2}$")
     *     ),
     *     @OA\Parameter(
     *         name="endDate",
     *         in="query",
     *         description="Конечная дата для фильтрации (в формате YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", pattern="^\d{4}-\d{2}-\d{2}$")
     *     ),
     *     @OA\Parameter(
     *        in="query",
     *        name="page",
     *        description="Номер текущей страницы пагинации",
     *        required=false,
     *        @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *        in="query",
     *        name="limit",
     *        description="Лимит заявок на странице пагинации",
     *        required=false,
     *        @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список заявок",
     *     )
     * )
     */
    public function actionIndex($status = null, $startDate = null, $endDate = null, int $page = 1, int $limit = 10)
    {
        $query = Requests::find();

        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }

        if ($startDate !== null) {
            $startDateFormatted = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
            $query->andWhere(['>=', 'created_at', $startDateFormatted]);
        }

        if ($endDate !== null) {
            $endDateFormatted = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));
            $query->andWhere(['<=', 'created_at', $endDateFormatted]);
        }

        $totalRecords = (int)$query->count();

        $query = $query->offset($page * $limit - $limit)->limit($limit);

        $requests = $query->all();

        $totalPages = ceil($totalRecords / $limit);
        $requests['pagination'] = [
            "total_records" => $totalRecords,
            "current_page" => $page,
            "total_pages" => $totalPages,
            "next_page" => $page < $totalPages ? $page + 1 : null,
            "prev_page" => $page > 1 && $page <= $totalPages ? $page - 1 : null,
        ];

        return $this->asJson($requests);
    }



    /**
     * @OA\Put(
     *     path="/requests/{id}/",
     *     summary="Обновление заявки",
     *     tags={"Requests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"status", "comment"},
     *             @OA\Property(property="status", type="string", enum={"Active", "Resolved"}, example="Resolved"),
     *             @OA\Property(property="comment", type="string", example="Комментарий ответственного лица")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Заявка обновлена"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Заявка не найдена"
     *     )
     * )
     */
    public function actionUpdate($id)
    {
        $request = Requests::findOne($id);
        if ($request === null) {
            Yii::$app->response->statusCode = 404;
            return $this->asJson(['error' => 'Заявка не найдена']);
        }
        if ($request->status == 'Resolved') {
            Yii::$app->response->statusCode = 404;
            return $this->asJson(['error' => 'Заявка уже решена.']);
        }
        $jsonInput = Yii::$app->request->getRawBody();
        $data = json_decode($jsonInput, true);
        if ($request->load(['Requests' => $data]) && $request->validate() && $request->save()) {
            if ($request->status == 'Resolved') {
                $this->sendEmail($request->email, $request->comment);
            }
            return $this->asJson($request);
        }
        Yii::$app->response->statusCode = 400;
        return $this->asJson(['errors' => $request->getErrors()]);
    }

    /**
     * @OA\Post(
     *     path="/requests/",
     *     summary="Создание заявки",
     *     tags={"Requests"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "status", "message"},
     *             @OA\Property(property="name", type="string", example="Иван Иванов"),
     *             @OA\Property(property="email", type="string", format="email", example="ivan@example.com"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Resolved"}, example="Active"),
     *             @OA\Property(property="message", type="string", example="Текст заявки")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заявка создана",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function actionCreate()
    {
        $request = new Requests();
        $jsonInput = Yii::$app->request->getRawBody();
        $data = json_decode($jsonInput, true);
        if ($request->load(["Requests" => $data]) && $request->validate() && $request->save()) {
            Yii::$app->response->statusCode = 201;
            return $this->asJson($request);
        }
        Yii::$app->response->statusCode = 400;
        return $this->asJson(['errors' => $request->getErrors()], 400);
    }

    protected function sendEmail($email, $comment)
    {
        return Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([Yii::$app->params['adminEmail'] => 'Admin'])
            ->setSubject('Ваша заявка обработана')
            ->setTextBody('Ваша заявка была обработана. Комментарий: ' . $comment)
            ->send();
    }
}
