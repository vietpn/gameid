<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;

/* @var $this yii\web\View */
/* @var $model backend\models\CardChargingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card-charging-search">
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
            <?= $form->field($model, 'cate_code')->widget(Select2::classname(), [
                'data'=> Yii::$app->params['pay365']['cate_code'],
                'pluginOptions'=>['allowClear'=>true],
                'options' => ['placeholder'=>'Select cate code...']
            ]); ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'card_code') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'response_code') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'card_serial') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'amount')->widget(Select2::classname(), [
                'data'=>[
                    10000 => '10,000',
                    20000 => '20,000',
                    30000 => '30,000',
                    50000 => '50,000',
                    100000 => '100,000',
                    200000 => '200,000',
                    300000 => '300,000',
                    500000 => '500,000',
                ],
                'pluginOptions'=>['allowClear'=>true],
                'options' => ['placeholder'=>'Select amount...']
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'charge_status')->widget(Select2::classname(), [
                'data'=>[1 => 'Success', 0 => 'Failure'],
                'pluginOptions'=>['allowClear'=>true],
                'options' => ['placeholder'=>'Select status...']
            ]); ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'des') ?>
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

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'account_id') ?>

    <?php // echo $form->field($model, 'username') ?>

    <?php // echo $form->field($model, 'cate_code') ?>

    <?php // echo $form->field($model, 'card_code') ?>

    <?php // echo $form->field($model, 'card_serial') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'charge_status') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'msg') ?>

    <?php // echo $form->field($model, 'gdc_tran_id') ?>

    <?php // echo $form->field($model, 'pay365_tran_id') ?>

    <?php // echo $form->field($model, 'tran_status') ?>

    <?php // echo $form->field($model, 'date_created') ?>

    <?php // echo $form->field($model, 'response_at') ?>

    <?php // echo $form->field($model, 'response_code') ?>


    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
