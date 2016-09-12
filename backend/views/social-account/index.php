<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SocialAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Social Accounts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="social-account-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'account_id',
            'username',
            'provider',
            'client_id',
            'data:ntext',
            'code',
            'email:email',
            'date_created',
        ];

        $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) . "_" : "";
        $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
        $fileName = "social_account_" . $startTimeFileName . $endTimeFileName;
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-credit-card"></i> Social Account </h3>',
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
