<?php

namespace console\controllers;

use common\models\CardBuying;
use common\models\CardCharging;
use yii\console\Controller;
use yii\base\Exception;
use yii;

/**
 *
 * @package console\controllers
 */
class CheckChargingController extends Controller
{
    /**
     * Kiểm tra các giao dịch nạp thẻ nghi vấn
     */
    public function actionCncCardCharging()
    {
        Yii::info("Check card charging\n", 'CHECK-CARD-CHARGING');
        $query = CardCharging::find();
        //$query->select("id, response_code, last_sync, data, date_created");
        $query->andWhere(['>=', 'date_created', date("Y-m-d")]);
        // tìm nhứng giao dịch đã check < 20 lần
        $query->andWhere(['<=', 'last_sync', 20]);
        // tìm những giao dịch nghi vấn
        $query->andWhere(['=', 'response_code', -7]);

        $rows = $query->all();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                Yii::info("Check card charging: ". $row->id ."\n", 'CHECK-CARD-CHARGING');
                $row->checkCharging();
            }
        }

        exit;
    }


    /**
     * Kiểm tra các giao dịch nạp thẻ nghi vấn
     */
    public function actionCncCardBuying()
    {
        Yii::info("Check card buying\n", 'CHECK-CARD-BUYING');
        $query = CardBuying::find();
        //$query->select("id, response_code, last_sync, data, date_created");
        $query->andWhere(['>=', 'date_created', date("Y-m-d")]);
        // tìm nhứng giao dịch đã check < 20 lần
        $query->andWhere(['<=', 'last_sync', 20]);
        // tìm những giao dịch nghi vấn
        $query->andWhere(['=', 'response_code', -7]);

        $rows = $query->all();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                Yii::info("Check card buying: ". $row->id ."\n", 'CHECK-CARD-BUYING');
                $row->checkCharging();
            }
        }

        exit;
    }
}