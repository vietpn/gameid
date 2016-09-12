<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 22/04/2016
 * Time: 14:42
 */

namespace payment\controllers;

use common\models\Account;
use common\models\SmsCharging;
use common\models\SmsplusCharging;
use Yii;
use yii\base\Controller;

class Smsplus1payController extends Controller
{
    public function actionIndex()
    {
        $arParams['access_key'] = @$_GET['access_key'] ? $_GET['access_key'] : '';
        $arParams['command_code'] = @$_GET['command_code'] ? $_GET['command_code'] : '';
        $arParams['mo_message'] = @$_GET['mo_message'] ? $_GET['mo_message'] : '';
        $arParams['msisdn'] = @$_GET['msisdn'] ? $_GET['msisdn'] : '';
        $arParams['request_id'] = @$_GET['request_id'] ? $_GET['request_id'] : '';
        $arParams['request_time'] = @$_GET['request_time'] ? $_GET['request_time'] : '';
        $arParams['amount'] = @$_GET['amount'] ? $_GET['amount'] : '';
        $arParams['signature'] = @$_GET['signature'] ? $_GET['signature'] : '';
        $arParams['error_code'] = @$_GET['error_code'] ? $_GET['error_code'] : '';
        $arParams['error_message'] = @$_GET['error_message'] ? $_GET['error_message'] : '';
        $data = "access_key=" . $arParams['access_key'] . "&amount=" . $arParams['amount'] . "&command_code=" . $arParams['command_code'] . "&error_code=" . $arParams['error_code'] . "&error_message=" . $arParams['error_message'] . "&mo_message=" . $arParams['mo_message'] . "&msisdn=" . $arParams['msisdn'] . "&request_id=" . $arParams['request_id'] . "&request_time=" . $arParams['request_time'];
        $secret = Yii::$app->params['1pay']['secret_key']; //product's secret key (get value from 1Pay product detail)
        $signature = hash_hmac("sha256", $data, $secret); // create signature to check
        $arResponse['type'] = 'text';

        $model = new SmsplusCharging();
        $model->attributes = $arParams;

        // kiem tra signature neu can va request_id
        if ($arParams['signature'] == $signature && $model->validate() &&
            in_array(intval($arParams['amount']), Yii::$app->params['yotel']['allow_amount'])
        ) {
            //if sms content and amount and ... are ok. return success case
            $model->response_status = 1;
            $iCash = $arParams['amount'] * Yii::$app->params['yotel']['exchange_rate'];


            // $model->response_sms = 'Ban da nap thanh cong ' . $iCash . ' iCash. Lien he Hotline: 0911822998';
            $model->response_sms = 'Ban da thanh toan thanh cong giao dich. So tien: ' . $arParams['amount'] . ' VND. Moi thac mac vui long Lien hẹ hotline: 0911822998';

            $model->charge_status = Yii::$app->params['status_success'];
            $messages = explode(" ", $arParams['mo_message']);

//            $account = $model->checkExistAccount($messages[2]);
//            if (!$account) {
//                $model->response_status = 0;
//                $model->response_sms = 'Giao dich khong thanh cong. Account khong ton tai';
//                $model->charge_status = Yii::$app->params['status_fail'];
//            } else {
//                $model->account_id = $account->id;
//                $model->username = $account->username;
//            }
            // sms charging without username
            //$model->username = $messages[2];
            if (is_numeric($messages[2])) {
                $model->game_id = $messages[2];

                // find account id if existing
                //$account = Account::find()->where(['username' => $model->username]);
                $account = Account::find()->where(['game_id' => $model->game_id]);
                if ($account->exists()) {
                    $model->account_id = $account->all()[0]->id;
                    $model->username = $account->all()[0]->username;
                }
            }
        } else {
            //if not. return fail case
            $model->response_status = 0;
            $model->response_sms = 'Giao dich khong thanh cong.';
            $model->charge_status = Yii::$app->params['status_fail'];
        }

        $arResponse['status'] = $model->response_status;
        $arResponse['sms'] = $model->response_sms;

        // write log charging
        if ($model->validate()) {
            $model->save();
            $model->addToQueue();
        }

        // return json for 1pay system
        echo json_encode($arResponse);
    }
}