<?php

namespace Spiral\Transactions\Exceptions\Transaction;

use Spiral\Transactions\Exceptions\TransactionException;

class EmptyAmountException extends TransactionException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = 'Amount should not be empty.';
        parent::__construct($message, $code, $previous);
    }
}