<?php

namespace MVC;

class Response
{
    private $domain;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    public function redirect(string $url = '')
    {
        Header('Location: http://' . $this->domain . $url);
        die;
    }
}
