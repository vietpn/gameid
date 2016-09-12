<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SmsbulkBrandname */

$this->title = 'Update Smsbulk Brandname: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Smsbulk Brandnames', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="smsbulk-brandname-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
