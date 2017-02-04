<?php

namespace asb\yii2\modules\users_0_170112\controllers;

use asb\yii2\controllers\BaseController;
use asb\yii2\modules\users_0_170112\models\User;
use asb\yii2\modules\users_0_170112\models\LoginForm;
use asb\yii2\modules\users_0_170112\models\ProfileForm;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\captcha\CaptchaAction;
use yii\web\NotFoundHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;

/**
 * Default controller
 */
class MainController extends BaseController
{
    public static $captchaActionId = 'captcha';
    public static $captchaMinLength = 4;
    public static $captchaMaxLength = 6;

    /** Enable/disable signup. If disabled - users can add only in backend by admin. */
    public $registrationEnable = false;

    /** After confirm from Email user is active (if False) or wait moderation (if True). */
    public $waitModeration = true;

    /** If True user can see auth_key in profile and can request to modify it. */
    public $allowUserUpdateAuthKey = false;

    /** How many days confirmation letter will actual before expire, 0 - never */
    public $confirmExpireDays = 0;

    /** Confirm letter's sender E-mail */
    public $emailConfirmFrom;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (isset($this->module->params['registrationEnable'])) {
            $this->registrationEnable = $this->module->params['registrationEnable'];
        }
        if (isset($this->module->params['waitModeration'])) {
            $this->waitModeration = $this->module->params['waitModeration'];
        }        
        if (isset($this->module->params['allowUserUpdateAuthKey'])) {
            $this->allowUserUpdateAuthKey = $this->module->params['allowUserUpdateAuthKey'];
        }
        if (!empty($this->module->params['confirmExpireDays'])) {
            $this->confirmExpireDays = intval($this->module->params['confirmExpireDays']);
        }
        $this->emailConfirmFrom
            = empty($this->module->params['emailConfirmFrom'])
            ? Yii::$app->params['adminEmail']
            : $this->module->params['emailConfirmFrom'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login',],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['signup', static::$captchaActionId, 'confirm'],
                        'allow' => $this->registrationEnable,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout'  => ['POST'],
                    'profile' => ['POST'],
                    'signup'  => ['POST'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [
            static::$captchaActionId => [
                'class' => CaptchaAction::ClassName(),
                'testLimit' => 1, // how many times should the same CAPTCHA be displayed
                'minLength' => static::$captchaMinLength, // minimum length for randomly generated word
                'maxLength' => static::$captchaMaxLength, // maximum length for randomly generated word
                'transparent' => true, // use transparent background
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @param Action $action the action to be executed.
     * @return boolean whether the action should continue to run.
     */
    public function beforeAction($action)
    {
        try {
            $isValid = parent::beforeAction($action);
        } catch (MethodNotAllowedHttpException $ex) {
            $isValid = false;
            $msg = $ex->getMessage();
            if ($action->id == 'signup') {
                $msg = Yii::t($this->tcModule, "To register go to 'Signup' link from our site menu.");
                throw new ForbiddenHttpException($msg); // robots go home!
            } else {
                throw new MethodNotAllowedHttpException($ex->getMessage());
            }
            //Yii::$app->session->setFlash('error', $msg);
            //return $this->goBack();
            //return $this->goHome();
        }
        return $isValid;
    }

    /**
     * Logs in a user.
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if (!Yii::$app->user->enableAutoLogin) $model->rememberMe = false;

        $period = isset($this->module->params['loginFrontendKeepPeriodSec'])
            ? intval($this->module->params['loginFrontendKeepPeriodSec'])
            : null;
        if ($model->load(Yii::$app->request->post()) && $model->login($period)) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Create user's profile.
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new ProfileForm([
            'user' => new User,
            'tc' => $this->tcModule,
            'scenario' => ProfileForm::SCENARIO_SELF_CREATE,
            'captchaActionUid' => $this->uniqueId . '/' . static::$captchaActionId,
        ]);

        $post = Yii::$app->request->post();
        $loaded = $model->load($post);
        if ($loaded && $model->save(true)) {
            Yii::$app->mailer->setViewPath(__DIR__ . '/../views/_mail');
            $message = Yii::$app->mailer->compose('confirm', ['model' => $model]);
            $subject = Yii::t($this->tcModule, 'Confirm you registration on site') . ' ' . Yii::$app->request->hostName;
            $message->setSubject($subject);
            $message->setFrom($this->emailConfirmFrom);
            $message->setTo($model->email);
            if ($message->send()) {
                Yii::$app->session->setFlash('success', Yii::t($this->tcModule, 'User created. Wait for admission.'));
            } else {
                Yii::error("Can't send E-mail to user #{$model->id}");
                Yii::$app->session->setFlash('error', Yii::t($this->tcModule, "Signup problem. Connect to support."));
            }
            //return $this->goBack(); //?? sometimes go back to signup - without POST will error
            return $this->goHome(); //= return $this->redirect(Yii::$app->homeUrl);
        } else {
            return $this->render('profile-form', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Update profile.
     * @return mixed
     */
    public function actionProfile()
    {
        $user = $this->findModel(Yii::$app->user->id);
        if (empty($user)) {
            return $this->goBack();
        }
        $model = new ProfileForm([
            'user' => $user,
            'tc' => $this->tcModule,
        ]);

        $post = Yii::$app->request->post();
        $loaded = $model->load($post);
        if ($loaded) {
            $saved = $model->save(false);
            if ($saved !== false) {
                $msg = ($saved === 0) ? Yii::t($this->tcModule, 'Profile not change.')
                                      : Yii::t($this->tcModule, 'Profile modified.');
                Yii::$app->session->setFlash('success', $msg);
                return $this->goHome();
            }
        }
        return $this->render('profile-form', [
            'model' => $model,
        ]);
    }

    /**
     * Confirm registration by click on link in email.
     */
    public function actionConfirm($token)
    {
        $user = $this->findModel([
            'auth_key' => $token,
            'status' => User::STATUS_REGISTERED,
        ]);

        if (empty($user)) {
            Yii::$app->session->setFlash('error',
                Yii::t($this->tcModule, 'Such unconfirmed user not found or already confirmed.')
                . ' ' . Yii::t($this->tcModule, 'Try to login or signup again.')
            );
            return $this->redirect(['login']);
        }

        if ($this->confirmExpireDays) {
            $confirmExpirePeriod = 60 * 60 * 24 * $this->confirmExpireDays;
            if (time() > ($user->created_at + $confirmExpirePeriod)) {
                $user->delete();
                Yii::$app->session->setFlash('error', Yii::t($this->tcModule, 'Token expired please register again.'));
                return $this->redirect(['signup']);
            }
        }

        $user->status = $this->waitModeration ? User::STATUS_WAIT : User::STATUS_ACTIVE;
        $user->auth_key = $user->generateAuthKey();
        $result = $user->save();
        if ($result) {
            $msg = Yii::t($this->tcModule, 'Registration confirmed.');
            if ($this->waitModeration) {
                $msg .= ' ' . Yii::t($this->tcModule, 'Wait moderation.');
            }
            Yii::$app->session->setFlash('success', $msg);
        } else {
            $msg = Yii::t($this->tcModule, 'User registration confirmation error.');
            Yii::trace($msg . ' ' . var_export($user->errors, true) . ' ' . var_export($user->attributes, true));
            Yii::$app->session->setFlash('error', $msg . ' ' . Yii::t($this->tcModule, 'Apply to support.'));
        }

        return $this->goHome();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|array $condition primary key value or a set of column values
     * @return User|null ActiveRecord model matching the condition, or `null` if nothing matches.
     */
    protected function findModel($condition)
    {
        return User::findOne($condition);
    }

}
