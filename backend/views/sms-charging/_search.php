<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;

/* @var $this yii\web\View */
/* @var $model backend\models\SmsChargingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-charging-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'well well-sm'
        ]
    ]); ?>


    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'account_id') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'username') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'short_code') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'command') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'charge_status')->widget(Select2::classname(), [
                'data'=>[1 => 'Success', 0 => 'Failure'],
                'pluginOptions'=>['allowClear'=>true],
                'options' => ['placeholder'=>'Select status...']
            ]); ?>
        </div>
        <div class="col-md-4">
            <?php
            echo FieldRange::widget([
                'form' => $form,
                'model' => $model,
                'label' => 'Khoảng thời gian',
                'attribute1' => 'startTime',
                'attribute2' => 'endTime',
                'type' => FieldRange::INPUT_DATE,
                'fieldConfig1' => ['addon' => [
                    'prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>'],
                    //'append' => ['content'=>'.txt']
                ]],
                'fieldConfig2' => ['addon' => [
                    'prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>'],
                    //'append' => ['content'=>'.txt']
                ]],
                'widgetOptions1' => [
                    'pluginOptions' => ['autoclose' => true,'format' => 'dd-mm-yyyy'],
                ],
                'widgetOptions2' => [
                    'pluginOptions' => ['autoclose' => true,'format' => 'dd-mm-yyyy'],
                ],
            ]);
            ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'mo_message') ?>

    <?php // echo $form->field($model, 'msisdn') ?>

    <?php // echo $form->field($model, 'request_id') ?>

    <?php // echo $form->field($model, 'request_time') ?>

    <?php // echo $form->field($model, 'short_code') ?>

    <?php // echo $form->field($model, 'signature') ?>

    <?php // echo $form->field($model, 'response_sms') ?>

    <?php // echo $form->field($model, 'response_status') ?>

    <?php // echo $form->field($model, 'charge_status') ?>

    <?php // echo $form->field($model, 'date_created') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
