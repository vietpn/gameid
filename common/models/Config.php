<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property integer $id
 * @property string $key
 * @property string $description
 * @property string $value
 */
class Config extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['date_created'], 'safe'],
            [['key', 'value'], 'string', 'max' => 45],
            [['description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
            'description' => 'Description',
        ];
    }

    public static function limitBuying()
    {
        $config = Config::find()->where(['key' => 'limit_buying']);

        if (!$config->exists()) {
            return 0;
        }

        return intval($config->all()[0]->getAttribute('value'));
    }
}