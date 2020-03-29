<?php

/** @var $isAdmin bool **/
/** @var $data string[] **/

use Custom\Models\TaskForm;

$title = 'Список задач';
$isAdmin = $isAdmin ?? null;

$columns = [
    [
        'attr'  => 'username',
        'label' => TaskForm::USERNAME_LABEL,
        'sort'  => true,
        'value' => function($data) use ($isAdmin) {
            $username = $this->html->encode($data['username']);
            if (!$isAdmin) {
                return $username;
            }
            return sprintf(
                '<a href="%s">%s</span>',
                $this->url->build(['index/add', 'id' => $data['id']]),
                $username
            );
        },
    ],
    [
        'attr'  => 'email',
        'label' => TaskForm::EMAIL_LABEL,
        'sort'  => true,
    ],
    [
        'attr'  => 'text',
        'label' => TaskForm::TEXT_LABEL,
        'value' => function($data) {
            $text = nl2br($this->html->encode($data['text']));
            if ($data['isChanged']) {
                $text .= '<p><span class="task_was_changed_by_admin">отредактировано администратором</span>';
            }
            return $text;
        },
    ],
    [
        'attr'  => 'status',
        'label' => TaskForm::STATUS_LABEL,
        'sort'  => true,
        'value' => function($data) {
            if (TaskForm::STATUS_COMPLETED == $data['status']) {
                return TaskForm::STATUS_LABEL_COMPLETED;
            }
            return '<span class="status_done">' . TaskForm::STATUS_LABEL_NOT_COMPLETED . '</span>';
        },
    ],
];

?>

<?= $this->buildGrid($dataProvider->getData(), $columns) ?>
<?= $dataProvider->pagination->render() ?>
