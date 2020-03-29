<?php

namespace Custom\Models;

use MVC\App;

class User
{
    public function validate(string $login, string $passwd): bool
    {
        return $login == 'admin' && $passwd == '123';
    }

    public function login(string $login, string $passwd): self
    {
        if ($this->validate($login, $passwd)) {
            $userId = 1;
            App::get()->session->login($userId);
        }
        return $this;
    }
}
