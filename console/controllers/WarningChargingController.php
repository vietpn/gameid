<?php

namespace console\controllers;

use common\models\CardBuying;
use common\models\CardCharging;
use common\models\SmsbulkBrandname;
use yii\console\Controller;
use yii\base\Exception;
use yii;

/**
 * Tien trinh canh bao he thong khi khong co giao dich hoac co loi khi giao dich
 *
 * Class WarningChargingController
 * @package console\controllers
 */
class WarningChargingController extends Controller
{
    /**
     * Cảnh báo lỗi giao dịch nạp thẻ trong 10 phút
     */
    public function actionCncCardCharging()
    {
        // Lấy danh sách giao dịch trong 10 phút --> nếu có lỗi, đẩy cảnh báo
        $query = CardCharging::find();
        $query->select("id, username, cate_code, card_code, card_serial, tran_status, charge_status, response_code, msg, date_created");
        $query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD(NOW(), INTERVAL -10 minute)")]);
        //$query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD('2016-07-14 11:10:24', INTERVAL -10 minute)")]);
        $rows = $query->asArray()->all();
        if (count($rows) > 0) {
            $totalErrors = 0;
            $errorInfo = [];
            $ignoreErrors = ['-10', '-9', '-1', '1', '-2', '-4', '-5', '-6', '-16', '-13'];
            foreach ($rows as $row) {
                if ($row['tran_status'] != Yii::$app->params['tran_status_init'] && !in_array($row['response_code'], $ignoreErrors)) { // Loi
                    $totalErrors += 1;
                    $errorInfo[] = $row;
                }
            }

            // Gui email neu co error
            if ($totalErrors > 0) {
                try {
                    Yii::$app->mailer->compose(
                        ['html' => 'emailAlertErrorCardCharging-html', 'text' => 'emailAlertErrorCardCharging-text'],
                        [
                            'errorInfo' => $errorInfo,
                            "totalErrors" => $totalErrors,
                            "count" => count($rows)
                        ])
                        ->setFrom(Yii::$app->params['email']['support'])
                        ->setTo(Yii::$app->params['alertEmail'])
                        ->setSubject('[Error-Game] Card charging - ' . date('Y-m-d H:i:s'))
                        ->send();
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                    return false;
                }
            }

            // Neu so loi > 10, gui sms canh bao
            if ($totalErrors > 10) {
                $this->sendAlertSms("He thong co loi nap the");
            }

        }

        exit;
    }

    /**
     * Cảnh báo nếu không có giao dịch nạp thẻ trong 1h
     */
    public function actionNoneCncCardCharging()
    {
        // Lấy danh sách giao dịch trong 1 h --> nếu khong co giao dich, đẩy cảnh báo
        $query = CardCharging::find();
        $query->select("id, username, cate_code, card_code, card_serial, tran_status, charge_status, response_code, msg, date_created");
        $query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD(NOW(), INTERVAL -1 hour)")]);
        //$query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD('2016-07-14 11:10:24', INTERVAL -10 minute)")]);
        $rows = $query->asArray()->all();

        if (count($rows) == 0) {
            try {
                Yii::$app->mailer->compose(
                    ['html' => 'emailAlertWarning-html', 'text' => 'emailAlertWarning-text'],
                    ['code' => 'Game', 'desc' => '[warning] Khong co giao dich nap the nao trong 1 tieng'])
                    ->setFrom(Yii::$app->params['email']['support'])
                    ->setTo(Yii::$app->params['alertEmail'])
                    ->setSubject('[warning-Game] Card charging - ' . date('Y-m-d H:i:s'))
                    ->send();
            } catch (Exception $e) {
                Yii::error($e->getMessage());
                return false;
            }
        }

        exit;
    }

    /**
     * Gửi tin nhắn báo lỗi SMS
     * @param $message
     */
    private function sendAlertSms($message)
    {
        foreach (Yii::$app->params['alertSms'] as $phone) {
            $sms = new SmsbulkBrandname();
            $sms->msisdn = $phone;
            $sms->alias = 'iCash';
            $sms->message = '[AlertSms-Game]: ' . $message;
            if ($sms->validate()) {
                $sms->save();
                $sms->addToQueue();
            }
        }

        exit;
    }

    /**
     * Cảnh báo lỗi giao dịch trả thưởng trong 10 phút,
     */
    public function actionCncCardBuying()
    {
        // Lấy danh sách giao dịch trong 10 phút --> nếu có lỗi, đẩy cảnh báo
        $query = CardBuying::find();
        $query->select("id, username, cate_code, tran_status, charge_status, response_code, msg, date_created");
        $query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD(NOW(), INTERVAL -10 minute)")]);
        //$query->andWhere(['>=', 'date_created', new yii\db\Expression("DATE_ADD('2016-08-16 13:19:17', INTERVAL -10 minute)")]);
        $rows = $query->asArray()->all();
        if (count($rows) > 0) {
            $totalErrors = 0;
            $errorInfo = [];
            foreach ($rows as $row) {
                if ($row['tran_status'] != Yii::$app->params['tran_status_init'] && $row['charge_status'] == 0) { // Loi
                    $totalErrors += 1;
                    $errorInfo[] = $row;
                }
            }

            // Gui email neu co error
            if ($totalErrors > 0) {
                try {
                    Yii::$app->mailer->compose(
                        ['html' => 'emailAlertErrorCardBuying-html', 'text' => 'emailAlertErrorCardBuying-text'],
                        [
                            'errorInfo' => $errorInfo,
                            "totalErrors" => $totalErrors,
                            "count" => count($rows)
                        ])
                        ->setFrom(Yii::$app->params['email']['support'])
                        ->setTo(Yii::$app->params['alertEmail'])
                        ->setSubject('[Error-Game] Card buying - ' . date('Y-m-d H:i:s'))
                        ->send();
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                    return false;
                }
            }

            // Neu so loi > 10, gui sms canh bao
            if ($totalErrors > 10) {
                $this->sendAlertSms("He thong co loi tra thuong");
            }

        }

        exit;
    }
}