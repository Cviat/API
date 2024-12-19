<?php

namespace app\controllers;

use Yii;
use app\models\Request;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\Cors;
use yii\web\Response;


// TODO вроде в тз не сказано про JWT , но по идее оно тут нужно
class RequestController extends Controller
{
    // Добавим фильтрацию CORS
    public function behaviors()
    {
        $behaviors = parent::behaviors();


        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:8080'], // TODO придумать что-то более оригинальней
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'PUT', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        return $behaviors;
    }

    // POST /requests - создание заявки
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

    // GET /requests - получение списка заявок
    public function actionIndex($status = null, $date_from = null, $date_to = null)
    {
        $query = Request::find();

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        if ($date_from) {
            $query->andWhere(['>=', 'created_at', $date_from]);
        }

        if ($date_to) {
            $query->andWhere(['<=', 'created_at', $date_to]);
        }

        $requests = $query->all();
        return $this->asJson($requests);
    }

    // PUT /requests/{id} - обновление заявки (ответ на заявку)
    public function actionUpdate($id)
    {
        $request = $this->findRequestModel($id);

        $requestData = Yii::$app->request->post();

        if ($request->status == 'Resolved') {

            $request->comment = $requestData['comment'];
        }

        $request->status = 'Resolved';
        if ($request->save()) {

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

    // Помощь для поиска заявки по ID
    protected function findRequestModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Заявка не найдена');
    }
}
