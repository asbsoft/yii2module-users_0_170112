<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\modules\users_0_170112\Module;
use asb\yii2\modules\users_0_170112\models\User;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;

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
    {
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
            $module = Module::getModuleByClassname(Module::className());
            $this->tc = $module->tcModule;
        }
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
            [['username', 'password_new', 'password_repeat', 'email'], 'required', 'on' => self::SCENARIO_SELF_CREATE],
            ['verify_code', 'captcha',
                'captchaAction' => $this->captchaActionUid,
                'on' => self::SCENARIO_SELF_CREATE,
            ],

            [['password_old', 'password_new', 'password_repeat'], 'string',
                'min' => $this->minPasswordLength, 'max' => $this->maxPasswordLength,
            ],
            ['password_repeat', 'compare', 'compareAttribute' => 'password_new'],

            ['email', 'string', 'max' => 255],
            ['email', 'email'],

            ['change_auth_key', 'boolean'],
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
    {
        if (!$this->isNewRecord) {
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

        $result = parent::validate($attributeNames, $clearErrors);
        return $result;
    }

    /**
     * Edit user's profile.
     * @return integer|false the number of rows affected, or false if validation fails
     */
    public function save($isNewRecord)
    {
        if ($this->validate()) {
            $user = $this->user;
            $data = [];
            $fn = $user->formName();
            if ($isNewRecord) {
                $data[$fn]['username']    = $this->username;
            }
            $data[$fn]['email']           = $this->email;
            $data[$fn]['password']        = $this->password_new;
            $data[$fn]['change_auth_key'] = $this->change_auth_key;

            $loaded = $user->load($data);
            if ($isNewRecord) {
                $user->scenario = $user::SCENARIO_CREATE;
                $user->status = User::STATUS_REGISTERED;
                $saved = $user->insert();
            } else {
                $saved = $user->update();
            }
            if ($loaded && $saved) {
                $this->auth_key = $user->auth_key;
                return $saved;
            } else {
                $this->addErrors($user->errors);
            }
        }
        return false;
    }

}
