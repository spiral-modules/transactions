<?php

namespace Spiral\Transactions\Exceptions;

class EmptySourceIDGatewayException extends TransactionException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = 'Source ID should not be empty.';
        parent::__construct($message, $code, $previous);
    }
}