<?php

use yii\db\Schema;
use jamband\schemadump\Migration;

class m170913_094955_add_department_employee_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 ';
        }

        if (Yii::$app->db->schema->getTableSchema('{{%department}}') !== null) {
            $this->dropTable('{{%department}}');
        }

        if (Yii::$app->db->schema->getTableSchema('{{%employee}}') !== null) {
            $this->dropTable('{{%employee}}');
        }

        if (Yii::$app->db->schema->getTableSchema('{{%department_employee}}') !== null) {
            $this->dropTable('{{%department_employee}}');
        }

        $this->createTable('{{%department}}', [
            'id' => $this->string(128),
            'corp_id' => $this->string(128),
            'name' => $this->string(64)->comment('名称'),
            'parent_id' => $this->string(128)->comment('上级ID'),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'),
        ], $tableOptions);
        $this->addPrimaryKey('id', '{{%department}}', ['id']);
        $this->createIndex('parent_id', '{{%department}}', ['parent_id']);

        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'corp_id' => $this->string(128),
            'userid' => $this->string(128)->notNull(),
            'name' => $this->string(64)->comment('员工姓名'),
            'position' => $this->string(128)->comment('职位'),
            'mobile' => $this->string(64)->comment('手机'), // 第三方仅通讯录套件可获取
            'email' => $this->string(64)->comment('邮箱'),
            'avatar' => $this->string(256)->comment('头像'), // 头像url。注：如果要获取小图将url最后的”/0”改成”/100”即可
            'telephone' => $this->string(64)->comment('座机'), // 第三方仅通讯录套件可获取
            'english_name' => $this->string(64)->comment('英文名'),
            'extattr' => $this->string(64)->comment('扩展属性'), //扩展属性，第三方仅通讯录套件可获取
            'gender' => $this->smallInteger()->notNull()->defaultValue(0)->comment('性别'), // 0表示未定义，1表示男性，2表示女性
            'isleader' => $this->smallInteger()->notNull()->defaultValue(0)->comment('是否为上级'), // 是否为上级
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('状态'), // 激活状态: 1=已激活，2=已禁用，4=未激活 已激活代表已激活企业微信或已关注微信插件。未激活代表既未激活企业微信又未关注微信插件。
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
        $this->createIndex('mobile', '{{%employee}}', ['mobile'], true);

        $this->createTable('{{%department_employee}}', [
            'id' => $this->primaryKey(),
            'corp_id' => $this->string(128),
            'department_id' => $this->integer()->notNull(),
            'employee_id' => $this->integer()->notNull(),
            'sort_order' => $this->integer()->notNull()->defaultValue(0)->comment('排序'), // 员工在部门内的排序
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ]);

    }

    public function down()
    {
        if (Yii::$app->db->schema->getTableSchema('{{%department}}') !== null) {
            $this->dropTable('{{%department}}');
        }

        if (Yii::$app->db->schema->getTableSchema('{{%employee}}') !== null) {
            $this->dropTable('{{%employee}}');
        }

        if (Yii::$app->db->schema->getTableSchema('{{%department_employee}}') !== null) {
            $this->dropTable('{{%department_employee}}');
        }
    }
}
