<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Corp;

/**
 * CorpSearch represents the model behind the search form of `common\models\Corp`.
 */
class CorpSearch extends Corp
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'corp_user_max', 'corp_agent_max', 'subject_type', 'status'], 'integer'],
            [['corp_id', 'corp_name', 'corp_type', 'corp_round_logo_url', 'corp_square_logo_url', 'corp_wxqrcode', 'corp_full_name', 'userid', 'mobile', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'access_token', 'created_at', 'updated_at'], 'safe'],
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
        $query = Corp::find();

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
            'corp_user_max' => $this->corp_user_max,
            'corp_agent_max' => $this->corp_agent_max,
            'subject_type' => $this->subject_type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'corp_id', $this->corp_id])
            ->andFilterWhere(['like', 'corp_name', $this->corp_name])
            ->andFilterWhere(['like', 'corp_type', $this->corp_type])
            ->andFilterWhere(['like', 'corp_round_logo_url', $this->corp_round_logo_url])
            ->andFilterWhere(['like', 'corp_square_logo_url', $this->corp_square_logo_url])
            ->andFilterWhere(['like', 'corp_wxqrcode', $this->corp_wxqrcode])
            ->andFilterWhere(['like', 'corp_full_name', $this->corp_full_name])
            ->andFilterWhere(['like', 'userid', $this->userid])
            ->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'access_token', $this->access_token]);

        return $dataProvider;
    }
}
