<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
?>
<div class="account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'well well-sm'
        ]
    ]); ?>
    <div class="row">

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
        <div class="col-md-4">
            <?= $form->field($model, 'provider_code')->widget(Select2::classname(), [
                'data'=>\yii\helpers\ArrayHelper::map(\common\models\Account::getProviderCode(),'provider_code','provider_code'),
                'options' => [
                    'placeholder' => 'Tất cả',
                ],
                'pluginOptions'=>['allowClear'=>true],
            ])->label(Yii::t('account','provider_code')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model,'cnc')->textInput()?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model,'one_pay')->textInput()?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model,'yotel')->textInput()?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model,'cps')->textInput()?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
