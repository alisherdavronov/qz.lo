<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                    ],
                ],
            ],
        ];
    }

    public function actionProfile()
    {
        $model = User::findOne(\Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate(['email'])) {
            if ($model->save(true, ['email']))
                Yii::$app->session->setFlash('success', 'Ваш эмейл ушпешно изменен.');
            else
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении.');

            return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }
}
