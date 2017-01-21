<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\modules\users_0_170112\models\AuthAssignment;

use Yii;

class UserWithRoles extends User
{
    protected $_roles;
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return boolean whether `load()` found the expected form in `$data`.
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if ($result) {
            $this->loadRoles($data);
        }
        return $result;
    }

    protected function loadRoles($data)
    {//echo __METHOD__;var_dump($data);
        $formName = basename(AuthAssignment::className());
        if (!empty($data[$formName])) {
            $this->_roles = $data[$formName];
        }//var_dump($this->_roles);exit;
    }

    /**
     * @inheritdoc
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save($runValidation = true, $attributeNames = null)
    {//echo __METHOD__;var_dump($this->attributes);var_dump($this->_roles);
        $result = parent::save($runValidation, $attributeNames);
        if ($result && !empty($this->_roles)) {
            $result = AuthAssignment::setRoles($this, $this->_roles);
        }//var_dump($result);var_dump($this->errors);exit;
        return $result;
    }

    /**
     * @inheritdoc
     * @return integer|boolean the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function delete()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (AuthAssignment::deleteRoles($this) && parent::delete()) {
                $result = true;
            } else {
                $result = false;
            }
        } catch(\Exception $e) {//var_dump($e->getMessage());
            Yii::error($e->getMessage());
            $result = false;
        }
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }//echo __METHOD__.';deletion result:';var_dump($result);var_dump($this->errors);exit;
        return $result;
    }

}
