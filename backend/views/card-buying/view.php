<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CardBuying */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Card Buyings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-buying-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'account_id',
            'username',
            'cate_code',
            'amount',
            'quantity',
            'msg:ntext',
            'data',
            'list_cards:ntext',
            'charge_status',
            'gdc_tran_id',
            'pay365_tran_id',
            'tran_status',
            'date_created',
            'response_at',
            'response_code',
        ],
    ]) ?>

</div>
