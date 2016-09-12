<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SmsChargingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Chargings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-charging-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'account_id',
            'username',
            //'ref_code',
            'command',
            'mo_message',
            // 'msisdn',
            // 'request_id',
            // 'request_time',
            'short_code',
            // 'signature',
            // 'response_sms',
            // 'response_status',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'charge_status',
                'vAlign'=>'middle',
            ],
            'date_created',
        ];

        $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
        $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
        $fileName = "Sms_charging_" . $startTimeFileName . $endTimeFileName;
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-credit-card"></i> Sms Charging </h3>',
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
