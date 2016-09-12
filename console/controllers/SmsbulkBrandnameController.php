<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 05/07/2016
 * Time: 09:34
 */

namespace console\controllers;

use common\models\SmsbulkBrandname;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii\console\Controller;
use yii;

class SmsbulkBrandnameController extends Controller
{
    public function actionIndex()
    {
        $connection = new AMQPStreamConnection(
            Yii::$app->params['queue']['host'],
            Yii::$app->params['queue']['port'],
            Yii::$app->params['queue']['user'],
            Yii::$app->params['queue']['pass']);

        $channel = $connection->channel();

        $channel->queue_declare(Yii::$app->params['queue']['smsbulk_brandname'], false, false, false, false);
        Yii::info("[*] Waiting for messages. To exit press CTRL+C \n", Yii::$app->params['key_log_smsbulk_brandname']);

        $callback = function ($msg) {
            Yii::info("******* Handing queue ******* " . $msg->body . "\n", Yii::$app->params['key_log_smsbulk_brandname']);
            $json = json_decode($msg->body, true);
            Yii::info("Smsbulk brand name ID: " . $json['id'], Yii::$app->params['key_log_smsbulk_brandname']);
            if (!empty($json['id'])) {
                $model = \common\models\SmsbulkBrandname::findOne($json['id']);
                Yii::info("Smsbulk brand name ID: " . $model->id . "\n", Yii::$app->params['key_log_smsbulk_brandname']);
                Yii::info("Smsbulk brand name ID: " . $model->id . ", status: " . $model->status . "\n", Yii::$app->params['key_log_smsbulk_brandname']);
                if (!empty($model) && $model->status == 0) {
                    Yii::info("Smsbulk brand name ID: " . $model->id . " Send sms bulk \n", Yii::$app->params['key_log_smsbulk_brandname']);
                    $model->sendSmsbulk();
                    if ($model->validate()) {
                        $model->save();
                    } else {
                        Yii::info($model->errors, Yii::$app->params['key_log_smsbulk_brandname']);
                    }
                }
            }
        };

        $channel->basic_consume(Yii::$app->params['queue']['smsbulk_brandname'], '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }


    /**
     * Gửi tin nhắn spam cho đại lý
     */
    public function actionSendSpamMessage()
    {
        echo "[*] Send spam message to users \n";
        // read phone number form file
        $file = file_get_contents(
            Yii::getAlias('console') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'spamPhoneNumbers'
        );

        $message = 'Dai ly Thuy Linh chuyen mua ban iCash game Tu quy K, gia tot giao dich toan quoc nhanh chong tien loi. L/H: 0912562626';
        $numbers = explode("\n", $file);
        $format = [];
        $count = 0;
        foreach ($numbers as $num) {
            $tmp = '';
            if (isset($num[0]) && $num[0] == 0) {
                $tmp = '84' . substr($num, 1, strlen($num));
            } else {
                $tmp = '84' . $num;
            }
            if (!empty($tmp) && !in_array($tmp, $format)) {
                // Kiểm tra số tin đã nhẵn
                $numberExit = SmsbulkBrandname::find()->where([
                    'msisdn' => $tmp,
                    'message' => $message
                ]);
                // bắn thêm 400 thằng
                if (!$numberExit->exists() && $count < 400) {
                    $format[] = $tmp;
                    $count++;
                }
            }
        }

        // loop all user to send spam message
        if (!empty($format)) {
            foreach ($format as $num) {
                $model = new SmsbulkBrandname();

                $model->msisdn = $num;
                $model->alias = 'iCash';
                $model->message = $message;
                $model->status = 0;

                if (!$model->validate() || !$model->save()) {
                    echo "Error in sending  \n";
                    var_dump($model->errors);
                } else {
                    $model->addToQueue();
                    echo "Added queue: " . $model->msisdn . "\n";
                }
            }
        }
        // end loop
    }
}