<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

class Refund extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'         => 'primary',
        'gateway_id' => 'string(255)',
        'amount'     => 'float'
    ];

    const INDEXES = [
        [self::UNIQUE, 'gateway_id'],
    ];
}