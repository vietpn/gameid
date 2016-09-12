<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SocialAccount;

/**
 * SocialAccountSearch represents the model behind the search form about `common\models\SocialAccount`.
 */
class SocialAccountSearch extends SocialAccount
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'account_id'], 'integer'],
            [['provider', 'client_id', 'data', 'code', 'date_created', 'email', 'username'], 'safe'],
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
        $query = SocialAccount::find();

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
        ]);

        $query->andFilterWhere(['like', 'provider', $this->provider])
            ->andFilterWhere(['like', 'client_id', $this->client_id])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'username', $this->username]);

        return $dataProvider;
    }
}
