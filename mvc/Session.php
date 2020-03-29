<?php

namespace MVC;

class Session
{
    public function login(int $userId): self
    {
        $_SESSION['auth']['userId'] = $userId;
        return $this;
    }

    public function logout(): self
    {
        unset($_SESSION['auth']);
        return $this;
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['auth']['userId']);
    }
}
