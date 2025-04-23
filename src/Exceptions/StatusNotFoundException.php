<?php

namespace App\Exceptions;

use Exception;

class StatusNotFoundException extends Exception
{
    public function __construct()
    {
        $message = 'Status not found';
        $code = 422;

        parent::__construct($message, $code);

        $this->message = $message;
        $this->code = $code;
    }
}
