<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SmsplusCharging */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="smsplus-charging-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'account_id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ref_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'msisdn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'command_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'error_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'error_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mo_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_time')->textInput() ?>

    <?= $form->field($model, 'signature')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'response_sms')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'response_status')->textInput() ?>

    <?= $form->field($model, 'response_game')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'response_game_status')->textInput() ?>

    <?= $form->field($model, 'charge_status')->textInput() ?>

    <?= $form->field($model, 'date_created')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
