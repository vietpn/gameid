<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "check_mo_smsplus".
 *
 * @property integer $id
 * @property string $amount
 * @property string $command_code
 * @property string $mo_message
 * @property string $msisdn
 * @property string $telco
 * @property string $signature
 * @property string $response_sms
 * @property integer $response_status
 * @property integer $charge_status
 * @property string $date_created
 */
class CheckMoSmsplus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'check_mo_smsplus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['response_status', 'charge_status'], 'integer'],
            [['date_created'], 'safe'],
            [['amount', 'signature'], 'string', 'max' => 100],
            [['command_code', 'telco'], 'string', 'max' => 50],
            [['mo_message'], 'string', 'max' => 250],
            [['msisdn'], 'string', 'max' => 20],
            [['response_sms'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'amount' => 'Amount',
            'command_code' => 'Command Code',
            'mo_message' => 'Mo Message',
            'msisdn' => 'Msisdn',
            'telco' => 'Telco',
            'signature' => 'Signature',
            'response_sms' => 'Response Sms',
            'response_status' => 'Response Status',
            'charge_status' => 'Charge Status',
            'date_created' => 'Date Created',
        ];
    }
}
