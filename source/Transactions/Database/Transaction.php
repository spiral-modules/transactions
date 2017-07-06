<?php

namespace Spiral\Transactions\Database;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;
use Spiral\Transactions\Database\Types\TransactionStatus;
use Spiral\Transactions\PaymentSourceInterface;

class Transaction extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'                     => 'primary',
        'currency'               => 'string',
        'gateway'                => 'string',
        'gateway_transaction_id' => 'string',

        'status'               => TransactionStatus::class,
        'paymentSource'        => [self::BELONGS_TO => PaymentSourceInterface::class],
        'items'                => [
            self::HAS_MANY           => TransactionItem::class,
            TransactionItem::INVERSE => 'transaction'
        ],
        'revisions'            => [
            self::HAS_MANY               => TransactionRevision::class,
            TransactionRevision::INVERSE => 'transaction'
        ],
        'count_items'          => 'int',
        'total_items_quantity' => 'int',

        'calculated_amount' => 'float',
        'paid_amount'       => 'float',
        'fee_amount'        => 'float',
    ];

    const DEFAULTS = [
        'currency' => 'USD'
    ];
}