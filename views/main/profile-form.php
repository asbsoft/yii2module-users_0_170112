<?php

/* @var $this yii\web\View */
/* @var $model ProfileForm */

    use asb\yii2\assets\CommonAsset;

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\captcha\Captcha;
    use yii\captcha\CaptchaAction;

    $tc = $this->context->module->tcModule;//var_dump($tc);

    $assets = CommonAsset::register($this);

    $this->title = $model->isNewRecord ? Yii::t($tc, 'Create new user') : Yii::t($tc, 'Update profile');
    $this->params['breadcrumbs'][] = ['label' => Yii::t($tc, 'Site'), 'url' => ['/']];
    $this->params['breadcrumbs'][] = Yii::t($tc, $this->title);

?>
<div class="user-profile">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?php else: ?>
        <h4><?= Yii::t($tc, 'User name') ?>: <b><?= $model->username ?></b></h4>
        <?= $form->field($model, 'password_old')->passwordInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'password_new')->passwordInput(['maxlength' => true])
//        -> label($labelPassword);
        ?>
    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->isNewRecord && $this->context->allowUserUpdateAuthKey): ?>
        <?= $form->field($model, 'change_auth_key')->checkbox([
                'label' => $model->attributeLabels()['change_auth_key'] . ": <b>{$model->auth_key}</b>",
            ]) ?>
    <?php endif; ?>

    <?php if ($model->isNewRecord): ?>
        <div class="col-xs-6">
        <?= $form->field($model, 'verify_code', [
                'labelOptions' => ['label' => Yii::t($tc, 'Enter code here')],
            ])->widget(Captcha::className(), [
                'template' => include(__DIR__ . '/captcha.php'),
                'captchaAction' => ['/' . $model->captchaActionUid, [
                    //CaptchaAction::REFRESH_GET_VAR => 1 // after every post new captcha
                ]],
                'imageOptions' => [
                    'id' => 'contact-captcha',
                    'title' => Yii::t($tc, 'Click to refresh code'),
                ],
            ]) ?>
        </div>
    <?php endif; ?>

    <br style="clear:both" />

    <div class="form-group">
        <?= Html::submitButton(Yii::t($tc, 'Send'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $this->registerJs("
        jQuery('#contact-captcha').bind('click', function() {
            jQuery('#contact-captcha').attr('src', '{$assets->baseUrl}/img/wait.gif');
        });
    ");
?>
