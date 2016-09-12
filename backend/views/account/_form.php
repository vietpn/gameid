<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Account */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'partner_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'provider_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ref_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'screen_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'birthday')->textInput() ?>

    <?= $form->field($model, 'birthyear')->textInput() ?>

    <?= $form->field($model, 'gender')->textInput() ?>

    <?= $form->field($model, 'passport')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'client_version')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'platform')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'os_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login_times')->textInput() ?>

    <?= $form->field($model, 'last_login')->textInput() ?>

    <?= $form->field($model, 'last_login_ip_addr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_created')->textInput() ?>

    <?= $form->field($model, 'date_modified')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'otp_status')->textInput() ?>

    <?= $form->field($model, 'ncoin')->textInput() ?>

    <?= $form->field($model, 'vcoin')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
