<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SmsplusCharging */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Smsplus Chargings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smsplus-charging-view">

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
            'msisdn',
            'amount',
            'command_code',
            'error_code',
            'error_message',
            'mo_message',
            'request_id',
            'request_time',
            'signature',
            'response_sms',
            'response_status',
            'response_game',
            'response_game_status',
            'charge_status',
            'date_created',
        ],
    ]) ?>

</div>
