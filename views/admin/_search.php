<?php

    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\users_0_170112\models\UserSearch */
    /* @var $form yii\widgets\ActiveForm */

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;


    $tc = $this->context->module->tcModule;

?>
<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'email_confirm_token') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t($tc, 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t($tc, 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
