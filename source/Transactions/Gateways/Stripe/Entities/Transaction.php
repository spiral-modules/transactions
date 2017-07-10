<?php

namespace Spiral\Transactions\Gateways\Stripe\Entities;

use Spiral\Transactions\GatewaySourceInterface;
use Spiral\Transactions\GatewayTransactionInterface;
use Stripe\Charge;

class Transaction implements GatewayTransactionInterface
{
    /** @var Charge */
    protected $charge;

    /** @var float */
    protected $fee;

    /** @var Refund[] */
    protected $refunds = [];

    /**
     * Transaction constructor.
     *
     * @param Charge   $charge
     * @param float    $fee
     * @param Refund[] $refunds
     */
    public function __construct(Charge $charge, float $fee, array $refunds = [])
    {
        $this->charge = $charge;
        $this->fee = $fee;
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
        return $this->fee;
    }

    /**
     * {@inheritdoc}
     */
    public function getRefunds(): array
    {
        $refunds = [];
        foreach ($this->refunds as $refund) {
            $refunds[] = new Refund($refund);
        }

        return $refunds;
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