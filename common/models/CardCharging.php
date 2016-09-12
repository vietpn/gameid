<?php

namespace common\models;

use common\utils\SecurityUtil;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "card_charging".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $username
 * @property string $cate_code
 * @property string $card_code
 * @property string $card_serial
 * @property string $data
 * @property integer $charge_status
 * @property string $amount
 * @property string $msg
 * @property string $gdc_tran_id
 * @property string $pay365_tran_id
 * @property integer $tran_status
 * @property string $response_at
 * @property string $response_code
 * @property string $des
 * @property integer $last_sync
 * @property string $date_created
 * @property string $date_modified
 */
class CardCharging extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'card_charging';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['account_id', 'cate_code', 'card_code', 'card_serial'], 'required'],
            [['cate_code', 'card_code', 'card_serial'], 'required'],
            // validate card_code and card_serial
            [['card_code', 'card_serial'], 'match', 'pattern' => '/^[a-zA-Z0-9]*$/'],
            [['account_id', 'charge_status', 'tran_status'], 'integer'],
            [['msg'], 'string'],
            [['date_created', 'response_at', 'date_modified', 'des', 'last_sync'], 'safe'],
            [['username'], 'string', 'max' => 50],
            [['cate_code', 'card_code', 'card_serial', 'data', 'amount'], 'string', 'max' => 250],
            [['gdc_tran_id', 'pay365_tran_id'], 'string', 'max' => 150],
            [['response_code'], 'string', 'max' => 10],
            [['gdc_tran_id'], 'unique'],
            //[['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'username' => 'Username',
            'cate_code' => 'Cate Code',
            'card_code' => 'Card Code',
            'card_serial' => 'Card Serial',
            'data' => 'Data',
            'charge_status' => 'Charge Status',
            'amount' => 'Amount',
            'msg' => 'Msg',
            'gdc_tran_id' => 'Gdc Tran ID',
            'pay365_tran_id' => 'Pay365 Tran ID',
            'tran_status' => 'Tran Status',
            'response_at' => 'Response At',
            'response_code' => 'Response Code',
            'des' => 'Description',
            'last_sync' => 'Last Sync',
            'date_created' => 'Date Created',
            'date_modified' => 'Date Modified',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_created', 'date_modified'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_modified'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset($fields['data']);
        unset($fields['pay365_tran_id']);

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // format cate_code
            if ($this->isAttributeChanged('cate_code')) {
                $this->cate_code = strtoupper($this->cate_code);
            }
            return true;
        }
        return false;
    }

    /**
     * call charging to pay365
     * @return bool
     */
    public function chargingPay365()
    {
        // validate model
        if (!$this->validate()) {
            return false;
        }

        // find account by account_id
        $account = Account::findOne($this->account_id);
        if (empty($account)) {
            return false;
        }
        $this->username = $account->username;

        // tao gdc tran id
        $this->gdc_tran_id = SecurityUtil::getTransactionGDC();
        // status khoi tao transaction
        $this->tran_status = Yii::$app->params['tran_status_init'];

        $data = array(
            'agentcode' => Yii::$app->params['pay365']['agentcode'],
            'catecode' => $this->cate_code,
            'cardcode' => $this->card_code,
            'cardserial' => $this->card_serial,
            'tranid' => $this->gdc_tran_id,
        );

        $this->data = SecurityUtil::encryptData(yii::$app->params['pay365']['agentkey'], json_encode($data));

        if (!$this->save()) {
            return false;
        }

        try {
            // status dang xu ly transaction
            $this->tran_status = Yii::$app->params['tran_status_process'];
            $client = new \SoapClient(yii::$app->params['pay365']['url']);
            $params = array(
                'agentCode' => yii::$app->params['pay365']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("UseCard", array($params));

            if (isset($response->UseCardResult)) {
                $response_json = json_decode($response->UseCardResult, true);
                $this->response_at = date('Y-m-d H:i:s');
                // status ket thuc transaction
                $this->tran_status = Yii::$app->params['tran_status_end'];
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->amount = (!empty($response_json['amount'])) ? $response_json['amount'] : '';
                $this->pay365_tran_id = (!empty($response_json['tranid'])) ? $response_json['tranid'] : '';
            }
            return ($this->save()) ? true : false;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return ($this->save()) ? true : false;
        }
    }

    /**
     * Call to charging to pay365 with out account GDC
     * @return bool
     */
    public function chargingPay365NoId()
    {
        // validate model
        if (!$this->validate()) {
            return false;
        }

        // thẻ lỗi nạp quá 3 lần bị khóa 30'
        if (!SecurityUtil::limitTimeCharging($this->username, $this->card_code, $this->card_serial)) {
            $this->addErrors(['limit_time_charing' => 'Giao dịch thẻ lỗi sau 30 phút']);
            return false;
        }

        // tao gdc tran id
        $this->gdc_tran_id = SecurityUtil::getTransactionGDC();
        // status khoi tao transaction
        $this->tran_status = Yii::$app->params['tran_status_init'];

        $data = array(
            'agentcode' => Yii::$app->params['pay365']['agentcode'],
            'catecode' => $this->cate_code,
            'cardcode' => $this->card_code,
            'cardserial' => $this->card_serial,
            'tranid' => $this->gdc_tran_id,
        );

        $this->data = SecurityUtil::encryptData(yii::$app->params['pay365']['agentkey'], json_encode($data));

        if (!$this->save()) {
            return false;
        }

        // find account id if existing
        $account = Account::find()->where(['username' => $this->username]);
        if ($account->exists()) {
            $this->account_id = $account->all()[0]->id;
        }

        try {
            // status dang xu ly transaction
            $this->tran_status = Yii::$app->params['tran_status_process'];
            $client = new \SoapClient(yii::$app->params['pay365']['url']);
            $params = array(
                'agentCode' => yii::$app->params['pay365']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("UseCard", array($params));

            if (isset($response->UseCardResult)) {
                $response_json = json_decode($response->UseCardResult, true);
                $this->response_at = date('Y-m-d H:i:s');
                // status ket thuc transaction
                $this->tran_status = Yii::$app->params['tran_status_end'];
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->amount = (!empty($response_json['amount'])) ? $response_json['amount'] : '';
                $this->pay365_tran_id = (!empty($response_json['tranid'])) ? $response_json['tranid'] : '';
            }
            if ($this->charge_status == Yii::$app->params['status_fail']) {
                SecurityUtil::countLimitTimeCharging($this->username, $this->card_code, $this->card_serial);
            }
            return ($this->save()) ? true : false;
        } catch (Exception $e) {
            //Yii::error($e->getMessage());
            Yii::info($e->getMessage(), Yii::$app->params['key_log_card_charging']);
            return ($this->save()) ? true : false;
        }
    }

    /**
     * Kiểm tra giao dịch nghi vấn
     */
    public function checkCharging()
    {
        try {
            // status dang xu ly transaction
            $client = new \SoapClient(yii::$app->params['pay365']['url']);
            $params = array(
                'agentCode' => yii::$app->params['pay365']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("GetTransaction", array($params));
            if (isset($response->GetTransactionResult)) {
                $response_json = json_decode($response->GetTransactionResult, true);
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->amount = (!empty($response_json['amount'])) ? $response_json['amount'] : '';
                $this->last_sync = $this->last_sync + 1;
                $this->des = 'Kiểm tra giao dịch nghi vấn';
            }
            return ($this->update()) ? true : false;
        } catch (Exception $e) {
            //Yii::error($e->getMessage());
            Yii::info($e->getMessage(), Yii::$app->params['key_log_card_charging']);
            return false;
        }
    }
}
