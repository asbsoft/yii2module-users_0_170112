<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\modules\users_0_170112\Module;

use asb\yii2\common_2_170212\models\DataModel;

use Yii;
use yii\helpers\ArrayHelper;

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
    const STATUS_REGISTERED = -20;
    const STATUS_WAIT       = -10;
  //const STATUS_UNACTIVE   = 0;
    const STATUS_DELETED    = 0; // as in advanced Yii2-template
  //const STATUS_ACTIVE     = 1;
    const STATUS_ACTIVE     = 10; // as in advanced Yii2-template

    const SCENARIO_CREATE = 'create';

    // defaults, rewrite by module's params
    public $minUsernameLength = 3;
    public $maxUsernameLength = 20;
    public $minPasswordLength = 6;
    public $maxPasswordLength = 32;

    public $password;
    public $change_auth_key;

    public static $tcCommon = 'app'; // for using in static context

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        static::$tcCommon = $this->tcModule;

        $properties = array_keys(Yii::getObjectVars($this));
        $params = $this->module->params;
        foreach ($params as $property => $value) {
            if (in_array($property, $properties)) {
                $this->$property = $value;
            }
        }
    }
    
    /**
     * @return array statuses list: [value => name, ...]
     */
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
        return ArrayHelper::merge($this->profileRules(), [
            // main (need db for validate)
            ['username', 'unique'],
            ['email', 'unique'],
            // additional
            ['password_hash', 'string', 'max' => 255],
            [['status', 'created_at', 'updated_at'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::statusesList())],
            ['status', 'default', 'value' => self::STATUS_DELETED],
            [['auth_key', 'password_reset_token'], 'unique'],
            ['change_auth_key', 'boolean'],

            //[['auth_key'], 'string', 'max' => 32],
            //[['created_at', 'updated_at'], 'required'],
            //[['email_confirm_token', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
        ]);
    }
    /**
     * Part of rules for common use with ProfileForm.
     * Rules with fields length, patterns be best in single place.
     */
    public function profileRules()
    {
        return [
            [['username', 'password', 'email'], 'required', 'on' => self::SCENARIO_CREATE],

            ['username', 'match', 'pattern' => '/^[A-Za-z][A-Za-z0-9\-\.\ ]+$/i',
                'message' => Yii::t($this->tcModule, 'Only latin letters, digits, hyphen, points and blanks begin with letter')
            ],
            ['username', 'string', 'min' => $this->minUsernameLength, 'max' => $this->maxUsernameLength],

            ['password', 'string', 'min' => $this->minPasswordLength, 'max' => $this->maxPasswordLength],

            ['email', 'string', 'max' => 255],
            ['email', 'email'],
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
            'password'             => Yii::t($this->tcModule, 'Password'),
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
      //return new UserQuery(get_called_class());
        $module = Module::getModuleByClassname(Module::className());
        $queryModel = $module->model('UserQuery', [get_called_class()]);
        return $queryModel;
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
     * Finds user by email.
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
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
