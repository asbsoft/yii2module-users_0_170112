<?php

    /* @var $this yii\web\View */
    /* @var $model asb\yii2\modules\users_0_170112\models\ProfileForm */

    use yii\helpers\Html;
    use yii\helpers\Url;

    $tc = $model->tcModule;

    $siteUrl = Url::to('', true);

    $confirmUrl = Url::toRoute(['confirm', 'token' => $model->user->auth_key], true);//var_dump($confirmUrl);exit;

?>
    <h3>
        <?= Yii::t($tc, 'Dear') ?>
        <?= Html::encode($model->username) ?>
    </h3>

    <?= Yii::t($tc, 'You have been registered on our site') ?>
    <b><?= $siteUrl ?></b>.
    <br />

    <?= Yii::t($tc, 'Follow this link to confirm your registration:') ?>
    <?= Html::a($confirmUrl, $confirmUrl) ?>
    <br />

    <?= Yii::t($tc, "If you don't register on our site remove this mail.") ?>
    <br />
