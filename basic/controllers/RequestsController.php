<?php

namespace app\controllers;

use Yii;
use app\models\Request;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use yii\web\Response;




class RequestsController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();



        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:8080'], // TODO придумать что-то более оригинальное
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'PUT', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    /**
     * @SWG\SecurityScheme(
     *     securityDefinition="BearerAuth",
     *     type="apiKey",
     *     in="header",
     *     name="Authorization",
     *     description="Введите токен в формате: Bearer {ваш_токен}"
     * )
     */


    /**
     * @SWG\Post(
     *     path="/requests",
     *     tags={"Requests"},
     *     summary="Создание новой заявки",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="name", type="string", description="Имя пользователя"),
     *             @SWG\Property(property="email", type="string", description="Email пользователя"),
     *             @SWG\Property(property="message", type="string", description="Сообщение заявки")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Успешное создание заявки"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Ошибка при создании заявки"
     *     )
     * )
     */



    public function actionCreate()
    {

        $requestData = Yii::$app->request->post();
        $request = new Request();

        $request->name = $requestData['name'];
        $request->email = $requestData['email'];
        $request->message = $requestData['message'];
        $request->status = 'Active';

        if ($request->save()) {
            Yii::$app->mailer->compose()
                ->setFrom('admin@example.com')
                ->setTo($request->email)
                ->setSubject('Ваша заявка принята')
                ->setTextBody('Ваша заявка принята в обработку.')
                ->send();

            return $this->asJson(['status' => 'success', 'message' => 'Заявка создана']);
        }

        return $this->asJson(['status' => 'error', 'message' => 'Ошибка при создании заявки']);
    }

    /**
     * @SWG\Get(
     *     path="/requests",
     *     tags={"Requests"},
     *     summary="Получение списка заявок",
     *     security={{"BearerAuth": {}}},
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         type="string",
     *         description="Фильтр по статусу"
     *     ),
     *     @SWG\Parameter(
     *         name="date_from",
     *         in="query",
     *         type="string",
     *         description="Фильтр по дате начала (YYYY-MM-DD)"
     *     ),
     *     @SWG\Parameter(
     *         name="date_to",
     *         in="query",
     *         type="string",
     *         description="Фильтр по дате окончания (YYYY-MM-DD)"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Список заявок"
     *     )
     * )
     */
    public function actionIndex($status = null, $date_from = null, $date_to = null)
    {
        $this->checkAdminAccess();

        $query = Request::find();  // TODO serchModel

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        if ($date_from) {
            $dateFrom = strtotime($date_from . ' 00:00:00');
            if ($dateFrom !== false) {
                $query->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', $dateFrom)]);
            }
        }

        // Фильтр по дате окончания
        if ($date_to) {
            $dateTo = strtotime($date_to . ' 23:59:59');
            if ($dateTo !== false) {
                $query->andWhere(['<=', 'created_at', date('Y-m-d H:i:s', $dateTo)]);
            }
        }

        $requests = $query->all();
        return $this->asJson($requests);
    }

    /**
     * @SWG\Put(
     *     path="/requests/{id}",
     *     tags={"Requests"},
     *     summary="Обновление заявки",
     *     security={{"BearerAuth": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Идентификатор заявки"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="comment", type="string", description="Комментарий для заявки")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Заявка успешно обновлена"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Заявка не найдена"
     *     )
     * )
     */
    public function actionUpdate($id)
    {

        $this->checkAdminAccess();

        $request = $this->findRequestModel($id);

        $requestData = Yii::$app->request->post();


        if (!isset($requestData['comment'])) {
            return $this->asJson(['status' => 'error', 'message' => 'Комментарий обязателен']);
        }


        $request->status = 'Completed';
        $request->comment = $requestData['comment'];

        if ($request->save()) {
            // Отправка email
            Yii::$app->mailer->compose()
                ->setFrom('admin@example.com')
                ->setTo($request->email)
                ->setSubject('Ответ на вашу заявку')
                ->setTextBody($request->comment)
                ->send();

            return $this->asJson(['status' => 'success', 'message' => 'Заявка обновлена']);
        }

        return $this->asJson(['status' => 'error', 'message' => 'Ошибка при обновлении заявки']);
    }


    /**
     * Найти заявку по ID
     *
     * @param int $id
     * @return Request
     * @throws NotFoundHttpException
     */
    protected function findRequestModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Заявка не найдена');
    }

    protected function checkAdminAccess()
    {
        $token = Yii::$app->request->headers->get('Authorization');
        $expectedToken = 'Bearer ' . Yii::$app->params['adminApiKey']; //  токен

        if ($token !== $expectedToken) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 403;
            Yii::$app->response->data = ['status' => 'error', 'message' => 'Нет доступа'];
            Yii::$app->end();
        }
    }

}
