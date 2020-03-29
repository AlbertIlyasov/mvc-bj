<?php

namespace MVC\Controllers;

class ErrorsController extends Controller
{
    public function NotFoundAction()
    {
        header('HTTP/1.0 404 Not Found');
    }
}
