<?php

/** @var $form \Custom\Models\TaskForm **/
/** @var $result bool **/

$title = 'Авторизация';
$result = $result ?? null;

?>

<? if (true === $result): ?>
    <div class="alert alert-success" role="alert">
        Вы успешно авторизовались.
    </div>
<? elseif (false === $result): ?>
    <div class="alert alert-danger" role="alert">
        Неверный логин или пароль.
    </div>
<? endif; ?>

<? if (true !== $result): ?>
    <?= $this->buildForm($form, [['login'], ['passwd', 'password']], 'Войти') ?>
<? endif; ?>
