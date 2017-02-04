<?php

use asb\yii2\web\UserIdentity;

use yii\db\Migration;

class m160524_093501_addrole_to_root extends Migration
{
    protected $rootUserId = 90; //!! tune
    protected $roleRoot = 'roleRoot';

    public function init()
    {
        parent::init();

        Yii::setAlias('@asb/yii2cms', '@vendor/asb/yii2cms');
        Yii::setAlias('@asb/yii2/modules', '@vendor/asb/yii2modules');
    }

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
