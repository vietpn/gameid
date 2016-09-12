<?php

namespace common\models;

use common\utils\SecurityUtil;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "card_buying".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $username
 * @property string $cate_code
 * @property string $amount
 * @property integer $quantity
 * @property string $msg
 * @property string $list_cards
 * @property string $data
 * @property integer $charge_status
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
class CardBuying extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'card_buying';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['account_id', 'cate_code', 'amount', 'quantity'], 'required'],
            [['cate_code', 'amount', 'quantity'], 'required'],
            [['account_id', 'quantity', 'charge_status', 'tran_status'], 'integer'],
            [['msg', 'list_cards'], 'string'],
            [['date_created', 'response_at', 'date_modified', 'des', 'last_sync'], 'safe'],
            [['username', 'amount'], 'string', 'max' => 50],
            [['cate_code', 'data'], 'string', 'max' => 250],
            [['gdc_tran_id', 'pay365_tran_id'], 'string', 'max' => 150],
            [['response_code'], 'string', 'max' => 10],
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
            'amount' => 'Amount',
            'quantity' => 'Quantity',
            'msg' => 'Msg',
            'list_cards' => 'List Cards',
            'data' => 'Data',
            'charge_status' => 'Charge Status',
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
            // cache total buying
            if ($this->response_code == 1) {
                SecurityUtil::totalBuying($this->amount);
            }
            // format cate_code
            if ($this->isAttributeChanged('cate_code')) {
                $this->cate_code = strtoupper($this->cate_code);
            }
            return true;
        }
        return false;
    }

    public function buyingPay365()
    {
        // validate limit total buying
        if (Config::limitBuying() != 0 && SecurityUtil::totalBuying() >= Config::limitBuying()) {
            $this->addErrors([]);
            return false;
        }

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
            'agentcode' => Yii::$app->params['softpin']['agentcode'],
            'catecode' => $this->cate_code,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'tranid' => $this->gdc_tran_id,
        );

        $this->data = SecurityUtil::encryptData(yii::$app->params['softpin']['agentkey'], json_encode($data));

        if (!$this->save()) {
            return false;
        }

        try {
            $this->tran_status = Yii::$app->params['tran_status_process'];
            $client = new \SoapClient(yii::$app->params['softpin']['url']);
            $params = array(
                'agentCode' => yii::$app->params['softpin']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("BuyCard", array($params));

            if (isset($response->BuyCardResult)) {
                $response_json = json_decode($response->BuyCardResult, true);
                $this->response_at = date('Y-m-d H:i:s');
                // status ket thuc transaction
                $this->tran_status = Yii::$app->params['tran_status_end'];
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->pay365_tran_id = (!empty($response_json['tranid'])) ? $response_json['tranid'] : '';
                $this->list_cards = (!empty($response_json['listCards'])) ? $response_json['listCards'] : '';
                // ma hoa list cards
                $this->list_cards = SecurityUtil::encryptData(
                    Yii::$app->params['yotel']['secret_key'],
                    SecurityUtil::decryptData($this->list_cards, Yii::$app->params['softpin']['agentkey'])
                );
            }
            return ($this->save()) ? true : false;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return ($this->save()) ? true : false;
        }
    }

    public function buyingPay365NoAcc()
    {
        // validate limit total buying
        if (Config::limitBuying() != 0 && SecurityUtil::totalBuying() >= Config::limitBuying()) {
            $this->addErrors([]);
            return false;
        }

        // validate model
        if (!$this->validate()) {
            return false;
        }

        // find account id if existing
        $account = Account::find()->where(['username' => $this->username]);
        if ($account->exists()) {
            $this->account_id = $account->all()[0]->id;
        }

        // tao gdc tran id
        $this->gdc_tran_id = SecurityUtil::getTransactionGDC();
        // status khoi tao transaction
        $this->tran_status = Yii::$app->params['tran_status_init'];

        $data = array(
            'agentcode' => Yii::$app->params['softpin']['agentcode'],
            'catecode' => $this->cate_code,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'tranid' => $this->gdc_tran_id,
        );

        $this->data = SecurityUtil::encryptData(yii::$app->params['softpin']['agentkey'], json_encode($data));

        if (!$this->save()) {
            return false;
        }

        try {
            $this->tran_status = Yii::$app->params['tran_status_process'];
            $client = new \SoapClient(yii::$app->params['softpin']['url']);
            $params = array(
                'agentCode' => yii::$app->params['softpin']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("BuyCard", array($params));

            if (isset($response->BuyCardResult)) {
                $response_json = json_decode($response->BuyCardResult, true);
                $this->response_at = date('Y-m-d H:i:s');
                // status ket thuc transaction
                $this->tran_status = Yii::$app->params['tran_status_end'];
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->pay365_tran_id = (!empty($response_json['tranid'])) ? $response_json['tranid'] : '';
                $this->list_cards = (!empty($response_json['listCards'])) ? $response_json['listCards'] : '';
                // ma hoa list cards
                $this->list_cards = SecurityUtil::encryptData(
                    Yii::$app->params['yotel']['secret_key'],
                    SecurityUtil::decryptData($this->list_cards, Yii::$app->params['softpin']['agentkey'])
                );
            }
            return ($this->save()) ? true : false;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
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
            $client = new \SoapClient(yii::$app->params['softpin']['url']);
            $params = array(
                'agentCode' => yii::$app->params['softpin']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("GetCard", array($params));
            if (isset($response->GetCardResult)) {
                $response_json = json_decode($response->GetCardResult, true);
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->list_cards = (!empty($response_json['listCards'])) ? $response_json['listCards'] : '';
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
