<?php

    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\users_0_170112\models\User */

    use yii\helpers\Html;
    use yii\widgets\DetailView;


    $tc = $this->context->module->tcModule;

    $this->title = Yii::t($tc, 'User') . ' #' . $model->id;
    $this->params['breadcrumbs'][] = ['label' => Yii::t($tc, 'Users'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-view">

    <?php $this->startBlock('header') ?>
        <h1><?= Html::encode($this->title) ?></h1>
    <?php $this->stopBlock('header') ?>

    <?php $this->startBlock('actions') ?>
    <p>
        <?= Html::a(Yii::t($tc, 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t($tc, 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t($tc, 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php $this->stopBlock('actions') ?>

    <?php $this->startBlock('before-view') ?>
    <?php $this->stopBlock('before-view') ?>

    <?php $this->startBlock('view') ?>
        <?= DetailView::widget([
            'id' => 'view-user',
            'model' => $model,
            'attributes' => [
                [
                    'label' => Yii::t($tc, 'Attribute'),
                    'value' => Yii::t($tc, 'Value'),
                    'captionOptions' => ['class' => 'col-md-3'],
                    'contentOptions' => ['class' => 'col-md-9', 'style' => 'font-weight: bold'],
                ],
              //'id',
                'username',
                'email:email',
                'auth_key',
                [
                    'attribute' => 'status',
                    'value' => $model::statusesList()[$model->status],
                ],
              //'email_confirm_token:email',
              //'password_hash',
              //'password_reset_token',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    <?php $this->stopBlock('view') ?>

    <?php $this->startBlock('after-view') ?>
    <?php $this->stopBlock('after-view') ?>

</div>
