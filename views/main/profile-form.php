<?php

/* @var $this yii\web\View */
/* @var $model ProfileForm */

    use asb\yii2\common_2_170212\assets\CommonAsset;

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\captcha\Captcha;
    use yii\captcha\CaptchaAction;


    $tc = $this->context->module->tcModule;

    $assets = CommonAsset::register($this);

    if (!isset($enableClientValidation)) { // from view-successor
        $enableClientValidation = true;
      //$enableClientValidation = false; // for debug
    }
    if ($model->user->isNewRecord) {
        $passwordsCommonLabel = Yii::t($tc, 'Enter your password twice');
    } else {
        $passwordsCommonLabel = Yii::t($tc
          , 'Enter new password twice only if need to change it, otherwise keep these fields empty');
    }


    $this->title = $model->user->isNewRecord ? Yii::t($tc, 'Create new user') : Yii::t($tc, 'Update profile');
    $this->params['breadcrumbs'][] = ['label' => Yii::t($tc, 'Site'), 'url' => ['/']];
    $this->params['breadcrumbs'][] = Yii::t($tc, $this->title);

?>
<div class="user-profile">

    <?php $this->startBlock('title') ?>
        <h1><?= Html::encode($this->title) ?></h1>
    <?php $this->stopBlock('title') ?>

    <?php $form = ActiveForm::begin([
              'id' => 'profile-form',
              'enableClientValidation' => $enableClientValidation,
          ]); ?>

    <?php $this->startBlock('fields') ?>
        <?php $this->startBlock('before-all') ?>
        <?php $this->stopBlock('before-all') ?>

        <?php $this->startBlock('login') ?>
            <?php if ($model->user->isNewRecord): ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            <?php else: ?>
                <h4><?= Yii::t($tc, 'User name') ?>: <b><?= $model->username ?></b></h4>
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
            <?php endif; ?>
        <?php $this->stopBlock('login') ?>

        <?php $this->startBlock('after-login') ?>
        <?php $this->stopBlock('after-login') ?>

        <?php $this->startBlock('password') ?>
            <div class="col-md-12"><b><?= $passwordsCommonLabel ?></b></div>
            <div class="col-md-4">
                <?= $form->field($model, 'password_new')->passwordInput(['maxlength' => true])
                     //-> label($labelPassword);
                    ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
            </div>
            <br style="clear:both" />
        <?php $this->stopBlock('password') ?>

        <?php $this->startBlock('after-passwords') ?>
        <?php $this->stopBlock('after-passwords') ?>

        <?php $this->startBlock('email') ?>
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?php $this->stopBlock('email') ?>

        <?php $this->startBlock('after-main-fields') ?>
        <?php $this->stopBlock('after-main-fields') ?>

        <?php $this->startBlock('change-auth-key') ?>
            <?php if (!$model->user->isNewRecord && $this->context->allowUserUpdateAuthKey): ?>
                <?= $form->field($model, 'change_auth_key')->checkbox([
                        'label' => $model->attributeLabels()['change_auth_key'] . ": <b>{$model->auth_key}</b>",
                    ]) ?>
            <?php endif; ?>
        <?php $this->stopBlock('change-auth-key') ?>

        <?php $this->startBlock('captcha') ?>
            <?php if ($model->user->isNewRecord): ?>
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
        <?php $this->stopBlock('captcha') ?>

        <?php $this->startBlock('after-all') ?>
        <?php $this->stopBlock('after-all') ?>
    <?php $this->stopBlock('fields') ?>

    <?php $this->startBlock('button') ?>
        <br style="clear:both" />
        <div class="form-group">
            <?= Html::submitButton(Yii::t($tc, 'Send'), ['class' => 'btn btn-primary']) ?>
        </div>
    <?php $this->stopBlock('button') ?>

    <?php ActiveForm::end(); ?>

    <?php $this->startBlock('after-form') ?>
    <?php $this->stopBlock('after-form') ?>

</div>

<?php
    $this->registerJs("
        jQuery('#contact-captcha').bind('click', function() {
            jQuery('#contact-captcha').attr('src', '{$assets->baseUrl}/img/wait.gif');
        });
    ");
?>
