<?php
/**
 * Created by IntelliJ IDEA.
 * User: vietpn
 * Date: 30/05/2016
 * Time: 09:58
 */

namespace backend\models;

use dektrium\user\models\LoginForm as BaseLoginForm;
use Yii;

class LoginForm extends BaseLoginForm
{
    /**
     * @var string
     */
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        array_push($rules, [['captcha'],
            \himiklab\yii2\recaptcha\ReCaptchaValidator::className(),
            'secret' => Yii::$app->params['google']['recaptcha']['secret']
        ]);

        return $rules;
    }
}