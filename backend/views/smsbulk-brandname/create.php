<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsbulkBrandname */

$this->title = 'Create Smsbulk Brandname';
$this->params['breadcrumbs'][] = ['label' => 'Smsbulk Brandnames', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smsbulk-brandname-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
