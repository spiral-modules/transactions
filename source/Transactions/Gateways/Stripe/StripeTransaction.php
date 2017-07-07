<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\GatewayTransactionInterface;
use Stripe\Charge;

class StripeTransaction implements GatewayTransactionInterface
{
    /** @var Charge */
    protected $charge;

    /** @var float */
    protected $fee;

    /**
     * @param \Stripe\Charge $charge
     * @param float          $fee
     */
    public function __construct(Charge $charge, float $fee)
    {
        $this->charge = $charge;
        $this->fee = $fee;
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
    public function getCurrency(): string
    {
        return $this->charge->currency;
    }

    public function getSource(): array
    {

    }
}