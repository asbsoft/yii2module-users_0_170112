<?php

    /* @var $this yii\web\View */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    /* @var $searchModel asb\yii2\modules\users_0_170112\models\UserSearch */
    /* @var $currentId integer current item id */

    use asb\yii2\common_2_170212\widgets\grid\ButtonedActionColumn;
    use asb\yii2\common_2_170212\assets\CommonAsset;

    use asb\yii2\modules\users_0_170112\models\User;
    use asb\yii2\modules\users_0_170112\models\UserSearch;
    use asb\yii2\modules\users_0_170112\models\AuthAssignment;

    use yii\grid\GridView;
    use yii\grid\SerialColumn;
    use yii\grid\ActionColumn;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;


    $showEmail = true;

    $showAuthKey = isset($this->context->module->params['showAuthKeyInAdmList'])
                 ? $this->context->module->params['showAuthKeyInAdmList'] : false;

    $showRoles = false;
    try { // check if auth table(s) exists
        $showRoles = (boolean)AuthAssignment::find()->count();
    } catch(\Exception $ex) {}

    $gridHtmlClass = 'users-list-grid';

    $tc = $this->context->module->tcModule;

    $commonAssets = CommonAsset::register($this);

    $title = Yii::t($tc, 'Users');
    $this->title = Yii::t($tc, 'Adminer') . ' - ' . $title;
    $this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];

?>
<div class="user-index">
    <div id="loadind" class="collapse text-center media-middle"><img src="<?= $commonAssets->baseUrl ?>/img/wait.gif" /></div>

    <h1><?= Html::encode($title) ?></h1>

    <p>
        <?= Html::a(Yii::t($tc, 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => $gridHtmlClass],
        'columns' => [
            ['class' => SerialColumn::className()],

            'id',

            'username',

            [
                'attribute' => 'roles',
                'header' => Yii::t($tc, 'Role(s)'),
                'value' => function($model, $key, $index, $column) {
                    if (empty($model->roles)) {
                        return UserSearch::ROLE_LOGINED;
                    }
                    $result = '';
                    foreach ($model->roles as $role) {
                        $result .= empty($result) ? '' : ', ';
                        $result .= $role->item_name;
                    }
                    return $result;
                },
                'visible' => $showRoles,
            ],
            [
                'attribute' => 'email',
                'format' => 'email',
                'visible' => $showEmail,
            ],
            [
                'attribute' => 'auth_key',
                'contentOptions' => [
                     'class' => 'small',
                ],
                'visible' => $showAuthKey,
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => [
                     'class' => 'small',
                ],
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'contentOptions' => [
                     'class' => 'small',
                ],
            ],
            [
                'attribute' => 'status',
                'value' => function($model, $key, $index, $column) {
                    //return ArrayHelper::getValue(User::statusesList(true), $model->status);
                    return Html::hiddenInput("old-status-name-{$key}", $model->status, [
                        'id' => "old-status-{$key}",
                    ])
                    . Html::dropDownList("status-name-{$key}", $model->status, User::statusesList(true), [
                        'class' =>'form-control statuses',
                        'id' => "status-{$key}",
                        //'data-method' => 'post', //?? where set url - links|buttons only
                        //'data-confirm' => "Are you sure to change status for #{$key}?"
                    ]);
                },
                'format' => 'raw',
                'filter' => User::statusesList(true),
                'filterInputOptions' => [
                    'class' =>'form-control',
                    'prompt' => Yii::t($tc, '-all-'),
                ],
                'contentOptions' => function($model, $key, $index, $column) {
                    return [
                        'class' => 'text-center col-xs-1',
                        'id' => "cell-status-{$key}", // add id to <td> tag
                    ];
                },
            ],
            [
                //'class' => ActionColumn::className(),
                'class' => ButtonedActionColumn::className(),
                //'tc' => $tc,
                'autosearch' => false,
                //'buttonSearch' => false,

                'header' => Yii::t($tc, 'Actions'),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
            ],
        ],
    ]); ?>
</div>

<?php
    $this->registerJs("
        jQuery('.{$gridHtmlClass} table tr').each(function(index) {
            var elem = jQuery(this);
            var id = elem.attr('data-key');
            if (id == '{$currentId}') {
                elem.addClass('bg-success'); //?? overwrite by .table-striped > tbody > tr:nth-of-type(2n+1)
                elem.css({'background-color': '#DFD'}); // work always
            }
        });
    ");

    $msg = Yii::t($tc, 'Are you sure to change status?');
    $url0 = Url::to(['change-status']);
    $waitImg = "<img class=\"pull-right\" src=\"{$commonAssets->baseUrl}/img/wait.gif\" />";
    $this->registerJs("
        jQuery('.statuses').bind('change', function() {
            var id = jQuery(this).attr('id');//alert(id);
            var value = jQuery(this).val();//alert(value);
            var value0 = jQuery('#old-' + id).val();//alert(value0);
            if (confirm('$msg')) {
                jQuery('.alert').hide();
                //jQuery('#loadind').show();
                //jQuery('#cell-' + id).empty();
                jQuery('#cell-' + id).replaceWith('$waitImg');

                var url = '{$url0}/' + id + '/' + value;//alert(url);
                //jQuery(location).attr('href', url); // POST need
                jQuery.ajax({url: url, type: 'POST'});
            } else {
                jQuery(this).val(value0);
            }
        });
    ");
?>
