<?php

/** @var $form \Custom\Models\TaskForm **/
/** @var $result bool **/
/** @var $isNew bool **/

$title = $isNew ? 'Новая задача' : 'Редактирование задачи';
$result = $result ?? null;

?>

<? if (true === $result): ?>
    <div class="alert alert-success" role="alert">
        Задача успешно сохранена.
    </div>
<? elseif (false === $result): ?>
    <div class="alert alert-danger" role="alert">
        Ошибка в процессе сохранения задачи. Попробуйте ещё раз.
    </div>
<? endif; ?>

<? if (true !== $result): ?>
    <?= $this->buildBeginForm() ?>
        <?= $this->buildInput('username', $form) ?>
        <?= $this->buildInput('email', $form, 'email') ?>
        <?= $this->buildInput('text', $form, 'textarea') ?>
    <? if (!$isNew): ?>
            <?= $this->buildInput('status', $form, 'select', $form::STATUS_OPTIONS_LABEL) ?>
    <? endif; ?>
        <?= $this->buildSubmitButton('Сохранить') ?>
    <?= $this->buildEndForm() ?>
<? endif; ?>
