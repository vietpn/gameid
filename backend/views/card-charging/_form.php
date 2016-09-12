<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CardCharging */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card-charging-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'account_id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cate_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'card_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'card_serial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'charge_status')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'msg')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'gdc_tran_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay365_tran_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tran_status')->textInput() ?>

    <?= $form->field($model, 'date_created')->textInput() ?>

    <?= $form->field($model, 'response_at')->textInput() ?>

    <?= $form->field($model, 'response_code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
