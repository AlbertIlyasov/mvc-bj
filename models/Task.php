<?php

namespace Custom\Models;

class Task extends \MVC\ActiveRecord
{
    public $username;
    public $email;
    public $text;
    public $status;
    public $isChanged;
}
