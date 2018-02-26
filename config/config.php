<?php

use asb\yii2\common_2_170212\base\UniApplication;

$adminUrlPrefix = empty(Yii::$app->params['adminPath']) ? '' : Yii::$app->params['adminPath'] . '/';

$type = empty(Yii::$app->type) ? false : Yii::$app->type;

return [
    'routesConfig' => [ // default: type => prefix|[config]
        'main'  => $type == UniApplication::APP_TYPE_BACKEND  ? false : [
            'urlPrefix' => 'user',
            'startLinkLabel' => 'Users', // use default link ''
        ],
        'admin' => $type == UniApplication::APP_TYPE_FRONTEND ? false : [
            'urlPrefix' => $adminUrlPrefix . 'user',
            'startLink' => [
                'label' => 'Users manager', //!! no translate here, it will translate using 'MODULE_UID/module' tr-category
              //'link'  => '', // default
                'action' => 'admin/index',
            ],
        ],
    ],

    // shared models
    'models' => [ // alias => class name or object array
        'UserIdentity'  => 'asb\yii2\modules\users_0_170112\models\UserIdentity',
        'ProfileForm'   => 'asb\yii2\modules\users_0_170112\models\ProfileForm',
        'LoginForm'     => 'asb\yii2\modules\users_0_170112\models\LoginForm',
        'AuthItem'      => 'asb\yii2\modules\users_0_170112\models\AuthItem',
        'AuthAssignment'=> 'asb\yii2\modules\users_0_170112\models\AuthAssignment',
        'UserQuery'     => 'asb\yii2\modules\users_0_170112\models\UserQuery',
        'UserSearch'    => 'asb\yii2\modules\users_0_170112\models\UserSearch',
        'UserWithRoles' => 'asb\yii2\modules\users_0_170112\models\UserWithRoles',
      //'User'          => 'asb\yii2\modules\users_0_170112\models\User',
        'User'          => 'asb\yii2\modules\users_0_170112\models\UserWithRoles', // !!
    ],
];
