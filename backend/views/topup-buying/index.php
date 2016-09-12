<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\TopupBuying;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TopupBuyingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trả thưởng TKĐT';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topup-buying-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $gridColumns = [
            ['class'=>'kartik\grid\SerialColumn'],

            'id',
            'account_id',
            'username',
            [
                'attribute'=>'cate_code',
                'value'=>function ($model, $key, $index, $widget) {
                    return isset(Yii::$app->params['pay365']['cate_code'][strtoupper($model->cate_code)]) ?
                        Yii::$app->params['pay365']['cate_code'][strtoupper($model->cate_code)] : strtoupper($model->cate_code);
                }
            ],
            'target',
            [
                'attribute'=>'amount',
                'format'=>['decimal', 0],
                'pageSummary'=>true,
                'pageSummaryFunc'=>GridView::F_SUM
            ],
            'msg:ntext',
            // 'data',
            // 'gdc_tran_id',
            // 'pay365_tran_id',
            [
                'class'=>'kartik\grid\BooleanColumn',
                'attribute'=>'charge_status',
                'vAlign'=>'middle',
            ],
            'date_created',
            // 'response_at',
            // 'response_code',
            // 'tran_status',
        ];

        $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
        $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
        $fileName = "Topup_buying_" . $startTimeFileName . $endTimeFileName;

        $amountProvider = new \yii\data\ActiveDataProvider($dataProvider);
        $amountProvider->setPagination(false);
        $totalAmount = TopupBuying::pageTotal($amountProvider->models, 'amount');
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
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-credit-card"></i> Topup Buying </h3>',
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
