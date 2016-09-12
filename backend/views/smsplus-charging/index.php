<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\SmsplusCharging;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SmsplusChargingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Smsplus Chargings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="smsplus-charging-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php
        $gridColumns = [
            ['class'=>'kartik\grid\SerialColumn'],

            'id',
            'account_id',
            'game_id',
            'username',
            //'ref_code',
            'msisdn',
            [
                'attribute'=>'telco',
                'value'=>function ($model, $key, $index, $widget) {
                    return isset(Yii::$app->params['1pay']['telco'][$model->telco]) ?
                        Yii::$app->params['1pay']['telco'][$model->telco] : $model->telco;
                }
            ],
            [
                'attribute'=>'amount',
                'format'=>['decimal', 0],
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM
            ],
            // 'command_code',
            // 'error_code',
            // 'error_message',
            'mo_message',
            // 'request_id',
            // 'signature',
            // 'response_sms',
            // 'response_status',
            // 'response_game',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'response_game_status',
                'vAlign'=>'middle',
            ],
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'charge_status',
                'vAlign'=>'middle',
            ],
            'request_time',
            'date_created',
        ];

        $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
        $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
        $fileName = "Smsplus_charging_" . $startTimeFileName . $endTimeFileName;

        $amountProvider = new \yii\data\ActiveDataProvider($dataProvider);
        $amountProvider->setPagination(false);
        $totalAmount = SmsplusCharging::pageTotal($amountProvider->models, 'amount');
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => ['id' => 'kv-pjax-container'],
            'beforeGrid' => '<h3>Total Amount = ' . Yii::$app->formatter->asDecimal($totalAmount) . '</h3>'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-credit-card"></i> Smsplus Charging </h3>',
        ],
        'columns' => $gridColumns,
        'showPageSummary'=>true,
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
