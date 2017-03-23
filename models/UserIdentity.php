<?php

namespace asb\yii2\modules\users_0_170112\models;

use yii\web\IdentityInterface;

class UserIdentity extends User implements IdentityInterface
{
    /** Safe user's attributes to be visible in list */
    public static $fieldsInList = ['id', 'username', 'email', 'status', 'created_at', 'updated_at'];
    /**
     * @return array of users info
     */
    public static function usersList($orderBy = ['username' => SORT_ASC])
    {
        $result = self::find()
                  ->select(static::$fieldsInList) // only safe attribute
                  ->orderBy($orderBy)
                  ->asArray()
                  ->all();
        $users = [];
        foreach ($result as $user) {
            $users[$user['id']] = $user;
        }//var_dump($users);exit;
        return $users;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

}
