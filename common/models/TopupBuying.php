<?php

namespace common\models;

use common\utils\SecurityUtil;
use Yii;

/**
 * This is the model class for table "topup_buying".
 *
 * @property integer $id
 * @property integer $account_id
 * @property string $username
 * @property string $cate_code
 * @property string $target
 * @property string $amount
 * @property string $msg
 * @property string $data
 * @property integer $charge_status
 * @property string $gdc_tran_id
 * @property string $pay365_tran_id
 * @property integer $tran_status
 * @property string $date_created
 * @property string $response_at
 * @property string $response_code
 */
class TopupBuying extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'topup_buying';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'cate_code', 'amount'], 'required'],
            [['account_id', 'charge_status', 'tran_status'], 'integer'],
            [['msg'], 'string'],
            [['date_created', 'response_at'], 'safe'],
            [['username', 'target', 'amount'], 'string', 'max' => 50],
            [['cate_code', 'data'], 'string', 'max' => 250],
            [['gdc_tran_id', 'pay365_tran_id'], 'string', 'max' => 150],
            [['response_code'], 'string', 'max' => 10],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
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
            'target' => 'Target',
            'amount' => 'Amount',
            'msg' => 'Msg',
            'data' => 'Data',
            'charge_status' => 'Charge Status',
            'gdc_tran_id' => 'Gdc Tran ID',
            'pay365_tran_id' => 'Pay365 Tran ID',
            'tran_status' => 'Tran Status',
            'date_created' => 'Date Created',
            'response_at' => 'Response At',
            'response_code' => 'Response Code',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // cache total buying
            if ($this->response_code == 1){
                SecurityUtil::totalBuying($this->amount);
            }
            return true;
        }
        return false;
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
        if (empty($account) || $account->otp_status != Yii::$app->params['status_success']) {
            return false;
        }

        $this->target = $account->phone_number;
        $this->username = $account->username;

        // tao gdc tran id
        $this->gdc_tran_id = SecurityUtil::getTransactionGDC();
        // status khoi tao transaction
        $this->tran_status = Yii::$app->params['tran_status_init'];

        $data = array(
            'agentcode' => Yii::$app->params['softpin']['agentcode'],
            'catecode' => $this->cate_code,
            'amount' => $this->amount,
            'target' => $this->target,
            'tranid' => $this->gdc_tran_id,
        );

        $this->data = SecurityUtil::encryptData(Yii::$app->params['softpin']['agentkey'], json_encode($data));

        if (!$this->save()) {
            return false;
        }

        try {
            $this->tran_status = Yii::$app->params['tran_status_process'];
            $client = new \SoapClient(Yii::$app->params['softpin']['url']);
            $params = array(
                'agentCode' => Yii::$app->params['softpin']['agentcode'],
                'data' => $this->data
            );
            $response = $client->__soapCall("Topup", array($params));

            if (isset($response->TopupResult)) {
                $response_json = json_decode($response->TopupResult, true);
                $this->response_at = date('Y-m-d H:i:s');
                // status ket thuc transaction
                $this->tran_status = Yii::$app->params['tran_status_end'];
                $this->response_code = (!empty($response_json['code'])) ? $response_json['code'] : '';
                $this->charge_status = ($this->response_code == 1) ? Yii::$app->params['status_success'] : Yii::$app->params['status_fail'];
                $this->msg = (!empty($response_json['msg'])) ? $response_json['msg'] : '';
                $this->pay365_tran_id = (!empty($response_json['tranid'])) ? $response_json['tranid'] : '';
            }
            return ($this->save()) ? true : false;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return ($this->save()) ? true : false;
        }
    }
}
