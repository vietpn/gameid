<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TopupBuying */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Topup Buyings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topup-buying-view">

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
            'target',
            'amount',
            'msg:ntext',
            'data',
            'gdc_tran_id',
            'pay365_tran_id',
            'date_created',
            'response_at',
            'response_code',
            'tran_status',
            'charge_status',
        ],
    ]) ?>

</div>
