<?php

namespace Spiral\Transactions\Database;

use Spiral\Models\Traits\TimestampsTrait;
use Spiral\ORM\Record;
use Spiral\ORM\Entities\Relations\HasManyRelation;
use Spiral\Transactions\Database\Transaction\Source;
use Spiral\Transactions\Database\Types\TransactionStatus;

/**
 * Class Transaction
 *
 * @package Spiral\Transactions\Database
 *
 * @property string                             $currency
 * @property string                             $gateway
 * @property string                             $gateway_id
 * @property float                              $paid_amount
 * @property float                              $refunded_amount
 * @property float|null                         $fee_amount
 * @property Source                             $source
 * @property TransactionStatus                  $status
 * @property HasManyRelation|Transaction\Refund $refunds
 * @property HasManyRelation                    $attributes
 * @property HasManyRelation|Transaction\Item[] $items
 */
class Transaction extends Record
{
    use TimestampsTrait;

    const DATABASE = 'transactions';

    const SCHEMA = [
        'status' => TransactionStatus::class,

        'gateway'    => 'string',
        'gateway_id' => 'string(255)',

        'source' => [
            self::HAS_ONE => Source::class
        ],

        'id'              => 'primary',
        'paid_amount'     => 'float',
        'refunded_amount' => 'float, nullable',
        'fee_amount'      => 'float',
        'currency'        => 'string(8)',

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
        [self::UNIQUE, 'gateway_id'],
    ];

    /**
     * Apply "completed" value.
     */
    public function setCompletedStatus()
    {
        $this->status->setCompleted();
    }

    /**
     * Apply "refunded" value.
     */
    public function setRefundedStatus()
    {
        $this->status->setRefunded();
    }

    /**
     * Apply "partially-refunded" value.
     */
    public function setPartiallyRefunded()
    {
        $this->status->setPartiallyRefunded();
    }

    /**
     * Apply "failed" value.
     */
    public function setFailedStatus()
    {
        $this->status->setFailed();
    }

    /**
     * @return string
     */
    public function getGateway(): string
    {
        return $this->gateway;
    }

    /**
     * @param string $gateway
     */
    public function setGateway(string $gateway)
    {
        $this->gateway = $gateway;
    }

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
    public function getPaidAmount(): float
    {
        return $this->paid_amount;
    }

    /**
     * @param float $amount
     */
    public function setPaidAmount(float $amount)
    {
        $this->paid_amount = $amount;
    }

    /**
     * @param float $inc
     */
    public function incPaidAmount(float $inc)
    {
        $this->paid_amount += $inc;
    }

    /**
     * @return float|null
     */
    public function getRefundedAmount()
    {
        return $this->refunded_amount;
    }

    /**
     * @param float $amount
     */
    public function setRefundedAmount(float $amount)
    {
        $this->refunded_amount = $amount;
    }

    /**
     * @return float
     */
    public function getFeeAmount(): float
    {
        return $this->fee_amount;
    }

    /**
     * @param float $amount
     */
    public function setFeeAmount(float $amount)
    {
        $this->fee_amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isRefundable(): bool
    {
        return $this->isFullyRefundable() || $this->isPartiallyRefundable();
    }

    /**
     * @return bool
     */
    public function isFullyRefundable(): bool
    {
        return $this->status->isCompleted();
    }

    /**
     * @return bool
     */
    public function isPartiallyRefundable(): bool
    {
        return $this->status->isPartiallyRefunded();
    }
}