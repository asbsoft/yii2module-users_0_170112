<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\common_2_170212\models\DataModel;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string //$email_confirm_token
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends DataModel
{
    //const TABLE_NAME = 'user'; // depracated, table name move to module's params

    const STATUS_REGISTERED = -20;
    const STATUS_WAIT       = -10;
  //const STATUS_UNACTIVE   = 0;
    const STATUS_DELETED    = 0; // as in advanced Yii2-template
  //const STATUS_ACTIVE     = 1;
    const STATUS_ACTIVE     = 10; // as in advanced Yii2-template

    const SCENARIO_CREATE = 'create';

    public $minUsernameLength = 3;
    public $minPasswordLength = 6;
    public $maxPasswordLength = 32;

    public $password;
    public $change_auth_key;

    public static $tcCommon; // for using in static context

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        static::$tcCommon = $this->tcModule;
    }
    
    public static function statusesList($showWait = true)
    {
        $list = [];
        $list += $showWait ? [self::STATUS_REGISTERED => Yii::t(static::$tcCommon, 'registered')] : [];
        $list += $showWait ? [self::STATUS_WAIT => Yii::t(static::$tcCommon, 'wait')] : [];
        $list += [
            self::STATUS_DELETED => Yii::t(static::$tcCommon, 'unactive'),
            self::STATUS_ACTIVE  => Yii::t(static::$tcCommon, 'active'),
        ];
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'string', 'min' => $this->minUsernameLength, 'max' => 255],
            [['password'], 'string', 'min' => $this->minPasswordLength, 'max' => $this->maxPasswordLength],

            ['username', 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9\-\.\ ]+$/i',
                'message' => Yii::t(static::$tcCommon, 'Only latin letters, digits, hyphen, points and blanks begin with letter')
            ],

            [['username', 'password', 'email'], 'required', 'on' => self::SCENARIO_CREATE],

            [['username', 'email', 'auth_key', 'password_reset_token'], 'unique'],

            [['email', 'password_hash'], 'string', 'max' => 255],
            ['email', 'email'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::statusesList())],
            ['status', 'default', 'value' => self::STATUS_DELETED],

            ['change_auth_key', 'boolean'],

            //[['auth_key'], 'string', 'max' => 32],
            //[['created_at', 'updated_at'], 'required'],
            //[['email_confirm_token', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => Yii::t($this->tcModule, 'ID'),
            'username' => Yii::t($this->tcModule, 'Username'),
            'auth_key' => Yii::t($this->tcModule, 'Auth key'),
            'change_auth_key'      => Yii::t($this->tcModule, 'Change auth key'),
          //'email_confirm_token'  => Yii::t($this->tcModule, 'Email confirm token'),
            'password_hash'        => Yii::t($this->tcModule, 'Password hash'),
            'password_reset_token' => Yii::t($this->tcModule, 'Password reset token'),
            'email'      => Yii::t($this->tcModule, 'Email'),
            'status'     => Yii::t($this->tcModule, 'Status'),
            'created_at' => Yii::t($this->tcModule, 'Created at'),
            'updated_at' => Yii::t($this->tcModule, 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;

        if (!empty($this->password)) {
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }
        $this->updated_at = time();
        if ($insert) {
            $this->created_at = time();
            $this->auth_key = $this->generateAuthKey();
        } else {
            if ($this->change_auth_key) {
                $this->auth_key = $this->generateAuthKey();
            }
        }

        return true;
    }

    public function generateAuthKey()
    {
        return Yii::$app->security->generateRandomString();
    }

    /**
     * Finds user by username.
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

}
