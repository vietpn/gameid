<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TopupBuying;

/**
 * TopupBuyingSearch represents the model behind the search form about `common\models\TopupBuying`.
 */
class TopupBuyingSearch extends TopupBuying
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id', 'tran_status', 'charge_status'], 'integer'],
            [['username', 'cate_code', 'target', 'amount', 'msg', 'data', 'gdc_tran_id', 'pay365_tran_id', 'date_created', 'response_at', 'response_code'], 'safe'],
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
        $query = TopupBuying::find();

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
            'date_created' => $this->date_created,
            'response_at' => $this->response_at,
            'tran_status' => $this->tran_status,
            'charge_status' => $this->charge_status,
            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'cate_code', $this->cate_code])
            ->andFilterWhere(['like', 'target', $this->target])
            ->andFilterWhere(['like', 'msg', $this->msg])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'gdc_tran_id', $this->gdc_tran_id])
            ->andFilterWhere(['like', 'pay365_tran_id', $this->pay365_tran_id])
            ->andFilterWhere(['like', 'response_code', $this->response_code]);

        return $dataProvider;
    }
}
