<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['active', 'token' => $user->email_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to active your account:

<?= $resetLink ?>
