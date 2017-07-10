<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

/**
 * Class Item
 *
 * @property string $type
 * @property float  $amount
 * @property int    $quantity
 * @property string $title
 * @package Spiral\Transactions\Database\Transaction
 */
class Item extends Record
{
    use TimestampsTrait;

    const DEFAULT_TYPE    = 'item';
    const CORRECTION_TYPE = 'correction';
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
        return $this->getAmount() * $this->getQuantity();
    }

    /**
     * If current item is a purchased item (not a discount or another internal entity).
     *
     * @return bool
     */
    public function isCountable(): bool
    {
        return $this->getType() === static::DEFAULT_TYPE;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}