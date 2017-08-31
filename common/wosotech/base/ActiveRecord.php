<?php

namespace common\wosotech\base;

use Yii;

class ActiveRecord extends \yii\db\ActiveRecord
{
    // Support 'the' preffix, if theAttributeName does not exists, then drop back to try attributeName, for example, theName, theTitle, ...
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (\yii\base\UnknownPropertyException $e) {
            if ('the' == substr($name, 0, 3)) {
                return parent::__get(lcfirst(substr($name, 3)));
            }
            throw $e;
        }
    }

    public static function insertAjax($params)
    {
        $className = get_called_class();
        $model = new $className;
        $model->loadDefaultValues();
        if ($model->load($params, '') && $model->save()) {
            return \yii\helpers\Json::encode(['code' => 0, 'msg' => 'OK']);	        
        }      
        yii::error(print_r([__METHOD__, $model->getErrors()], true));
        return \yii\helpers\Json::encode(['code' => 1, 'msg' => '???????']);                
    }

    public static function createAjax($params)
    {
        return self::insertAjax($params);        
    }

    public static function readAjax($params)
    {
        $className = get_called_class();
        $model = new $className;
        $model->loadDefaultValues();
        if ($model->load($params, '') && $model->save()) {
            return \yii\helpers\Json::encode(['code' => 0, 'msg' => 'OK']);	        
        }      
        yii::error(print_r([__METHOD__, $model->getErrors()], true));
        return \yii\helpers\Json::encode(['code' => 1, 'msg' => '???????']);                
    }

}
