<?php

namespace common\modules\attendance\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\attendance\models\user;

/**
 * UserSearch represents the model behind the search form of `common\modules\attendance\models\user`.
 */
class UserSearch extends user
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gender', 'leader'], 'integer'],
            [['userid', 'name', 'department', 'position', 'mobile', 'email', 'weixinid', 'avatar', 'state', 'extattr', 'update_at'], 'safe'],
            [['lng', 'lat'], 'number'],
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
        $query = user::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'admin'=>SORT_DESC,
                    'department'=>SORT_ASC,
                    'leader'=>SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
           // 'id' => $this->id,
            'gender' => $this->gender,
            'leader' => $this->leader,
            'update_at' => $this->update_at,
            'state'=>$this->state,
            // 'lng' => $this->lng,
            // 'lat' => $this->lat,
        ]);
        $query->andFilterWhere(['like', 'userid', $this->userid])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['department'=> $this->department]);
        return $dataProvider;
    }
}
