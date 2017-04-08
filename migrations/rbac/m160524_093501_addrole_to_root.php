<?php

use asb\yii2\common_2_170212\web\UserIdentity;

use yii\db\Migration;

//Yii::setAlias('@asb/yii2/common_2_170212', '@vendor/asbsoft/yii2-common_2_170212');

class m160524_093501_addrole_to_root extends Migration
{
    protected $rootUserId = 90; //!! tune
    protected $roleRoot = 'roleRoot';

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleRoot);
        $auth->assign($roleAdmin, $this->rootUserId);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleRoot);
        $auth->revoke($roleAdmin, $this->rootUserId);
    }

}
