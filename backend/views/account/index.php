<?php

//use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            // 'password_hash',
            // 'partner_code',
            'provider_code',
            'ref_code',
            // 'screen_name',
            'fullname',
            // 'avatar',
            'address',
            'email:email',
            // 'email_token:email',
            // 'email_token_expire:email',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'email_status',
                'vAlign'=>'middle',
            ],
            'birthday',
            // 'birthyear',
            'gender',
            // 'passport',
            'phone_number',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'otp_status',
                'vAlign'=>'middle',
            ],
            // 'client_version',
            // 'platform',
            // 'os_type',
            // 'login_times:datetime',
            // 'last_login',
            // 'last_login_ip_addr',
            'game_id',
            'date_created',
            // 'date_modified',
            // 'status',
            // 'ncoin',
            // 'vcoin',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'delete' => false,
                    'update' => false,
                ]
            ],
        ];

        $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
        $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
        $fileName = "Account_" . $startTimeFileName . $endTimeFileName;
    ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> Account </h3>',
        ],
        'columns' => $gridColumns,
        'export'=>[
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
        ],
        'exportConfig' => [
            GridView::CSV=>[
                'filename' => $fileName,
            ],
            GridView::PDF=>[
                'filename' => $fileName,
            ],
            GridView::EXCEL=>[
                'filename' => $fileName,
            ],
        ],
    ]); ?>
</div>
