<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\models\DataModel;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Login form
 */
class LoginForm extends DataModel
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    public static $tcCommon;

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
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $result = $this->validate();//var_dump($result);exit;
        if ($result) {
            $result = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);//var_dump($result);exit;
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
        }//var_dump($this->_user);exit;

        return $this->_user;
    }
}
