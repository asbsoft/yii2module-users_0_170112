<?php

use asb\yii2\common_2_170212\base\UniApplication;

$adminUrlPrefix = empty(Yii::$app->params['adminPath']) ? '' : Yii::$app->params['adminPath'] . '/';//var_dump($adminUrlPrefix);

$type = empty(Yii::$app->type) ? false : Yii::$app->type;//var_dump($type);

return [
    //'layoutPath' => '@project/modules/sys/views/layouts',
    'layouts' => [
        'frontend'  => 'main',
        'backend'  => 'backend/main',
    ],

    'routesConfig' => [ // default: type => prefix|[config]
        'main'  => $type == UniApplication::APP_TYPE_BACKEND  ? false : 'user',
        'admin' => $type == UniApplication::APP_TYPE_FRONTEND ? false : ($adminUrlPrefix . 'user'),
    ],

    // shared models
    'models' => [ // alias => class name or object array
        'UserIdentity' => 'asb\yii2\modules\users_0_170112\models\UserIdentity',
    ],
];
