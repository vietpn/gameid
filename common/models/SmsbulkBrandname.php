<?php

namespace common\models;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "smsbulk_brandname".
 *
 * @property integer $id
 * @property string $msisdn
 * @property string $alias
 * @property string $message
 * @property string $send_time
 * @property string $response
 * @property integer $status
 * @property string $date_created
 */
class SmsbulkBrandname extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'smsbulk_brandname';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['msisdn', 'alias', 'message'], 'required'],
            [['date_created', 'status'], 'safe'],
            [['msisdn', 'alias', 'send_time'], 'string', 'max' => 50],
            [['message'], 'string', 'max' => 500],
            [['response'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'msisdn' => 'Msisdn',
            'alias' => 'Alias',
            'message' => 'Message',
            'send_time' => 'Send Time',
            'response' => 'Response',
            'status' => 'Status',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $str = substr($this->msisdn, 0, 1);
        $str_replace = substr($this->msisdn, 1, strlen($this->msisdn));
        if ($str == 0):
            $this->msisdn = '84' . $str_replace;
        endif;
        return parent::beforeSave($insert);
    }

    public function addToQueue()
    {
        try {
            // init server queue
            $connection = new AMQPStreamConnection(
                Yii::$app->params['queue']['host'],
                Yii::$app->params['queue']['port'],
                Yii::$app->params['queue']['user'],
                Yii::$app->params['queue']['pass']);

            $channel = $connection->channel();

            $channel->queue_declare(Yii::$app->params['queue']['smsbulk_brandname'], false, false, false, false);
            Yii::info("Add Queue: " . json_encode($this->attributes), Yii::$app->params['key_log_smsbulk_brandname']);
            $msg = new AMQPMessage(json_encode($this->attributes));

            $channel->basic_publish($msg, '', Yii::$app->params['queue']['smsbulk_brandname']);

            $channel->close();
            $connection->close();
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }

    public function sendSmsbulk()
    {
        try {
            Yii::info("Smsbulk brand name ID: " . $this->id . " create curl: " . "\n", Yii::$app->params['key_log_smsbulk_brandname']);
            $client = new \SoapClient(Yii::$app->params['sms_brandname']['url']);
            $params = array(
                'msisdn' => $this->msisdn,
                'alias' => $this->alias,
                'message' => $this->message,
                'sendTime' => '',
                'authenticateUser' => Yii::$app->params['sms_brandname']['authenticateUser'],
                'authenticatePass' => Yii::$app->params['sms_brandname']['authenticatePass'],
            );
            Yii::info("Smsbulk brand name ID: " . $this->id . " Params send: " . json_encode($params, true) . "\n", Yii::$app->params['key_log_smsbulk_brandname']);

            $response = $client->__soapCall("BulkSendSms", array($params));

            $json = json_encode($response, true);
            Yii::info("Smsbulk brand name ID: " . $this->id . " Response from sms brand name: " . $json . "\n", Yii::$app->params['key_log_smsbulk_brandname']);

            // response from brandname server
            if (!empty($json)) {
                $this->response = $json;
                $resJson = json_decode($json, true);
                if (!empty($resJson['BulkSendSmsResult']['messageId'])) {
                    $this->status = 1;
                }
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}
