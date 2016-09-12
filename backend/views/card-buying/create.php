<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CardBuying */

$this->title = 'Create Card Buying';
$this->params['breadcrumbs'][] = ['label' => 'Card Buyings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-buying-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
