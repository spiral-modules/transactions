<?php

namespace Spiral\Transactions\Database\Transaction;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;

/**
 * Class Refund
 *
 * @property string $gateway_id
 * @property float  $amount
 *
 * @package Spiral\Transactions\Database\Transaction
 */
class Refund extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'id'         => 'primary',
        'gateway_id' => 'string(255)',
        'amount'     => 'float'
    ];

    const INDEXES = [
        [self::UNIQUE, 'gateway_id'],
    ];

    /**
     * @return string
     */
    public function getGatewayID(): string
    {
        return $this->gateway_id;
    }

    /**
     * @param string $id
     */
    public function setGatewayID(string $id)
    {
        $this->gateway_id = $id;
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
}