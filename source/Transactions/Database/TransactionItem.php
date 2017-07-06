<?php

namespace Spiral\Transactions\Database;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;
use Spiral\Transactions\Database\Types\ItemType;

class TransactionItem extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'       => 'primary',
        'type'     => ItemType::class,
        'amount'   => 'float',
        'quantity' => 'int',
        'title'    => 'string(200)'
    ];

    const DEFAULTS = [
        'quantity' => 1
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
        return $this->type->isItem();
    }
}