<?php

use asb\yii2\modules\users_0_170112\models\User as Model;

use yii\db\Migration;

class m170112_202000_insert_user_user extends Migration
{
    const NAME = 'user';
    const PSW  = 'usr123';
    const ID = 123;

    protected $tableName;

    public function init()
    {
        parent::init();

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
