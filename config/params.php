<?php

return [
    'label'   => 'Simple users manager',
    'version' => '0.170112',

    /** Confirm letter's sender E-mail */
    'emailConfirmFrom' => Yii::$app->params['adminEmail'],

    /** How many days confirmation letter will actual before expire, 0 - never */
    'confirmExpireDays' => 31,
    //'confirmExpireDays' => 0,

    /** Admin list page size */
    'pageSizeAdmin' => 10,

    /** Enable registration online by guest */
    'registrationEnable' => true,//'registrationEnable' => false,

    /** Wait moderation after confirm registration */
    'waitModeration' => true,//'waitModeration' => false,

    /** Allow user see and regenerate auth key in profile */
    'allowUserUpdateAuthKey' => false,//'allowUserUpdateAuthKey' => true,

];
