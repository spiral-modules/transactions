<?php

namespace Spiral\Transactions\Database\Sources;

use Spiral\ORM\Entities\RecordSource;
use Spiral\Transactions\Database\Transaction;

class TransactionSource extends RecordSource
{
    const RECORD = Transaction::class;
}