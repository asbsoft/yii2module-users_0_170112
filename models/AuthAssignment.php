<?php

namespace asb\yii2\modules\users_0_170112\models;

//use asb\yii2\models\DataModel; // problem with calcPage()
use yii\db\ActiveRecord as DataModel;

use Yii;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends DataModel
{

    public $value;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['item_name'], 'exist',
                'skipOnError' => true,
                'targetClass' => AuthItem::className(),
                'targetAttribute' => ['item_name' => 'name'],
            ],
            ['value', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => Yii::t('app', 'Item Name'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

    /**
     * @inheritdoc
     * @return AuthAssignmentQuery the active query used by this AR class.
     */
/*
    public static function find()
    {
        return new AuthAssignmentQuery(get_called_class());
    }
*/

    public static function deleteRoles($user)
    {//echo __METHOD__."($user->id)";
        $result = true;
        $models = static::find()->where(['user_id' => $user->id])->all();
        foreach ($models as $model) {
            if(!$model->delete()) {
                $result = false;
                $user->addErrors($model->errors);
            }
        }
        return $result;
    }

    /**
     * @return boolean
     */
    public static function setRoles($user, $roles)
    {//echo __METHOD__."($user->id)";var_dump($roles);
        $result = true;

        $result = static::deleteRoles($user);

        //$allRoles = AuthItem::find()->where(['like', 'name', 'role'])->all();//var_dump($allRoles);exit;

        foreach ($roles as $roleName => $data) {
            if ((boolean)$data['value']) {
                $role = new static([
                    'item_name' => $roleName,
                    'user_id' => (string)$user->id,
                    'created_at' => time(),
                ]);
                if (!$role->save()) {
                    $result = false;
                    $user->addErrors($role->errors);
                }
            }
        }//var_dump($result);var_dump($user->errors);exit;

        return $result;
    }

}
