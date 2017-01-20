<?php

$adminUrlPrefix = empty($params['adminPath']) ? '' : $params['adminPath'] . '/';

return [
    'layoutPath' => '@asb/yii2cms/modules/sys/views/layouts',
    'layouts' => [
        'frontend'  => 'main',
        'backend'  => 'backend/main',
    ],

    'routesConfig' => [ // default: type => prefix|[config]
        'main'  => 'user',
        'admin' => $adminUrlPrefix . 'user', // Yii2-base config
    ],

    // shared models
    'models' => [ // alias => class name or object array
        'UserIdentity' => 'asb\yii2\modules\users_0_170112\models\UserIdentity',
    ],
];
