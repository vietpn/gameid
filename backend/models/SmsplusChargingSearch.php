<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SmsplusCharging;

/**
 * SmsplusChargingSearch represents the model behind the search form about `common\models\SmsplusCharging`.
 */
class SmsplusChargingSearch extends SmsplusCharging
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'response_status', 'response_game_status', 'charge_status'], 'integer'],
            [['username', 'ref_code', 'msisdn', 'amount', 'command_code', 'error_code', 'error_message', 'mo_message', 'request_id', 'request_time', 'signature', 'response_sms', 'response_game', 'date_created', 'telco', 'game_id'], 'safe'],
            [['startTime', 'endTime'], 'safe'],
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
        $query = SmsplusCharging::find();

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
            'account_id' => $this->account_id,
            'game_id' => $this->game_id,
            'request_time' => $this->request_time,
            'response_status' => $this->response_status,
            'response_game_status' => $this->response_game_status,
            'charge_status' => $this->charge_status,
            'date_created' => $this->date_created,
            'amount' => $this->amount,
            'telco' => $this->telco,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'ref_code', $this->ref_code])
            ->andFilterWhere(['like', 'msisdn', $this->msisdn])
            ->andFilterWhere(['like', 'command_code', $this->command_code])
            ->andFilterWhere(['like', 'error_code', $this->error_code])
            ->andFilterWhere(['like', 'error_message', $this->error_message])
            ->andFilterWhere(['like', 'mo_message', $this->mo_message])
            ->andFilterWhere(['like', 'request_id', $this->request_id])
            ->andFilterWhere(['like', 'signature', $this->signature])
            ->andFilterWhere(['like', 'response_sms', $this->response_sms])
            ->andFilterWhere(['like', 'response_game', $this->response_game]);

        return $dataProvider;
    }
}
