<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

class Refund extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'                => 'primary',
        'gateway_refund_id' => 'string(255)',
        'currency'          => 'string(8)',
        'amount'            => 'float'
    ];

    const DEFAULTS = [
        'currency' => 'usd'
    ];

    const INDEXES = [
        [self::INDEX, 'currency'],
        [self::UNIQUE, 'gateway_refund_id'],
    ];
}