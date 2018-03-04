<?php

use asb\yii2\modules\users_0_170112\models\User;

use asb\yii2\common_2_170212\behaviors\ParamsAccessBehaviour;

return [
    'label'   => 'Simple users manager',
    'version' => '0.170112',

    'behaviors' => [
        'params-access' => [
            'class' => ParamsAccessBehaviour::className(),
            'defaultRole' => 'roleAdmin',
          //'readonlyParams' => [],
        ],
    ],

    /** Keep login period for admin */
    'loginAdminKeepPeriodSec' => 28800, // 8 hours: 3600 * 8

    /** Keep login period for frontend */
    'loginFrontendKeepPeriodSec' => 26784000, // 1 full month = 3600 * 24 * 31

    /** Confirm letter's sender E-mail */
    'emailConfirmFrom' => Yii::$app->params['adminEmail'],

    /** How many days confirmation letter will actual before expire, 0 - never */
    'confirmExpireDays' => 31,
    //'confirmExpireDays' => 0,

    /** Min login length */
    'minUsernameLength' => 5,
    /** Max login length */
    'maxUsernameLength' => 25,

    /** Admin list page size */
    'pageSizeAdmin' => 10,

    /** Enable registration online by guest */
    'registrationEnable' => true,//'registrationEnable' => false,

    /** Wait moderation after confirm registration */
    'waitModeration' => true,//'waitModeration' => false,

    /** Allow user see and regenerate auth key in profile */
    'allowUserUpdateAuthKey' => false,//'allowUserUpdateAuthKey' => true,

    /** Allow auth key in adminlist */
  //'showAuthKeyInAdmList' => true,

    User::className() => [
        'tableName' => '{{%user}}',
    ],

];
