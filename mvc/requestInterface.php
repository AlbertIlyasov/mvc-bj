<?php

namespace MVC;

interface RequestInterface
{
    public function __get(string $name);
}
