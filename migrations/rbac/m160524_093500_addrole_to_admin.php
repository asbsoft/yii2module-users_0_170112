<?php

use asb\yii2\web\UserIdentity;

use yii\db\Migration;

class m160524_093500_addrole_to_admin extends Migration
{
    protected $adminUserId = 100; //!! tune
    protected $roleAdmin = 'roleAdmin';

    public function init()
    {
        parent::init();

        //Yii::setAlias('@asb/yii2', '@vendor/asb');
        Yii::setAlias('@asb/yii2cms', '@vendor/asb/yii2cms');
        Yii::setAlias('@asb/yii2/modules', '@vendor/asb/yii2modules');//var_dump(Yii::$aliases);exit;
    }

/*
    public function up()
    {

    }

    public function down()
    {
        echo "m160524_092000_addroles_root_admin cannot be reverted.\n";

        return false;
    }
*/
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleAdmin);
        //$user = UserIdentity::findIdentity($this->adminUserId);
        //$auth->assign($roleAdmin, $user->getId());
        $auth->assign($roleAdmin, $this->adminUserId);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $roleAdmin = $auth->getRole($this->roleAdmin);
        $auth->revoke($roleAdmin, $this->adminUserId);
    }

}
