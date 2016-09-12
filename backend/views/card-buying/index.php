<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\CardBuying;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CardBuyingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trả thưởng thẻ ĐT';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-buying-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $gridColumns = [
        ['class' => 'kartik\grid\SerialColumn'],

        'id',
        'account_id',
        'username',
        [
            'attribute' => 'cate_code',
            'value' => function ($model, $key, $index, $widget) {
                return isset(Yii::$app->params['pay365']['cate_code'][strtoupper($model->cate_code)]) ?
                    Yii::$app->params['pay365']['cate_code'][strtoupper($model->cate_code)] : strtoupper($model->cate_code);
            }
        ],
        [
            'attribute' => 'amount',
            'format' => ['decimal', 0],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],
        'quantity',
        'msg:ntext',
        // 'data',
        //'list_cards:ntext',
        [
            'attribute' => 'list_cards',
            'format' => 'raw',
            'label' => 'Cards Serial',
            'value' => function ($model, $key, $index, $widget) {
                $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->getId());
                $jsonCards = json_decode(\common\utils\SecurityUtil::decryptData($model->list_cards, Yii::$app->params['yotel']['secret_key']), true);
                $html = '';
                if (!empty($jsonCards)) {
                    foreach ($jsonCards as $card) {
                        if (!empty($card['serial'])) {
                            $html .= '<p>Serial: ' . $card['serial'] . '</p>';
                        }
                        if (!empty($role['Game Topup']) && !empty($card['pin'])) {
                            $html .= '<p>Pin: ' . $card['pin'] . '</p>';
                        }
                    }
                }
                return $html;
            }
        ],
        //'charge_status',
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'charge_status',
            'vAlign' => 'middle',
        ],
        // 'gdc_tran_id',
        // 'pay365_tran_id',
        // 'tran_status',
        'response_code',
        'des',
        'date_created',
        'date_modified',
        // 'response_at',
    ];

    $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
    $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
    $fileName = "Card_buying_" . $startTimeFileName . $endTimeFileName;

    $amountProvider = new \yii\data\ActiveDataProvider($dataProvider);
    $amountProvider->setPagination(false);
    $totalAmount = CardBuying::pageTotal($amountProvider->models, 'amount');
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => ['id' => 'kv-pjax-container'],
            'beforeGrid' => '<h3> Total Amount = ' . Yii::$app->formatter->asDecimal($totalAmount) . '</h3>'
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-credit-card"></i> Card Buying </h3>',
        ],
        'columns' => $gridColumns,
        'showPageSummary' => true,
        'export' => [
            'showConfirmAlert' => false,
            'target' => GridView::TARGET_BLANK,
        ],
        'exportConfig' => [
            GridView::CSV => [
                'filename' => $fileName,
            ],
            GridView::PDF => [
                'filename' => $fileName,
            ],
            GridView::EXCEL => [
                'filename' => $fileName,
            ],
        ],
    ]); ?>
</div>
