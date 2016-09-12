<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TopupBuying */

$this->title = 'Create Topup Buying';
$this->params['breadcrumbs'][] = ['label' => 'Topup Buyings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topup-buying-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
