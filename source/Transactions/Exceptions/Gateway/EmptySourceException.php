<?php

namespace Spiral\Transactions\Exceptions\Gateway;

use Spiral\Transactions\Exceptions\GatewayException;

class EmptySourceException extends GatewayException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = 'Unable to process transaction, no payment method specified, source ID is empty.';
        parent::__construct($message, $code, $previous);
    }
}