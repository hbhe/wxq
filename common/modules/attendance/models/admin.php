<?php

namespace common\modules\attendance\models;

use Yii;

/**
 * This is the model class for table "wxe_admin".
 *
 * @property int $id
 * @property string $admin 管理员名称
 * @property string $password 密码
 * @property string $corpid 企业id
 * @property string $secret 管理组密码
 * @property string $login_at 登录时间
 * @property string $login_ip 登录ip
 */
class admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wxe_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin', 'password', 'corpid', 'secret'], 'required'],
            [['login_at'], 'safe'],
            [['admin', 'corpid','auth'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 32],
            [['secret'], 'string', 'max' => 100],
            [['login_ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin' => '管理员名称',
            'password' => '密码',
            'corpid' => '企业id',
            'secret' => '通讯录密匙',
            'login_at' => '登录时间',
            'login_ip' => '登录ip',
            'auth'=>'权限',
        ];
    }
    /**
     * 登录后修改登录信息
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            //密码发生变化则重新加密，否则不变化

            // if($this->isNewRecord or ($this->attributes['password'] !=$this->oldAttributes['password'])){
            //    // $this->password=md5($_POST['MapAdmin']['password']);
            // }

            $this->login_at=date('Y-m-d H:i:s');
            $this->login_ip=Yii::$app->request->userIp;
            return true;
        }else{
            return false;
        }
    }
}
