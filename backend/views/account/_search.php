<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\AccountSearch */
/* @var $form yii\widgets\ActiveForm */
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
        <div class="col-md-3">
            <?php echo $form->field($model, 'id') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'username') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'email') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'phone_number') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'game_id') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'provider_code') ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'ref_code') ?>
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

    <?php // echo $form->field($model, 'username') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'partner_code') ?>

    <?php // echo $form->field($model, 'provider_code') ?>

    <?php // echo $form->field($model, 'ref_code') ?>

    <?php // echo $form->field($model, 'screen_name') ?>

    <?php // echo $form->field($model, 'fullname') ?>

    <?php // echo $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'email_token') ?>

    <?php // echo $form->field($model, 'email_token_expire') ?>

    <?php // echo $form->field($model, 'email_status') ?>

    <?php // echo $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'birthyear') ?>

    <?php // echo $form->field($model, 'gender') ?>

    <?php // echo $form->field($model, 'passport') ?>

    <?php // echo $form->field($model, 'phone_number') ?>

    <?php // echo $form->field($model, 'client_version') ?>

    <?php // echo $form->field($model, 'platform') ?>

    <?php // echo $form->field($model, 'os_type') ?>

    <?php // echo $form->field($model, 'login_times') ?>

    <?php // echo $form->field($model, 'last_login') ?>

    <?php // echo $form->field($model, 'last_login_ip_addr') ?>

    <?php // echo $form->field($model, 'date_created') ?>

    <?php // echo $form->field($model, 'date_modified') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'otp_status') ?>

    <?php // echo $form->field($model, 'ncoin') ?>

    <?php // echo $form->field($model, 'vcoin') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
