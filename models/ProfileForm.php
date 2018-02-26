<?php

namespace asb\yii2\modules\users_0_170112\models;

use asb\yii2\modules\users_0_170112\Module;
use asb\yii2\modules\users_0_170112\models\User;

use asb\yii2\common_2_170212\models\BaseModel;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Profile form.
 */
class ProfileForm extends BaseModel
{
    const SCENARIO_CREATE = 'create';

    // defaults, rewrite by module's params
    public $minUsernameLength = 3;
    public $maxUsernameLength = 20;
    public $minPasswordLength = 6;
    public $maxPasswordLength = 32;
    
    // User model properties
    public $username;
    public $email;
    public $auth_key;

    public $password; // old (already exists)
    public $password_new;
    public $password_repeat;
    public $change_auth_key;
    public $verify_code;

    public $captchaActionUid;

    /** @var User */
    public $user;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $properties = array_keys(Yii::getObjectVars($this));
        $params = $this->module->params;
        foreach ($params as $property => $value) {
            if (in_array($property, $properties)) {
                $this->$property = $value;
            }
        }

        if ($this->user->isNewRecord) {
            $this->change_auth_key = false;
        } else {
            $this->username = $this->user->username;
            $this->email    = $this->user->email;
            $this->auth_key = $this->user->auth_key;
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
        $userModel = $this->module->model('User');
        $rules = $userModel->profileRules(); // part of rules from User model (fields length, etc.)
        $rules = ArrayHelper::merge($rules, [
            // rules only for this model:
            [['password_new', 'password_repeat'], 'required',
                'on' => self::SCENARIO_CREATE,
            ],
            ['verify_code', 'captcha', 'captchaAction' => $this->captchaActionUid,
                'on' => self::SCENARIO_CREATE,
            ],
            [['password', 'password_new', 'password_repeat'], 'string',
                'min' => $this->minPasswordLength, 'max' => $this->maxPasswordLength,
            ],
            ['password_repeat', 'compare',
                'compareAttribute' => 'password_new',
                'skipOnEmpty' => false,
            ],
        ]);
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $user = $this->module->model('User');
        $labels = $user->attributeLabels();
        $labels = ArrayHelper::merge($labels, [
            'password_new'    => Yii::t($this->tcModule, 'New password'),
            'password_repeat' => Yii::t($this->tcModule, 'Repeat password'),
        ]);
        if ($this->user->isNewRecord) {
          $labels['password'] = Yii::t($this->tcModule, 'Password');
        } else {
          $labels['password'] = Yii::t($this->tcModule, 'Old password');
        }
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeLabel($attribute)
    {
        if ($this->user->isNewRecord && $attribute == 'password_new') {
            $label = Yii::t($this->tcModule, 'Password');
        } else {
            $label = parent::getAttributeLabel($attribute);
        }
        return $label;
    }

    /**
     * @inheritdoc
     * @return bool whether the validation should be executed. Defaults to true.
     */
    public function beforeValidate()
    {
        if ($this->user->isNewRecord && empty($this->password)) {
            $this->password = $this->password_new;
        }
        return parent::beforeValidate();
    }
    
    /**
     * @inheritdoc
     * @return bool whether the validation is successful without any error.
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        // check this form fields
        $result = parent::validate($attributeNames, $clearErrors);

        // check user's fields
        $this->user->load($this->attributes, '');
        $resultUser = $this->user->validate();
        if (!$resultUser) {
            $this->addErrors($this->user->errors);
            $result = false;
        }

        // check old password
        if (!$this->user->isNewRecord) {
            if (empty($this->password)) {
                $this->addError('password', Yii::t($this->tcModule, 'Old password required'));
                $result = false;
            } else {
                $result = Yii::$app->security->validatePassword($this->password, $this->user->password_hash);
                if (!$result) {
                    $this->addError('password', Yii::t($this->tcModule, 'Invalid password'));
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Edit user's profile.
     * @param boolean $isNewRecord
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
                $user->status = $user::STATUS_REGISTERED;
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

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     * Need for unique-validator imported from User model.
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        $module = Module::getModuleByClassname(Module::className());
        $user = $module->model('User');
        return $user::find();
    }

}
