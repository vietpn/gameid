<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'key',
            'description',
            [
                'attribute' => 'value',
                'format'=>['decimal', 0],
            ],
            //'date_created',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'delete' => false,
                ]
            ],
        ],
    ]); ?>
</div>
