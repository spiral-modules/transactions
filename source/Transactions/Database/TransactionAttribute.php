<?php

namespace Spiral\Transactions\Database;

use Spiral\ORM\Record;

class TransactionAttribute extends Record
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
}