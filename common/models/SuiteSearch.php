<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Suite;

/**
 * SuiteSearch represents the model behind the search form of `common\models\Suite`.
 */
class SuiteSearch extends Suite
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['sid', 'title', 'corp_id', 'suite_id', 'suite_secret', 'suite_ticket', 'token', 'encodingAESKey', 'created_at', 'updated_at'], 'safe'],
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
        $query = Suite::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'sid', $this->sid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'corp_id', $this->corp_id])
            ->andFilterWhere(['like', 'suite_id', $this->suite_id])
            ->andFilterWhere(['like', 'suite_secret', $this->suite_secret])
            ->andFilterWhere(['like', 'suite_ticket', $this->suite_ticket])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'encodingAESKey', $this->encodingAESKey]);

        return $dataProvider;
    }
}
