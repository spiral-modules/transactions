<?php

namespace Spiral\Transactions\Database;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;
use Spiral\Transactions\Database\Types\RevisionOperation;

class TransactionRevision extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'            => 'primary',
        'operation'     => RevisionOperation::class,

        //current transaction paid amount and fee amount
        'paid_amount'   => 'float',
        'fee_amount'    => 'float',

        //in case of refund operation
        'refund_amount' => 'float, nullable'
    ];
}