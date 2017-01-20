<?php

use asb\yii2\rbac\IsRootRule;
use asb\yii2\rbac\IsAdminRule;

use yii\db\Migration;

class m160524_092000_add_roles_root_admin extends Migration
{
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

        $ruleIsRoot = new IsRootRule;
        $auth->add($ruleIsRoot);
        $roleRoot = $auth->createRole('roleRoot');
        $roleRoot->ruleName = $ruleIsRoot->name;
        $auth->add($roleRoot);

        $ruleIsAdmin = new IsAdminRule;
        $auth->add($ruleIsAdmin);
        $roleAdmin = $auth->createRole('roleAdmin');
        $roleAdmin->ruleName = $ruleIsAdmin->name;
        $auth->add($roleAdmin);

        $auth->addChild($roleRoot, $roleAdmin);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $roleRoot = $auth->getRole('roleRoot');
        $roleAdmin = $auth->getRole('roleAdmin');

        $auth->removeChild($roleRoot, $roleAdmin);
        $auth->remove($roleAdmin);
        $auth->remove($roleRoot);
        
        $ruleIsRoot = $auth->getRule('ruleIsRoot');
        $ruleIsAdmin = $auth->getRule('ruleIsAdmin');
        $auth->remove($ruleIsAdmin);
        $auth->remove($ruleIsRoot);
    }

}
