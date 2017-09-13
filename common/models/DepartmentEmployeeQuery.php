<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[DepartmentEmployee]].
 *
 * @see DepartmentEmployee
 */
class DepartmentEmployeeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DepartmentEmployee[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DepartmentEmployee|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
