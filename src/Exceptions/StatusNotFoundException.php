<?php

namespace App\Exceptions;

class StatusNotFoundException extends \Exception
{
    public function __construct() {
        $message = 'Status not found';
        $code = 422;

        parent::__construct($message, $code);

        $this->message = $message;
        $this->code = $code;
    }
}