<?php

namespace Spiral\Transactions\Gateways\Stripe\Entities;

use Spiral\Transactions\GatewayRefundInterface;

class Refund implements GatewayRefundInterface
{
    /** @var \Stripe\Refund */
    protected $refund;

    /**
     * Refund constructor.
     *
     * @param \Stripe\Refund $refund
     */
    public function __construct(\Stripe\Refund $refund)
    {
        $this->refund = $refund;
    }

    /**
     * {@inheritdoc}
     */
    public function getGatewayID(): string
    {
        return $this->refund->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): float
    {
        return $this->refund->amount;
    }
}