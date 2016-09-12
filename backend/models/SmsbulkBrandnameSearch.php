<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SmsbulkBrandname;

/**
 * SmsbulkBrandnameSearch represents the model behind the search form about `common\models\SmsbulkBrandname`.
 */
class SmsbulkBrandnameSearch extends SmsbulkBrandname
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['msisdn', 'alias', 'message', 'send_time', 'response', 'date_created'], 'safe'],
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
        $query = SmsbulkBrandname::find();

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
            'status' => $this->status,
            'date_created' => $this->date_created,
        ]);

        $query->andFilterWhere(['like', 'msisdn', $this->msisdn])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'send_time', $this->send_time])
            ->andFilterWhere(['like', 'response', $this->response]);

        return $dataProvider;
    }
}
