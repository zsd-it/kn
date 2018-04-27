<?php
namespace backend\controllers;

use common\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * User controller
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['register', 'error'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'register' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionRegister()
    {
        $request = json_decode(Yii::$app->request->rawBody, true);

        if (!$request['username'] || !$request['password']) {
            throw new BadRequestHttpException('请输入用户名,密码');
        }

        $user = new User();
        if ($user->findOne(['username' => $request['username']])) {
            throw new BadRequestHttpException('该用户名已经存在');
        }
        $user->username      = $request['username'];
        $user->password_hash = Yii::$app->security->generatePasswordHash($request['password']);
        $user->generateAuthKey();

        if (!$user->save()) {
            throw new BadRequestHttpException(current($user->getErrors()));
        }

        return true;
    }
}
