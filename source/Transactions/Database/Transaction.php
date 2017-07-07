<?php

namespace Spiral\Transactions\Database;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\Transactions\Database\Entities\AbstractTransactionEntity;
use Spiral\Transactions\Database\Types\TransactionStatus;
use Spiral\Transactions\PaymentSourceInterface;

class Transaction extends AbstractTransactionEntity
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'status' => TransactionStatus::class,

        'gateway'                => 'string',
        'gateway_transaction_id' => 'string(255)',

        'paymentSource' => [
            self::BELONGS_TO => PaymentSourceInterface::class
        ],
        'revisions'     => [
            self::HAS_MANY                 => Transaction\Revision::class,
            Transaction\Revision::ORDER_BY => ['{@}.id' => 'ASC']
        ],

        //first paid amount
        'total_amount'  => 'float',
        'currency'      => 'string(8)',

        'refunds' => [
            self::HAS_MANY               => Transaction\Refund::class,
            Transaction\Refund::ORDER_BY => ['{@}.id' => 'ASC']
        ],

        'attributes' => [
            self::HAS_MANY => Transaction\Attribute::class
        ],
        'items'      => [
            self::HAS_MANY => Transaction\Item::class
        ]
    ];

    const DEFAULTS = [
        'currency' => 'usd'
    ];

    const INDEXES = [
        [self::INDEX, 'currency'],
        [self::UNIQUE, 'gateway_transaction_id'],
    ];
}