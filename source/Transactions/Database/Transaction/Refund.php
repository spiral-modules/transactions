<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

class Refund extends Record
{
    use TimestampsTrait;

    const DATABASE = 'refunds';

    const SCHEMA = [
        'id'                => 'primary',
        'gateway_refund_id' => 'string(255)',
        'gateway_raw_data'  => 'text', //todo json?
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