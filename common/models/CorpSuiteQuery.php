<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CorpSuite]].
 *
 * @see CorpSuite
 */
class CorpSuiteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CorpSuite[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CorpSuite|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
