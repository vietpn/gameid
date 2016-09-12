<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 22/04/2016
 * Time: 14:40
 */

namespace payment\controllers;

use common\models\CheckMoSmsplus;
use Yii;
use yii\base\Controller;


class CheckMoSmsplus1payController extends Controller
{
    public function actionIndex()
    {
        $arParams['access_key'] = @$_GET['access_key'] ? $_GET['access_key'] : '';
        $arParams['amount'] = @$_GET['amount'] ? $_GET['amount'] : '';
        $arParams['command_code'] = @$_GET['command_code'] ? $_GET['command_code'] : '';
        $arParams['mo_message'] = @$_GET['mo_message'] ? $_GET['mo_message'] : '';
        $arParams['msisdn'] = @$_GET['msisdn'] ? $_GET['msisdn'] : '';
        $arParams['telco'] = @$_GET['telco'] ? $_GET['telco'] : '';
        $arParams['signature'] = @$_GET['signature'] ? $_GET['signature'] : '';
        $data = "access_key=" . $arParams['access_key'] . "&amount=" . $arParams['amount'] . "&command_code=" . $arParams['command_code'] . "&mo_message=" . $arParams['mo_message'] . "&msisdn=" . $arParams['msisdn'] . "&telco=" . $arParams['telco'];
        $secret = Yii::$app->params['1pay']['secret_key']; //product's secret key (get value from 1Pay product detail)
        $signature = hash_hmac("sha256", $data, $secret); // create signature to check
        $arResponse['type'] = 'text';

        $model = new CheckMoSmsplus();
        $model->attributes = $arParams;

        // kiem tra signature neu can
        if ($arParams['signature'] == $signature &&
            in_array(intval($arParams['amount']), Yii::$app->params['yotel']['allow_amount'])) {
            // tin nhan hop le
            $model->response_status = 1;
            $model->response_sms = 'Tin nhan hop le';
            $model->charge_status = Yii::$app->params['status_success'];
            $messages = explode(" ", $arParams['mo_message']);

            // check format sms message
            if (count($messages) < 3) {
                $model->response_status = 0;
                $model->response_sms = 'Tin nhan khong hop le';
                $model->charge_status = Yii::$app->params['status_fail'];
            }
        } else {
            //if not. return fail case
            $model->response_status = 0;
            $model->response_sms = 'Tin nhan khong hop le';
            $model->charge_status = Yii::$app->params['status_fail'];
        }

        $arResponse['status'] = $model->response_status;
        $arResponse['sms'] = $model->response_sms;

        // write log
        if ($model->validate())
            $model->save();

        // return json for 1pay system
        echo json_encode($arResponse);
    }
}