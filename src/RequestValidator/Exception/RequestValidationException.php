<?php

namespace App\RequestValidator\Exception;

class RequestValidationException extends \Exception
{
    public function __construct(array $messages)
    {
        $message = implode("\n", $messages);

        parent::__construct($message, 400);
    }
}