<?php

    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\users_0_170112\models\UserWithRoles */
    /* @var $rolesModels empty|asb\yii2\modules\users_0_170112\models\AuthAssignment[] */
    /* @var $form yii\widgets\ActiveForm */

    use asb\yii2\modules\users_0_170112\models\User;
    use asb\yii2\modules\users_0_170112\models\AuthAssignment;
    use asb\yii2\modules\users_0_170112\models\AuthItem;

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;


    if (!isset($enableClientValidation)) { // from view-successor
        $enableClientValidation = true;
      //$enableClientValidation = false; // for debug
    }

    $tc = $this->context->module->tcModule;

    $showRoles = false;
    try { // check if auth tables exists
        AuthItem::find()->count();
        $showRoles = (boolean)AuthAssignment::find()->count();
    } catch(\Exception $ex) {}

?>
<div class="user-form">

    <?php $form = ActiveForm::begin([
              'id' => 'profile-form',
              'enableClientValidation' => $enableClientValidation,
          ]); ?>

    <?php $this->startBlock('fields') ?>
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

        <?php $this->startBlock('after-main-fields') ?>
        <?php $this->stopBlock('after-main-fields') ?>
    <?php $this->stopBlock('fields') ?>

    <?php if ($showRoles && !$model->isNewRecord): ?>
        <label><?= Yii::t($tc, 'Roles') ?></label>
        <br style="clear:both" />

        <?php foreach ($rolesModels as $roleModel): ?>
            <div class="col-xs-2">
            <?= $form->field($roleModel, "[{$roleModel->item_name}]value", [
                    'options' => [
                        'class' => 'text-nowrap',
                    ],
                ])->checkbox([
                    'label' => $roleModel->item_name,
                ]) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br style="clear:both" />

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t($tc, 'Create') : Yii::t($tc, 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
