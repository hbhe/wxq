<?php

namespace common\modules\attendance\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\attendance\models\attendance;

/**
 * AttendanceSearch represents the model behind the search form of `common\modules\attendance\models\attendance`.
 */
class AttendanceSearch extends attendance
{
    public $name;
    public $department;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'attendance'], 'integer'],
            [['mobile', 'create_at', 'name','department'], 'safe'],
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
        $corpid=Yii::$app->session->get('corpid');
        $query = attendance::find();
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_DESC,
                ],
            ],
            'pagination'=>[
                'pageSize'=>20,
            ],
        ]);

        $this->load($params);
        $query->where(['wxe_attendance.corpid'=>$corpid]);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'create_at' => $this->create_at,
            'attendance' => $this->attendance,
            'department'=>$this->department,
        ]);
        $query->andFilterWhere(['like', 'mobile', $this->mobile]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        return $dataProvider;
    }
}
