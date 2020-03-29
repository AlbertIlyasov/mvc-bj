<?php

namespace MVC\Traits;

use Exception;

trait MagicSetterTrait
{
    protected $magicSetterAvailableNames;

    public function __set(string $name, ?string $value): self
    {
        if (!in_array($name, $this->magicAvailableNames)) {
            throw new Exception($name . ' is unavailable name.');
        }
        $this->$name = trim($value);
        return $this;
    }
}
