<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne(['email'=>$model->email]);

            if ($user === null && !User::createUser($model->email)) {
                Yii::$app->session->setFlash('error', 'Ошибка при сохранении нового пользователя.');
                return $this->goBack();
            }

            $link = Url::to(['site/token', 'token'=>$user->token], true);
            $link = Html::a('ссылке', $link);

            $body = 'Вы зарегистрировались на сайте qz.lo. Чтобы подтвердить перейдите по ' . $link;
            $body.= '. Если это были не вы, тогда проста игнорируйте это письмо.';

            $res = Yii::$app->mail->compose()
                ->setTo($user->email)
                ->setFrom(['site-robot@qz.lo' => 'Робот сайта qz.lo'])
                ->setSubject('Подтверждение регистрации на сайте qz.lo')
                ->setTextBody($body)
                ->send();

            if ($res)
                Yii::$app->session->setFlash('success', 'На указанный эмейл отправлено сообщение.');
            else
                Yii::$app->session->setFlash('error', 'Ошибка отправки эмейл.');

            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionToken($token)
    {
        $user = User::findOne(['token' => $token]);
        if ($user === null) {
            Yii::$app->session->setFlash('error', 'Ошибка авторизации.');
            return $this->redirect(['site/login']);
        }

        if (!Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('error', 'Ошибка авторизации.');
            return $this->redirect(['site/login']);
        }

        Yii::$app->session->setFlash('success', 'Успешная авторизация.');
        $user->clearToken();
        return $this->redirect(['user/profile']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
