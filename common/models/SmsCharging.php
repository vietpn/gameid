<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_charging".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $username
 * @property string $ref_code
 * @property string $command
 * @property string $mo_message
 * @property string $msisdn
 * @property string $request_id
 * @property string $request_time
 * @property string $short_code
 * @property string $signature
 * @property string $response_sms
 * @property integer $response_status
 * @property integer $charge_status
 * @property string $date_created
 */
class SmsCharging extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_charging';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'response_status', 'charge_status'], 'integer'],
            [['request_id'], 'required'],
            [['request_time', 'date_created'], 'safe'],
            [['username', 'command'], 'string', 'max' => 50],
            [['ref_code'], 'string', 'max' => 128],
            [['mo_message'], 'string', 'max' => 250],
            [['msisdn', 'short_code'], 'string', 'max' => 20],
            [['request_id', 'signature'], 'string', 'max' => 100],
            [['response_sms'], 'string', 'max' => 350],
            [['request_id'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
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
            'username' => 'Username',
            'ref_code' => 'Ref Code',
            'command' => 'Command',
            'mo_message' => 'Mo Message',
            'msisdn' => 'Msisdn',
            'request_id' => 'Request ID',
            'request_time' => 'Request Time',
            'short_code' => 'Short Code',
            'signature' => 'Signature',
            'response_sms' => 'Response Sms',
            'response_status' => 'Response Status',
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

    public function activeAccount()
    {
        $account = Account::findOne($this->account_id);

        if (empty($account) || $this->charge_status != Yii::$app->params['status_success']) {
            return false;
        }

        $account->otp_status = Yii::$app->params['status_success'];

        if (!$account->save()){
            return false;
        }

        return true;
    }
}
