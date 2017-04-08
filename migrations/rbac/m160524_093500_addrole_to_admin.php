<?php

use asb\yii2\common_2_170212\web\UserIdentity;

use yii\db\Migration;

//Yii::setAlias('@asb/yii2/common_2_170212', '@vendor/asbsoft/yii2-common_2_170212');

class m160524_093500_addrole_to_admin extends Migration
{
    protected $adminUserId = 100; //!! tune
    protected $roleAdmin = 'roleAdmin';

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleAdmin);
        $auth->assign($roleAdmin, $this->adminUserId);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleAdmin);
        $auth->revoke($roleAdmin, $this->adminUserId);
    }

}
