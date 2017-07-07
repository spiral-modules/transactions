<?php

namespace Spiral\Transactions\Database\Entities;

use Spiral\ORM\Record;

abstract class AbstractTransactionEntity extends Record
{
    const ACTIVE_SCHEMA = false;

    const SCHEMA = [
        'id'               => 'primary',
        'gateway_raw_data' => 'text', //todo json?

        //current transaction paid, refunded and fee amount (through all revisions)
        'paid_amount'      => 'float',
        'fee_amount'       => 'float',
        'refunded_amount'  => 'float, nullable',
    ];
}