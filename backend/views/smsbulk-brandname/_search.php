<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;

/* @var $this yii\web\View */
/* @var $model backend\models\SmsbulkBrandnameSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="smsbulk-brandname-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'well well-sm'
        ]
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'msisdn') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'alias') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'message') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'status')->widget(Select2::classname(), [
                'data'=>[1 => 'Success', 0 => 'Failure'],
                'pluginOptions'=>['allowClear'=>true],
                'options' => ['placeholder'=>'Select status...']
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'response') ?>
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

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
