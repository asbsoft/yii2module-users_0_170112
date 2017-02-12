<?php

use asb\yii2\modules\users_0_170112\models\User as Model;

use yii\db\Migration;

class m170112_202001_insert_user_demo extends Migration
{
    const NAME = 'demo';
    const PSW  = 'demo12';
    const ID = 125;

    protected $tableName;

    public function init()
    {
        parent::init();

        //Yii::setAlias('@asb/yii2/common_2_170212', '@vendor/asbsoft/yii2-common_2_170212');
        //Yii::setAlias('@asb/yii2/cms_3_170211', '@vendor/asbsoft/yii2-cms_3_170211');
        //Yii::setAlias('@asb/yii2/modules', '@vendor/asbsoft/yii2module');

        $this->tableName = Model::tableName();
    }
    
    public function safeUp()
    {
        $now = time();

        $this->insert($this->tableName, [
            'id' => self::ID,
            'username' => self::NAME,
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash(self::PSW),
            'password_reset_token' => null,
            'email' => self::NAME . '@example.com',
            'status' => Model::STATUS_ACTIVE,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

    }

    public function safeDown()
    {
        $this->delete($this->tableName, [
            'id' => self::ID,
        ]);
    }

}
