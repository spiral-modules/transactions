<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

class Item extends Record
{
    use TimestampsTrait;

    const DEFAULT_TYPE    = 'item';
    const REGULATION_TYPE = 'regulation';
    const DISCOUNT_TYPE   = 'discount';

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'       => 'primary',
        'type'     => 'string(32)',
        'amount'   => 'float',
        'quantity' => 'int',
        'title'    => 'string(255)'
    ];

    const DEFAULTS = [
        'type'     => self::DEFAULT_TYPE,
        'quantity' => 1
    ];

    const INDEXES = [
        [self::INDEX, 'type'],
        [self::INDEX, 'title'],
    ];

    /**
     * Full item amount (amount per item * quantity).
     *
     * @return float
     */
    public function fullAmount(): float
    {
        return $this->amount * $this->quantity;
    }

    /**
     * If current item is a purchased item (not a discount or another internal entity).
     *
     * @return bool
     */
    public function isCountable(): bool
    {
        return $this->type === static::DEFAULT_TYPE;
    }
}