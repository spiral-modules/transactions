<?php

namespace Spiral\Transactions\Exceptions\Transaction;

use Spiral\Transactions\Exceptions\TransactionException;

class InvalidQuantityException extends TransactionException
{
    public function __construct($quantity = "", $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Invalid quantity passed "%s", should be positive.', $quantity);
        parent::__construct($message, $code, $previous);
    }
}