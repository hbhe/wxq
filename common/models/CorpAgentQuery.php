<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[CorpAgent]].
 *
 * @see CorpAgent
 */
class CorpAgentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CorpAgent[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CorpAgent|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
