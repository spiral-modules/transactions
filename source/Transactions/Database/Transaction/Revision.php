<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\Transactions\Database\Entities\AbstractTransactionEntity;
use Spiral\Transactions\Database\Types\RevisionOperation;

class Revision extends AbstractTransactionEntity
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'operation' => RevisionOperation::class,
        'refund'    => [
            self::HAS_ONE   => Refund::class,
            self::NULLABLE  => true,
            Refund::INVERSE => 'revision'
        ],
    ];

    const INDEXES = [
        [self::INDEX, 'operation']
    ];

    /**
     * Apply "purchase" value.
     */
    public function setPurchase()
    {
        $this->operation->setPurchase();
    }
}