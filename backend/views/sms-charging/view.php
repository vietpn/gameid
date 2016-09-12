<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SmsCharging */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-charging-view">

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
            'ref_code',
            'command',
            'mo_message',
            'msisdn',
            'request_id',
            'request_time',
            'short_code',
            'signature',
            'response_sms',
            'response_status',
            'charge_status',
            'date_created',
        ],
    ]) ?>

</div>
