<?php

namespace asb\yii2\modules\users_0_170112\models;

use Yii;

class UserWithRoles extends User
{
    protected $_roles;
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        $authAssignmentModel = $this->module->model('AuthAssignment');
        return $this->hasMany($authAssignmentModel::className(), ['user_id' => 'id']);
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
    {
        $authAssignmentModel = $this->module->model('AuthAssignment');
        $formName = basename($authAssignmentModel::className());
        if (!empty($data[$formName])) {
            $this->_roles = $data[$formName];
        }
    }

    /**
     * @inheritdoc
     * @return boolean whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $result = parent::save($runValidation, $attributeNames);
        if ($result && !empty($this->_roles)) {
            $authAssignmentModel = $this->module->model('AuthAssignment');
            $result = $authAssignmentModel::setRoles($this, $this->_roles);
        }
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
            $authAssignmentModel = $this->module->model('AuthAssignment');
            if ($authAssignmentModel::deleteRoles($this) && parent::delete()) {
                $result = true;
            } else {
                $result = false;
            }
        } catch(\Exception $e) {
            Yii::error($e->getMessage());
            $result = false;
        }
        if ($result) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $result;
    }

}
