<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model asb\yii2\modules\users_0_170112\models\User */

$tc = $this->context->module->tcModule;

$this->title = Yii::t($tc, 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t($tc, 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
