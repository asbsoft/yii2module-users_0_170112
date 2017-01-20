<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\modules\users_0_170112\Module;
use asb\yii2\modules\users_0_170112\models\User;

//use asb\yii2\models\DataModel; // extends ActiveRecord - need table
use yii\base\Model;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Profile form.
 */
class ProfileForm extends Model
{
    const SCENARIO_SELF_CREATE = 'self-create';

    public $minUsernameLength = 3;
    public $minPasswordLength = 6;
    public $maxPasswordLength = 32;
    
    // User model properties
    public $username;
    public $email;
    public $auth_key;

    public $password_old;
    public $password_new;
    public $password_repeat;
    public $change_auth_key;
    public $verify_code;

    /** Edited fields at form */
    public $fieldsForm = ['username', 'password_old', 'password_new', 'password_repeat', 'email', 'change_auth_key'];

/** Fields of User model can change */
//public $fieldsCanChange = ['username', 'password_new', 'email', 'auth_key'];

    /** Translation category */
    public $tc;

    public $isNewRecord;

    public $captchaActionUid;

    /** @var User */
    public $user;

    /**
     * @inheritdoc
     */
    public function init()
    {//echo __METHOD__;var_dump($this->user->isNewRecord);
        //var_dump($this->tc);var_dump($this->scenario);var_dump($this->captchaActionUid);exit;

        parent::init();

        if ($this->user->isNewRecord) {
            $this->isNewRecord = true;
            $this->change_auth_key = false;
        } else {
            $this->isNewRecord = false;

            $this->username = $this->user->username;
            $this->email    = $this->user->email;
            $this->auth_key = $this->user->auth_key;
        }

        // standart model yii\base\Model hasn't property $this->tcModule
        if (empty($this->tc)) {
            $module = Module::getModuleByClassname(Module::className());//var_dump($module::className());
            $this->tc = $module->tcModule;
        }//var_dump($this->tc);
    
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'string', 'min' => $this->minUsernameLength, 'max' => 255],
            [['username', 'password_new'], 'required', 'on' => self::SCENARIO_SELF_CREATE],
            [['email'], 'required'],

            [['password_old', 'password_new', 'password_repeat'], 'string',
                'min' => $this->minPasswordLength, 'max' => $this->maxPasswordLength,
            ],
            ['password_repeat', 'compare', 'compareAttribute' => 'password_new'],

            [['email', 'password_hash'], 'string', 'max' => 255],
            ['email', 'email'],

            ['change_auth_key', 'boolean'],

            ['verify_code', 'captcha',
                'skipOnEmpty' => false,
                'caseSensitive' => false,
                'captchaAction' => $this->captchaActionUid,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge($this->user->attributeLabels(), [
            'password'        => Yii::t($this->tc, 'Password'),
            'password_old'    => Yii::t($this->tc, 'Old password'),
            'password_new'    => Yii::t($this->tc, 'New password'),
            'password_repeat' => Yii::t($this->tc, 'Repeat password'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAttributeLabel($attribute)
    {
        if ($this->isNewRecord && $attribute == 'password_new') {
            $label = $this->getAttributeLabel('password');
        } else {
            $label = parent::getAttributeLabel($attribute);
        }
        return $label;
    }

    /**
     * @return boolean 
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {//echo __METHOD__.'<br>';
        if (!$this->isNewRecord) {//echo"#{$this->user->id}:{$this->user->username}/{$this->password_old}[{$this->user->password_hash}]<br>";
            if (empty($this->password_old)) {
                $this->addError('password_old', Yii::t($this->tc, 'Old password required'));
                return false;
            }
            $result = Yii::$app->security->validatePassword($this->password_old, $this->user->password_hash);
            if (!$result) {
                $this->addError('password_old', Yii::t($this->tc, 'Invalid password'));
                return false;
            }
        }

        $result = parent::validate($attributeNames, $clearErrors);//echo"parent::validate():";var_dump($result);var_dump($this->errors);
        return $result;
    }

    /**
     * Edit user's profile.
     * @return integer|false the number of rows affected, or false if validation fails
     */
    public function save($isNewRecord)
    {//echo __METHOD__."($isNewRecord)<br>";
        if ($this->validate($this->fieldsForm)) {
            $user = $this->user;
            $data = [];
            $fn = $user->formName();
            if ($isNewRecord) {
                $data[$fn]['username']    = $this->username;
            }
            $data[$fn]['email']           = $this->email;
            $data[$fn]['password']        = $this->password_new;
            $data[$fn]['change_auth_key'] = $this->change_auth_key;
            //var_dump($data);exit;

            $loaded = $user->load($data);
            //$fieldsCanChange = array_keys($data[$fn]); //!! + 'auth_key'
            if ($isNewRecord) {
                $user->scenario = $user::SCENARIO_CREATE;
                //$saved = $user->insert(true, $fieldsCanChange);
                $user->status = User::STATUS_REGISTERED;
                $saved = $user->insert();
            } else {
                //$saved = $user->update(true, $fieldsCanChange);
                $saved = $user->update();
            }
            if ($loaded && $saved) {//var_dump($user->attributes);var_dump($this->attributes);exit;
                $this->auth_key = $user->auth_key; //!!
                return $saved;
            } else {//var_dump($user->errors);
                $this->addErrors($user->errors);
            }
        }//var_dump($this->errors);exit;
        return false;
    }

}
