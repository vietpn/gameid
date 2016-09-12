<?php

namespace console\controllers;

use common\models\SmsplusCharging;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use yii\console\Controller;
use yii;

class SendToYotelController extends Controller
{

    public function actionIndex()
    {
        $connection = new AMQPStreamConnection(
            Yii::$app->params['queue']['host'],
            Yii::$app->params['queue']['port'],
            Yii::$app->params['queue']['user'],
            Yii::$app->params['queue']['pass']);

        $channel = $connection->channel();

        $channel->queue_declare(Yii::$app->params['queue']['name'], false, false, false, false);
        Yii::info("[*] Waiting for messages. To exit press CTRL+C \n", Yii::$app->params['key_log_smsplus_charging']);

        $callback = function ($msg) {
            Yii::info("******* Handing queue ******* " . $msg->body . "\n", Yii::$app->params['key_log_smsplus_charging']);
            $json = json_decode($msg->body, true);
            if (!empty($json['id'])) {
                $model = SmsplusCharging::findOne($json['id']);
                if (!empty($model) && $model->charge_status == Yii::$app->params['status_success']) {
                    Yii::info("Send Smsplus Charging ID: " . $model->id . "\n", Yii::$app->params['key_log_smsplus_charging']);
                    $model->sendSmsToGame();
                    if ($model->validate()) {
                        $model->save();
                    } else {
                        Yii::info($model->errors, Yii::$app->params['key_log_smsplus_charging']);
                    }
                }
            }
        };

        $channel->basic_consume(Yii::$app->params['queue']['name'], '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    public function actionRecall()
    {
        // time kiem sms loi
        $query = SmsplusCharging::find();
        $query->select("*");

        // tim kiem theo id
        $query->andWhere(['>=', 'id', 19145]);
        // tim kiem response game that bai
        $query->andWhere(['=', 'response_game_status', 0]);
        $rows = $query->all();

        // add lai vao queue
        if(!empty($rows)){
            foreach($rows as $row){
                $row->addToQueue();
            }
        }
        echo "Done!\n";
        exit;
        //var_dump($rows);die;
    }
}