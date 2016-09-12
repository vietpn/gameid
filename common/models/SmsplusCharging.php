<?php

namespace common\models;

use common\utils\SystemUtil;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "smsplus_charging".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $game_id
 * @property string $username
 * @property string $ref_code
 * @property string $msisdn
 * @property integer $telco
 * @property string $amount
 * @property string $command_code
 * @property string $error_code
 * @property string $error_message
 * @property string $mo_message
 * @property string $request_id
 * @property string $request_time
 * @property string $signature
 * @property string $response_sms
 * @property string $response_game
 * @property integer $response_game_status
 * @property integer $response_status
 * @property integer $charge_status
 * @property string $date_created
 */
class SmsplusCharging extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'smsplus_charging';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['account_id', 'response_status', 'charge_status', 'response_game_status'], 'integer'],
            [['response_status', 'charge_status', 'response_game_status'], 'integer'],
            [['request_id'], 'required'],
            [['request_time', 'date_created', 'telco', 'game_id'], 'safe'],
            [['username', 'command_code'], 'string', 'max' => 50],
            [['ref_code'], 'string', 'max' => 128],
            [['msisdn'], 'string', 'max' => 20],
            [['amount', 'request_id', 'signature'], 'string', 'max' => 100],
            [['error_code'], 'string', 'max' => 150],
            [['error_message'], 'string', 'max' => 500],
            [['mo_message'], 'string', 'max' => 250],
            [['response_sms', 'response_game'], 'string', 'max' => 350],
            [['request_id'], 'unique'],
            //[['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'game_id' => 'Game ID',
            'username' => 'Username',
            'ref_code' => 'Ref Code',
            'msisdn' => 'Msisdn',
            'telco' => 'Telco',
            'amount' => 'Amount',
            'command_code' => 'Command Code',
            'error_code' => 'Error Code',
            'error_message' => 'Error Message',
            'mo_message' => 'Mo Message',
            'request_id' => 'Request ID',
            'request_time' => 'Request Time',
            'signature' => 'Signature',
            'response_sms' => 'Response Sms',
            'response_status' => 'Response Status',
            'response_game' => 'Response Game',
            'response_game_status' => 'Response Game Status',
            'charge_status' => 'Charge Status',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * @param $username
     * @return bool|\yii\db\ActiveRecord
     */
    public function checkExistAccount($username)
    {
        if (empty($username)) {
            return false;
        }

        $account = Account::find()->where(['username' => $username]);
        if (!$account->exists()) {
            return false;
        }

        return $account->all()[0];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('msisdn')) {
                $this->telco = SystemUtil::getTelcoId($this->msisdn);
            }
            return true;
        }
        return false;
    }

    /**
     * Send request to game
     */
    public function sendSmsToGame()
    {
        try {
            Yii::info("Smsplus Charging ID:  " . $this->id . " Create curl ", Yii::$app->params['key_log_smsplus_charging']);

            // create curl resource
            $curl = curl_init();

            $params = array(
                'mobile' => $this->msisdn,
                'serviceNumber' => Yii::$app->params['1pay']['smsplus_number'],
                'userId' => (!empty($this->game_id)) ? $this->game_id : '',
                'message' => $this->mo_message,
                'amount' => $this->amount
            );
            $url = Yii::$app->params['yotel']['sms_id_url'] . '?' . http_build_query($params);
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [],
                CURLOPT_TIMEOUT => 180,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array('token: ' . Yii::$app->params['yotel']['token'])
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $this->response_game = $url;

            Yii::info("Smsplus Charging ID:  " . $this->id . " Error curl: " . $err . "\n", Yii::$app->params['key_log_smsplus_charging']);
            Yii::info("Smsplus Charging ID:  " . $this->id . " Response from game: " . $response . "\n", Yii::$app->params['key_log_smsplus_charging']);

            $json = json_decode($response, true);
            if(isset($json['status']) && $json['status'] == 0){
                $this->response_game_status = 1;
            }
            curl_close($curl);
        } catch (Exception $e) {
            Yii::info("Smsplus Charging ID:  " . $this->id . " Exception: " . $e->getMessage() . "\n", Yii::$app->params['key_log_smsplus_charging']);
            Yii::error($e->getMessage());
        }
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

            $channel->queue_declare(Yii::$app->params['queue']['name'], false, false, false, false);
            $msg = new AMQPMessage(json_encode($this->attributes));

            $channel->basic_publish($msg, '', Yii::$app->params['queue']['name']);

            $channel->close();
            $connection->close();
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}
