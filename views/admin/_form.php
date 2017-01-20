<?php

use asb\yii2\modules\users_0_170112\models\User;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model asb\yii2\modules\users_0_170112\models\User */
/* @var $form yii\widgets\ActiveForm */

$tc = $this->context->module->tcModule;

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->isNewRecord): ?>
        <?= $form->field($model, 'change_auth_key')->checkbox([
                'label' => $model->attributeLabels()['change_auth_key'] . " '{$model->auth_key}'",
            ]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'status')->dropDownList(User::statusesList(true)) ?>

    <?php //echo $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>
    <?php //echo $form->field($model, 'email_confirm_token')->textInput(['maxlength' => true]) ?>
    <?php //echo $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>
    <?php //echo $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>
    <?php //echo $form->field($model, 'created_at')->textInput() ?>
    <?php //echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t($tc, 'Create') : Yii::t($tc, 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
