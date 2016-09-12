<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CardCharging */

$this->title = 'Create Card Charging';
$this->params['breadcrumbs'][] = ['label' => 'Card Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-charging-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
