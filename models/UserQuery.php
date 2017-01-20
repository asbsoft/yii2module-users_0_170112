<?php

namespace asb\yii2\modules\users_0_170112\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[User]].
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /*public function active()
    {
        //return $this->andWhere('[[status]]=1');
        return $this->andWhere('[[status]]=10'); //!! User::STATUS_ACTIVE
    }*/

    /**
     * @inheritdoc
     * @return User[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return User|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
