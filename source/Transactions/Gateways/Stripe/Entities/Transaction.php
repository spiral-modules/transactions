<?php

namespace Spiral\Transactions\Gateways\Stripe\Entities;

use Spiral\Transactions\Gateways\Stripe\Fees;
use Spiral\Transactions\Gateways\Stripe\Refunds;
use Spiral\Transactions\GatewaySourceInterface;
use Spiral\Transactions\GatewayTransactionInterface;
use Stripe\Charge;

class Transaction implements GatewayTransactionInterface
{
    /** @var Charge */
    protected $charge;

    /** @var Fees|null */
    protected $fees = null;

    /** @var Refunds|null */
    protected $refunds = null;

    /**
     * @param Charge $charge
     */
    public function __construct(Charge $charge, Fees $fees, Refunds $refunds)
    {
        $this->charge = $charge;
        $this->fees = $fees;
        $this->refunds = $refunds;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionID(): string
    {
        return $this->charge->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaidAmount(): float
    {
        return $this->charge->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefundedAmount(): float
    {
        return $this->charge->amount_refunded;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeeAmount(): float
    {
        return $this->fees->getFee($this->charge);
    }

    /**
     * {@inheritdoc}
     */
    public function getRefunds(): array
    {
        return $this->refunds->getRefunds($this->charge);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): string
    {
        return $this->charge->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function isPartiallyRefunded(): bool
    {
        return empty($this->isRefunded()) && !empty($this->getRefundedAmount());
    }

    /**
     * {@inheritdoc}
     */
    public function isRefunded(): bool
    {
        return $this->charge->refunded;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource(): GatewaySourceInterface
    {
        return new Source($this->charge->source->jsonSerialize());
    }
}