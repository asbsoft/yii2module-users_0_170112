<?php

/* @var $this yii\web\View */
/* @var $model asb\yii2\modules\users_0_170112\models\ProfileForm */

    use yii\helpers\Html;
    use yii\helpers\Url;

    $tc = $model->tc;//var_dump($tc);

    $siteUrl = Url::to('', true);//var_dump($siteUrl);

    //var_dump($model->attributes);//var_dump($model->auth_key);var_dump($model->user->auth_key);
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
