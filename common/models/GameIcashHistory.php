<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "game_icash_history".
 *
 * @property integer $id
 * @property integer $game_id
 * @property integer $icash_change
 * @property integer $icash
 * @property integer $yotel_id
 * @property string $reason_code
 * @property string $reason
 * @property integer $match_id
 * @property integer $type
 * @property string $data
 * @property integer $deal_status
 * @property string $created_at
 * @property string $sync_at
 */
class GameIcashHistory extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game_icash_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id', 'icash_change', 'icash', 'yotel_id', 'match_id', 'type'], 'integer'],
            [['yotel_id'], 'required'],
            [['data'], 'string'],
            [['created_at', 'sync_at', 'deal_status'], 'safe'],
            [['reason_code'], 'string', 'max' => 15],
            [['reason'], 'string', 'max' => 512],
            [['yotel_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'game_id' => 'Game ID',
            'icash_change' => 'Icash Change',
            'icash' => 'Icash',
            'yotel_id' => 'Yotel ID',
            'reason_code' => 'Reason Code',
            'reason' => 'Reason',
            'match_id' => 'Match ID',
            'type' => 'Type',
            'data' => 'Data',
            'deal_status' => 'Deal Status',
            'created_at' => 'Created At',
            'sync_at' => 'Sync At',
        ];
    }
}
