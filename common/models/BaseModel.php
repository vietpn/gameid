<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 21/04/2016
 * Time: 11:22
 */

namespace common\models;


class BaseModel extends \yii\db\ActiveRecord
{
    public $startTime;
    public $endTime;

    /**
     * @param $provider
     * @param $fieldName
     * @return int
     */
    public static function pageTotal($provider, $fieldName)
    {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }
        return $total;
    }

}