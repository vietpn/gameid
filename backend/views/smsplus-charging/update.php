<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SmsplusCharging */

$this->title = 'Update Smsplus Charging: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Smsplus Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="smsplus-charging-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
