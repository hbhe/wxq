<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wxe_corp".
 *
 * @property int $id
 * @property string $corp_id
 * @property string $corp_name
 * @property string $corp_type
 * @property string $corp_round_logo_url
 * @property string $corp_square_logo_url
 * @property int $corp_user_max
 * @property int $corp_agent_max
 * @property string $corp_wxqrcode
 * @property string $corp_full_name
 * @property int $subject_type
 * @property string $userid
 * @property string $mobile
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $access_token
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Corp extends \common\wosotech\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%corp}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['corp_user_max', 'corp_agent_max', 'subject_type', 'status'], 'integer'],
            [['auth_key', 'password_hash', 'access_token'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['corp_id'], 'string', 'max' => 128],
            [['corp_name', 'corp_type', 'email', 'access_token'], 'string', 'max' => 64],
            [['corp_round_logo_url', 'corp_square_logo_url', 'corp_wxqrcode', 'corp_full_name'], 'string', 'max' => 512],
            [['userid', 'auth_key'], 'string', 'max' => 32],
            [['mobile'], 'string', 'max' => 16],
            [['username', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['corp_id'], 'unique'],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['email'], 'unique'],
            [['corp_user_max', 'corp_agent_max', 'subject_type', 'status', ], 'default', 'value' => 0],            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'corp_id' => 'Corp ID',
            'corp_name' => 'Corp Name',
            'corp_type' => 'Corp Type',
            'corp_round_logo_url' => 'Corp Round Logo Url',
            'corp_square_logo_url' => 'Corp Square Logo Url',
            'corp_user_max' => 'Corp User Max',
            'corp_agent_max' => 'Corp Agent Max',
            'corp_wxqrcode' => 'Corp Wxqrcode',
            'corp_full_name' => 'Corp Full Name',
            'subject_type' => 'Subject Type',
            'userid' => 'Userid',
            'mobile' => 'Mobile',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return CorpQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CorpQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'value' => new \yii\db\Expression('NOW()'),
            ],            
        ];
    }

}
