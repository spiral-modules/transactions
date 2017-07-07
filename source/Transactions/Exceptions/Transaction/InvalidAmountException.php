<?php

namespace Spiral\Transactions\Exceptions\Transaction;

use Spiral\Transactions\Exceptions\TransactionException;

class InvalidAmountException extends TransactionException
{
    public function __construct($amount = "", $code = 0, \Throwable $previous = null)
    {
        $message = sprintf('Invalid amount passed "%s", should be positive.', $amount);
        parent::__construct($message, $code, $previous);
    }
}