<?php

namespace common\modules\attendance\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\attendance\models\NewAttendance;

/**
 * NewAttendanceSearch represents the model behind the search form of `common\modules\attendance\models\NewAttendance`.
 */
class NewAttendanceSearch extends NewAttendance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'attendance', 'am_pm', 'num', 'state'], 'integer'],
            [['mobile', 'work_date', 'name','department'], 'safe'],
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
        $query = NewAttendance::find()->joinWith('user');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder'=>[
                    'id'=>SORT_DESC,
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
            'id' => $this->id,
            'work_date' => $this->work_date,
            'attendance' => $this->attendance,
            'am_pm' => $this->am_pm,
            'num' => $this->num,
            'name'=>$this->name,
            'wxe_new_attendance.state'=>$this->state,
        ]);
        if($this->department){
            $where=['or',['department'=>$this->department],['like', 'department', $this->department.';']];
        }else{
            $where=['>','department',0];
        }
        $query->andFilterWhere($where);
        return $dataProvider;
    }
}
