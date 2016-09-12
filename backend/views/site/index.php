<?php
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;

$this->title = 'GDC-ID';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $startTimeFileName = isset($searchModel->startTime) ? str_replace("/", "", $searchModel->startTime) : "";

    $endTimeFileName = isset($searchModel->endTime) ? str_replace("/", "", $searchModel->endTime) : "";
    $fileName = "Bao_cao_doanh_thu_theo_doi_tac" . $startTimeFileName . '_' . $endTimeFileName;
    $filename_gdc = 'Báo cáo doanh thu từ ' . $startTimeFileName . $endTimeFileName;
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success" role="alert">
                <strong>Dự đoán doanh thu trong
                    tháng <?= date('m-Y') ?> : </strong> <?php echo(isset($expectedRevenue) ? number_format($expectedRevenue, 0, '.', ',') : 0) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <h3 class="panel-title"><i class="glyphicon glyphicon-th"></i> Báo cáo doanh thu Từ ngày <strong
                                style="font-size: 13px"><?= $startTimeFileName; ?></strong> đến Ngày <strong
                                style="font-size: 13px"><?= $endTimeFileName ?></strong>
                        </h3>
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover kv-grid-table kv-table-wrap">
                        <thead>
                        <tr>
                            <th class="text-center"><?php echo Yii::t('app', 'total_revenus') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_cnc') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_one_pay') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_yotel') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_CPS') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_bonus_payment') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_gdc_before') ?></th>
                            <th class="text-center"><?php echo Yii::t('app', 'total_gdc_after') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dataProviderSales->getModels() as $key => $item): ?>
                            <tr>
                                <td class="text-center"><?= (isset($item['total_revenus']) && $item['total_revenus'] > 0) ? number_format($item['total_revenus'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_cnc']) && $item['total_cnc'] > 0) ? number_format($item['total_cnc'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_one_pay']) && $item['total_one_pay'] > 0) ? number_format($item['total_one_pay'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_yotel']) && $item['total_yotel'] > 0) ? number_format($item['total_yotel'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_CPS']) && $item['total_CPS'] > 0) ? number_format($item['total_CPS'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_bonus_payment']) && $item['total_bonus_payment'] > 0) ? number_format($item['total_bonus_payment'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_gdc_before']) && $item['total_gdc_before'] > 0) ? number_format($item['total_gdc_before'], 0, '.', ',') : 0 ?></td>
                                <td class="text-center"><?= (isset($item['total_gdc_after']) && $item['total_gdc_after'] > 0) ? number_format($item['total_gdc_after'], 0, '.', ',') : 0 ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
                <div class="panel-footer"></div>
            </div>

        </div>
    </div>
    <?php
    $gridColumns = [
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute' => 'provider_code',
            'label' => Yii::t('account','provider_code'),
        ],
        [
            'attribute' => 'total_sms',
            'label' => Yii::t('account','total_sms'),
            'format'    => ['decimal', 0],
            'pageSummary' => true,
        ],
        [
            'attribute' => 'total_card',
            'label' => Yii::t('account','total_card'),
            'format'    => ['decimal', 0],
            'pageSummary' => true,
        ],
        [
            'attribute' => 'total_all',
            'label' => Yii::t('account','total_all'),
            'format'    => ['decimal', 0],
            'pageSummary' => true,
        ],
    ];


    ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-user"></i> Báo cáo doanh thu theo đối tác </h3>',
        ],
        'columns' => $gridColumns,
        'showPageSummary' => true,
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
        'options' => ['class' => 'grid-view'],
        'tableOptions' => ['class'=> 'table table-striped table-bordered table-hover'],
        'layout'=> '{summary}<div class="table-scrollable">{items}</div><div class="pull-left">{pager}</div>',
    ]); ?>
</div>
