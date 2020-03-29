<?php

namespace MVC;

class Html
{
    public function encode(?string $str): ?string
    {
        return null !== $str ? htmlspecialchars($str) : null;
    }
}
