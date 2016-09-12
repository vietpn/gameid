<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsplusCharging */

$this->title = 'Create Smsplus Charging';
$this->params['breadcrumbs'][] = ['label' => 'Smsplus Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smsplus-charging-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
