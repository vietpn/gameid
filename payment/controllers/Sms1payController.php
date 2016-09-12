<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 27/04/2016
 * Time: 13:37
 */

namespace payment\controllers;

use common\models\SmsCharging;
use Yii;
use yii\base\Controller;

class Sms1payController extends Controller
{
    public function actionIndex()
    {
        $arParams['access_key'] = @$_GET['access_key'] ? $_GET['access_key'] : '';
        $arParams['command'] = @$_GET['command'] ? $_GET['command'] : '';
        $arParams['mo_message'] = @$_GET['mo_message'] ? $_GET['mo_message'] : '';
        $arParams['msisdn'] = @$_GET['msisdn'] ? $_GET['msisdn'] : '';
        $arParams['request_id'] = @$_GET['request_id'] ? $_GET['request_id'] : '';
        $arParams['request_time'] = @$_GET['request_time'] ? $_GET['request_time'] : '';
        $arParams['short_code'] = @$_GET['short_code'] ? $_GET['short_code'] : '';
        $arParams['signature'] = @$_GET['signature'] ? $_GET['signature'] : '';
        $data = "access_key=" . $arParams['access_key'] . "&command=" . $arParams['command'] . "&mo_message=" . $arParams['mo_message'] . "&msisdn=" . $arParams['msisdn'] . "&request_id=" . $arParams['request_id'] . "&request_time=" . $arParams['request_time'] . "&short_code=" . $arParams['short_code'];
        $secret = Yii::$app->params['1pay']['secret_key'];  // serequire your secret key from 1pay
        $signature = hash_hmac("sha256", $data, $secret);
        $arResponse['type'] = 'text';

        $model = new SmsCharging();
        $model->attributes = $arParams;

        if ($arParams['signature'] == $signature && $model->validate()) {
            //if sms content, amount,... are ok. return success
            $model->response_status = 1;
            $model->response_sms = 'Tai khoan xac nhan thanh cong.';
            $model->charge_status = Yii::$app->params['status_success'];
            $messages = explode(" ", $arParams['mo_message']);

            $account = (!empty($messages[0])) ? $model->checkExistAccount($messages[0]) : '';
            if (empty($account)) {
                $model->response_status = 0;
                $model->response_sms = 'Tai khoan khong ton tai';
                $model->charge_status = Yii::$app->params['status_fail'];
            }

            $model->account_id = $account->getAttribute('id');
            $model->username = $account->getAttribute('username');

            if (!$model->activeAccount()) {
                $model->response_status = 0;
                $model->response_sms = 'Tai khoan xac nhan khong thanh cong';
                $model->charge_status = Yii::$app->params['status_fail'];
            }
        } else {
            //if not, return unsuccess
            $model->response_status = 0;
            $model->response_sms = 'Tai khoan xac nhan khong thanh cong';
            $model->charge_status = Yii::$app->params['status_fail'];
        }

        $arResponse['status'] = $model->response_status;
        $arResponse['sms'] = $model->response_sms;

        // write log charging
        if ($model->validate())
            $model->save();

        // return json for 1pay system
        echo json_encode($arResponse);
    }
}