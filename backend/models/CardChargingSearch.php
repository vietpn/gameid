<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CardCharging;

/**
 * CardChargingSearch represents the model behind the search form about `common\models\CardCharging`.
 */
class CardChargingSearch extends CardCharging
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'charge_status', 'tran_status'], 'integer'],
            [['username', 'cate_code', 'card_code', 'card_serial', 'data', 'amount', 'msg', 'gdc_tran_id', 'pay365_tran_id', 'date_created', 'response_at', 'response_code', 'des'], 'safe'],
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
        $query = CardCharging::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'account_id' => $this->account_id,
            'charge_status' => $this->charge_status,
            'tran_status' => $this->tran_status,
            'date_created' => $this->date_created,
            'response_at' => $this->response_at,
            'cate_code' => $this->cate_code,
            'response_code' => $this->response_code,
            'amount' => $this->amount,
        ]);

        // search by start_time and end_time
        $startTime = \DateTime::createFromFormat('d-m-Y', $this->startTime);
        $endTime = \DateTime::createFromFormat('d-m-Y', $this->endTime);

        if ($startTime) {
            $query->andWhere(['>=', 'date_created', $startTime->format('Y-m-d 00:00:00')]);
        }

        if ($endTime) {
            $query->andWhere(['<=', 'date_created', $endTime->format('Y-m-d 23:59:59')]);
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'cate_code', $this->cate_code])
            ->andFilterWhere(['like', 'card_code', $this->card_code])
            ->andFilterWhere(['like', 'card_serial', $this->card_serial])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'msg', $this->msg])
            ->andFilterWhere(['like', 'gdc_tran_id', $this->gdc_tran_id])
            ->andFilterWhere(['like', 'des', $this->des])
            ->andFilterWhere(['like', 'pay365_tran_id', $this->pay365_tran_id]);

        return $dataProvider;
    }
}
