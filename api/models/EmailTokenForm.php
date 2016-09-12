<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 16/04/2016
 * Time: 09:55
 */

namespace api\models;


use yii\base\Model;

class EmailTokenForm extends Model
{
    public $token;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // validate email token
            ['token', 'required'],
            ['token', 'match', 'pattern' => '/^[0-9A-F]{40}$/i'],
        ];
    }
}