<?php

namespace MVC\Traits;

use Exception;

trait MagicGetterTrait
{
    protected $magicAvailableNames;

    public function __get(string $name)
    {
        if (in_array($name, $this->magicAvailableNames)) {
            return $this->$name;
        }
        throw new Exception($name . ' is unavailable name.');
    }
}
