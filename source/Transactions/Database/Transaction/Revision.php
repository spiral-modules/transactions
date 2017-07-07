<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;
use Spiral\Transactions\Database\Types\RevisionOperation;

class Revision extends Record
{
    use TimestampsTrait;

    const DATABASE = 'revisions';

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
}