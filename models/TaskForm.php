<?php

namespace Custom\Models;

class TaskForm extends \MVC\Form
{
    const USERNAME_LABEL = 'Имя пользователя';
    const EMAIL_LABEL    = 'E-mail';
    const TEXT_LABEL     = 'Текст задачи';
    const STATUS_LABEL   = 'Статус';

    const STATUS_NOT_COMPLETED       = 0;
    const STATUS_COMPLETED           = 1;
    const STATUS_LABEL_NOT_COMPLETED = 'не выполнено';
    const STATUS_LABEL_COMPLETED     = 'выполнено';
    const STATUS_OPTIONS_LABEL = [
        self::STATUS_NOT_COMPLETED => self::STATUS_LABEL_NOT_COMPLETED,
        self::STATUS_COMPLETED     => self::STATUS_LABEL_COMPLETED,
    ];

    protected $username;
    protected $email;
    protected $text;
    protected $status;

    protected $labels = [
        'username' => self::USERNAME_LABEL,
        'email'    => self::EMAIL_LABEL,
        'text'     => self::TEXT_LABEL,
        'status'   => self::STATUS_LABEL,
    ];

    protected $rules = [
        [['username','email','text'], 'required'],
        ['email', 'email'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->magicAvailableNames = [
            'username',
            'email',
            'text',
            'status',
        ];
        $this->magicSetterAvailableNames = [
            'username',
            'email',
            'text',
            'status',
        ];
    }
}
