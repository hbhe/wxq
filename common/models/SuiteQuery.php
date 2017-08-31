<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Suite]].
 *
 * @see Suite
 */
class SuiteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Suite[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Suite|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
