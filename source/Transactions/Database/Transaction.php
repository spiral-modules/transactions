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
        'id'     => 'primary',
        'status' => TransactionStatus::class,

        'gateway'                => 'string',
        'gateway_transaction_id' => 'string',

        'paymentSource' => [self::BELONGS_TO => PaymentSourceInterface::class],
        'revisions'     => [
            self::HAS_MANY                => TransactionRevision::class,
            TransactionRevision::INVERSE  => 'transaction',
            TransactionRevision::ORDER_BY => [
                '{@}.id' => 'ASC'
            ]
        ],

        'currency'        => 'string(16)',
        'total_amount'    => 'float',           //first paid amount (FYI)
        'refunded_amount' => 'float, nullable', //total refunded amount (FYI)
        'paid_amount'     => 'float',           //current paid amount (through all revisions)
        'fee_amount'      => 'float',           //current gateway fee (through all revisions)

        'attributes' => [
            self::HAS_MANY => TransactionAttribute::class
        ],
        'items'      => [
            self::HAS_MANY => TransactionItem::class
        ]
    ];

    const DEFAULTS = [
        'currency' => 'USD'
    ];

    const INDEXES = [
        [self::INDEX, 'currency']
    ];
}