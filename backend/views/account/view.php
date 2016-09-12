<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = 'Acount ID: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="well">
        <?= Html::a('<span>Nạp thẻ</span>', ['/card-charging/index', 'CardChargingSearch[account_id]' => $model->id], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('<span>Nạp SMS</span>', ['/smsplus-charging/index', 'SmsplusChargingSearch[account_id]' => $model->id], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('<span>Trả thưởng thẻ ĐT</span>', ['/card-buying/index', 'CardBuyingSearch[account_id]' => $model->id], ['class'=>'btn btn-primary']) ?>
        <?= Html::a('<span>Trả thưởng TKĐT</span>', ['/topup-buying/index', 'TopupBuyingSearch[account_id]' => $model->id], ['class'=>'btn btn-primary']) ?>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            //'password_hash',
            'partner_code',
            'provider_code',
            'ref_code',
            'screen_name',
            'fullname',
            'avatar',
            'address',
            'email:email',
            'email_token:email',
            'email_token_expire:email',
            'email_status:email',
            'birthday',
            'birthyear',
            'gender',
            'passport',
            'phone_number',
            'client_version',
            'platform',
            'os_type',
            'login_times:datetime',
            'last_login',
            'last_login_ip_addr',
            'date_created',
            'date_modified',
            'status',
            'otp_status',
            'game_id',
            'ncoin',
            'vcoin',
        ],
    ]) ?>

</div>
