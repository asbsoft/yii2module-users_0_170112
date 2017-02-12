<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\common_2_170212\models\DataModel;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Login form
 */
class LoginForm extends DataModel
{
    public $loginPeriod = 2592000; // default: 1 month = 3600 * 24 * 30
    public $rememberMe = true;

    public $username;
    public $password;

    public static $tcCommon;

    private $_user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        static::$tcCommon = $this->tcModule;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $user = $this->getUser();
        if (empty($user)) $user = new User;

        return ArrayHelper::merge($user->attributeLabels(), [
            'password' => Yii::t(static::$tcCommon, 'Password'),
            'rememberMe' => Yii::t(static::$tcCommon, 'Remember me'),
        ]);
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t(static::$tcCommon, 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @var integer|null $period in seconds to keep login, if null use default value
     * @return bool whether the user is logged in successfully
     */
    public function login($period = null)
    {
        if (!isset($period)) { // $period may be 0
            $period = $this->loginPeriod;
        }
        
        $result = $this->validate();
        if ($result) {
            $result = Yii::$app->user->login($this->getUser(), $this->rememberMe ? $period : 0);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = UserIdentity::findByUsername($this->username);
        }

        return $this->_user;
    }
}
