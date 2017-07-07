<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\ORM\Record;

class Attribute extends Record
{
    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'    => 'primary',
        'name'  => 'string(128)',
        'value' => 'string(255)'
    ];

    const INDEXES = [
        [self::INDEX, 'name', 'value']
    ];

    const FILLABLE = ['*'];

    const IP_ADDRESS    = 'ipAddress';
    const GATEWAY_ERROR = 'gatewayError';
}