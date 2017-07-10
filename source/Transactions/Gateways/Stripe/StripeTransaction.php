<?php

namespace Spiral\Transactions\Gateways\Stripe;

use Spiral\Transactions\GatewayPaymentSourceInterface;
use Spiral\Transactions\GatewayTransactionInterface;
use Stripe\Charge;

class StripeTransaction implements GatewayTransactionInterface
{
    /** @var Charge */
    protected $charge;

    /** @var float */
    protected $fee;

    /**
     * @param Charge $charge
     */
    public function __construct(Charge $charge)
    {
        $this->charge = $charge;

        $fees = new StripeFees($charge);
        $this->fee = $fees->getFee();
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

    /**
     * {@inheritdoc}
     */
    public function getSource(): GatewayPaymentSourceInterface
    {
        return new StripePaymentSource($this->charge->source->jsonSerialize());
    }
}