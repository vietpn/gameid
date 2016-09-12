<?php

namespace backend\models;

use common\models\Account;
use common\models\CardBuying;
use common\models\CardCharging;
use common\models\SmsplusCharging;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * AccountSearch represents the model behind the search form about `common\models\Account`.
 */
class AccountSearch extends Account
{
    public $cnc;
    public $one_pay;
    public $yotel;
    public $cps;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'email_token_expire', 'email_status', 'birthyear', 'gender', 'login_times', 'status', 'otp_status', 'ncoin', 'vcoin', 'game_id'], 'integer'],
            [['username', 'password_hash', 'partner_code', 'provider_code', 'ref_code', 'screen_name', 'fullname', 'avatar', 'address', 'email', 'email_token', 'birthday', 'passport', 'phone_number', 'client_version', 'platform', 'os_type', 'last_login', 'last_login_ip_addr', 'date_created', 'date_modified'], 'safe'],
            [['startTime', 'endTime'], 'safe'],
            [['cnc', 'one_pay', 'yotel', 'cps'], 'required'],
            [['cnc', 'one_pay', 'yotel', 'cps'], 'number'],
            [['cnc', 'one_pay', 'yotel', 'cps'], 'number', 'min' => 1],
            [['cnc', 'one_pay', 'yotel', 'cps'], 'number', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cnc' => \Yii::t('app', 'cnc'),
            'one_pay' => \Yii::t('app', 'one_pay'),
            'yotel' => \Yii::t('app', 'yotel'),
            'cps' => \Yii::t('app', 'cps'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Account::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date_created' => SORT_DESC]],
        ]);

        $this->load($params);

        $session = Yii::$app->session;

        if (!empty($this->startTime)) {
            $session->set('cur_start_time', $this->startTime);
        }

        if (!empty($this->endTime)) {
            $session->set('cur_end_time', $this->endTime);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // search by start_time and end_time
        $startTime = \DateTime::createFromFormat('d-m-Y', $this->startTime);
        $endTime = \DateTime::createFromFormat('d-m-Y', $this->endTime);

        if ($startTime) {
            $query->andWhere(['>=', 'date_created', $startTime->format('Y-m-d 00:00:00')]);
        }

        if ($endTime) {
            $query->andWhere(['<=', 'date_created', $endTime->format('Y-m-d 23:59:59')]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'email_token_expire' => $this->email_token_expire,
            'email_status' => $this->email_status,
            'birthday' => $this->birthday,
            'birthyear' => $this->birthyear,
            'gender' => $this->gender,
            'login_times' => $this->login_times,
            'last_login' => $this->last_login,
            'date_created' => $this->date_created,
            'date_modified' => $this->date_modified,
            'status' => $this->status,
            'otp_status' => $this->otp_status,
            'game_id' => $this->game_id,
            'ncoin' => $this->ncoin,
            'vcoin' => $this->vcoin,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'partner_code', $this->partner_code])
            ->andFilterWhere(['like', 'provider_code', $this->provider_code])
            ->andFilterWhere(['like', 'ref_code', $this->ref_code])
            ->andFilterWhere(['like', 'screen_name', $this->screen_name])
            ->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'email_token', $this->email_token])
            ->andFilterWhere(['like', 'passport', $this->passport])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'client_version', $this->client_version])
            ->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'os_type', $this->os_type])
            ->andFilterWhere(['like', 'last_login_ip_addr', $this->last_login_ip_addr]);

        return $dataProvider;
    }
    public function searchProviderCode($params){
        $this->load($params);
        // search by start_time and end_time
        $startTime = \DateTime::createFromFormat('d-m-Y', $this->startTime);
        $endTime = \DateTime::createFromFormat('d-m-Y', $this->endTime);
        if($this->provider_code ==""){
            $list_provider =ArrayHelper::toArray(Account::getProviderCode());
            foreach ($list_provider as $key => $value){
                //lấy doanh thu sms
                $list_provider[$key]['id']  =$key+1;
                $total_sms = SmsplusCharging::find()->select('sum(smsplus_charging.amount) as total_sms')
                    ->innerJoin('account','smsplus_charging.username = account.username ')
                    ->where(['account.provider_code'=>$value['provider_code']])
                    ->andWhere(['>=', 'account.date_created', $startTime->format('Y-m-d')])
                    ->andWhere(['<=', 'account.date_created', $endTime->format('Y-m-d')])
                    ->groupBy('smsplus_charging.username')->asArray()->all();
                $list_provider[$key]['total_sms'] = (!empty($total_sms))?$total_sms[0]['total_sms']:0;
                //lấy doanh thu thẻ cào
                $total_card = CardCharging::find()->select('sum(card_charging.amount) as total_card')
                    ->innerJoin('account','card_charging.username = account.username ')
                    ->where(['account.provider_code'=>$value['provider_code']])
                    ->andWhere(['>=', 'account.date_created', $startTime->format('Y-m-d')])
                    ->andWhere(['<=', 'account.date_created', $endTime->format('Y-m-d')])
                    ->groupBy('card_charging.username')->asArray()->all();
                $list_provider[$key]['total_card'] = (!empty($total_card))?$total_card[0]['total_card']:0;
                //tổng doanh thu
                $list_provider[$key]['total_all']  =$list_provider[$key]['total_sms']+$list_provider[$key]['total_card'];
            }

        }else{
            $list_provider[0]['id']  =1;
            $list_provider[0]['provider_code']  =$this->provider_code;
            $total_sms = SmsplusCharging::find()->select('sum(smsplus_charging.amount) as total_sms')
                ->innerJoin('account','smsplus_charging.username = account.username ')
                ->where(['account.provider_code'=>$this->provider_code])
                ->andWhere(['smsplus_charging.charge_status' => 1])
                ->andWhere(['>=', 'account.date_created', $startTime->format('Y-m-d')])
                ->andWhere(['<=', 'account.date_created', $endTime->format('Y-m-d')])
                ->groupBy('smsplus_charging.username')->asArray()->all();
            $list_provider[0]['total_sms'] = (!empty($total_sms))?$total_sms[0]['total_sms']:0;
            //lấy doanh thu thẻ cào
            $total_card = CardCharging::find()->select('sum(card_charging.amount) as total_card')
                ->innerJoin('account','card_charging.username = account.username ')
                ->where(['account.provider_code'=>$this->provider_code])
                ->andWhere(['card_charging.charge_status' => 1])
                ->andWhere(['>=', 'account.date_created', $startTime->format('Y-m-d')])
                ->andWhere(['<=', 'account.date_created', $endTime->format('Y-m-d')])
                ->groupBy('card_charging.username')->asArray()->all();
            $list_provider[0]['total_card'] = (!empty($total_card))?$total_card[0]['total_card']:0;
            //tổng doanh thu
            $list_provider[0]['total_all']  =$list_provider[0]['total_sms']+$list_provider[0]['total_card'];
        }
        return new ArrayDataProvider([
            'key' => 'id',
            'sort' => [
                'attributes' => ['id', 'provider_code','total_sms','total_card','total_all'],
            ],
            'allModels' => $list_provider,
        ]);
    }

    public function searchTotalRevenue($params)
    {
        $this->load($params);
        // search by start_time and end_time
        $startTime = \DateTime::createFromFormat('d-m-Y', $this->startTime);
        $endTime = \DateTime::createFromFormat('d-m-Y', $this->endTime);
        //lấy tổng doanh thu
        $total_revenus = 0;
        $total_cnc = 0;
        $total_one_pay = 0;
        $total_yotel = 0;
        $total_CPS = 0;
        $total_bonus_payment = 0;
        $total_sms_plus = SmsplusCharging::find()->select('sum(amount) as total')
            ->where(['charge_status' => 1])
            ->andWhere(['>=', 'date_created', $startTime->format('Y-m-d')])
            ->andWhere(['<=', 'date_created', $endTime->format('Y-m-d')])
            ->asArray()->one();
        $total_card = CardCharging::find()->select('sum(amount) as total')
            ->where(['charge_status' => 1])
            ->andWhere(['>=', 'date_created', $startTime->format('Y-m-d')])
            ->andWhere(['<=', 'date_created', $endTime->format('Y-m-d')])
            ->asArray()->one();
        $total_revenus = (float)$total_sms_plus['total'] + (float)$total_card['total'];
        //Doanh thu của CNC bằng 30% GDC
        $total_cnc = ($this->cnc * (float)$total_revenus) / 100;
        //Doanh thu OnePay bằng 46% SMS
        $total_one_pay = ($this->one_pay * (float)$total_sms_plus['total']) / 100;
        //Doanh thu Yotel bằng 20% tổng doanh thu
        $total_yotel = ($this->yotel * (float)$total_revenus) / 100;
        //Doanh thu CPS bằng 37% của 80% doanh thu
        $total_CPS = ($this->cps * 80 * (float)$total_revenus) / 10000;
        //Doanh thu trả thưởng
        $total_card_buy = CardBuying::find()->select('sum(amount) as total')
            ->where(['charge_status' => 1])
            ->andWhere(['>=', 'date_created', $startTime->format('Y-m-d')])
            ->andWhere(['<=', 'date_created', $endTime->format('Y-m-d')])
            ->asArray()->one();
        $total_bonus_payment = (float)$total_card_buy['total'];
        //DGC trước trả thưởng
        $total_gdc_before = $total_revenus - ($total_cnc + $total_one_pay + $total_yotel + $total_CPS);
        //GDC sau trả thưởng
        $total_gdc_after = $total_revenus - ($total_cnc + $total_one_pay + $total_yotel + $total_CPS + $total_bonus_payment);
        $dataProvider = [];
        $dataProvider[0]['id'] = 1;
        $dataProvider[0]['total_revenus'] = $total_revenus;
        $dataProvider[0]['total_cnc'] = $total_cnc;
        $dataProvider[0]['total_one_pay'] = $total_one_pay;
        $dataProvider[0]['total_yotel'] = $total_yotel;
        $dataProvider[0]['total_CPS'] = $total_CPS;
        $dataProvider[0]['total_bonus_payment'] = $total_bonus_payment;
        $dataProvider[0]['total_gdc_before'] = $total_gdc_before;
        $dataProvider[0]['total_gdc_after'] = $total_gdc_after;
        return new ArrayDataProvider([
            'key' => 'id',
            'sort' => [
                'attributes' => ['id', 'total_cnc', 'total_revenus', 'total_one_pay', 'total_yotel', 'total_CPS', 'total_bonus_payment', 'total_gdc_before', 'total_gdc_after'],
            ],
            'allModels' => $dataProvider,
        ]);
    }

    public function ExpectedRevenue()
    {
        $startTime = date('d-m-Y', strtotime('-4 day'));
        $endTime = date('d-m-Y', strtotime('-1 day'));
        $startTime = \DateTime::createFromFormat('d-m-Y', $startTime);
        $endTime = \DateTime::createFromFormat('d-m-Y', $endTime);
        //số ngày trong tháng
        $total_date = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $total_sms_plus = SmsplusCharging::find()->select('sum(amount) as total')
            ->where(['charge_status' => 1])
            ->andWhere(['>=', 'date_created', $startTime->format('Y-m-d')])
            ->andWhere(['<=', 'date_created', $endTime->format('Y-m-d')])
            ->asArray()->one();
        $total_card = CardCharging::find()->select('sum(amount) as total')
            ->where(['charge_status' => 1])
            ->andWhere(['>=', 'date_created', $startTime->format('Y-m-d')])
            ->andWhere(['<=', 'date_created', $endTime->format('Y-m-d')])
            ->asArray()->one();
        $total_revenus = 0;
        $total = 0;
        $total = (float)$total_sms_plus['total'] + (float)$total_card['total'];
        //tính doanh thu
        $total_revenus = ($total / 3) * ($total_date - 3);
        return $total_revenus;
    }
}
