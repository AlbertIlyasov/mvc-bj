<?php

namespace Custom\Models;

class AuthForm extends \MVC\Form
{
    const LOGIN_LABEL  = 'Логин';
    const PASSWD_LABEL = 'Пароль';

    protected $login;
    protected $passwd;

    protected $labels = [
        'login'  => self::LOGIN_LABEL,
        'passwd' => self::PASSWD_LABEL,
    ];

    protected $rules = [
        [['login','passwd'], 'required'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->magicAvailableNames = [
            'login',
            'passwd',
        ];
        $this->magicSetterAvailableNames = [
            'login',
            'passwd',
        ];
    }
}
