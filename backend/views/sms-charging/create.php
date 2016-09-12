<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsCharging */

$this->title = 'Create Sms Charging';
$this->params['breadcrumbs'][] = ['label' => 'Sms Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-charging-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
