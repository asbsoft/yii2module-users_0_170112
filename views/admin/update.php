<?php

    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\users_0_170112\models\User */
    /* @var $rolesModels empty|asb\yii2\modules\users_0_170112\models\AuthAssignment[] */

    use yii\helpers\Html;


    $tc = $this->context->module->tcModule;

    $this->title = Yii::t($tc, 'Update user') . ' #' . $model->id;
    $this->params['breadcrumbs'][] = ['label' => Yii::t($tc, 'Users'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = Yii::t($tc, 'Update');

?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'rolesModels' => $rolesModels,
    ]) ?>

</div>
