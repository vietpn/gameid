<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 14/04/2016
 * Time: 15:36
 */

namespace api\controllers;

use api\models\EmailTokenForm;
use common\models\Account;
use Yii;
use yii\base\Controller;

class ActiveController extends Controller
{
    public function actionIndex()
    {
        $params = Yii::$app->request->get();
        $form = new EmailTokenForm();
        $form->attributes = $params;

        if(!$form->validate()){
            return $this->render('index', ['messages' => $form->errors]);
        }

        $accounts = Account::find()->where(['email_token' => $form->token]);
        if (!$accounts->exists()) {
            return $this->render('index', ['messages' => 'Token is not exist']);
        }

        $model =$accounts->all()[0];
        if ($model->email_token_expire - $_SERVER["REQUEST_TIME"] > Yii::$app->params['email']['time_expire']) {
            return $this->render('index', ['messages' => 'Time expire']);
        }

        $model->email_token = null;
        $model->email_token_expire = null;
        $model->email_status = Yii::$app->params['status_success'];

        if(!$model->save()){
            return false;
        }

        return $this->render('index');
    }
}