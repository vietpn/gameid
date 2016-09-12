<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "social_account".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $provider
 * @property string $client_id
 * @property string $data
 * @property string $code
 * @property string $date_created
 * @property string $email
 * @property string $username
 */
class SocialAccount extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'social_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'provider', 'client_id'], 'required'],
            [['account_id'], 'integer'],
            [['data'], 'string'],
            [['date_created'], 'safe'],
            [['provider', 'client_id', 'email', 'username'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 32],
            [['provider', 'client_id'], 'unique', 'targetAttribute' => ['provider', 'client_id'], 'message' => 'The combination of Provider and Client ID has already been taken.'],
            [['client_id'], 'unique'],
            [['code'], 'unique'],
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
            'provider' => 'Provider',
            'client_id' => 'Client ID',
            'data' => 'Data',
            'code' => 'Code',
            'date_created' => 'Date Created',
            'email' => 'Email',
            'username' => 'Username',
        ];
    }
}
