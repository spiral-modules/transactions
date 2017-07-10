<?php

namespace Spiral\Transactions;

interface GatewayRefundInterface
{
    /**
     * Internal gateway ID.
     *
     * @return string
     */
    public function getGatewayID(): string;

    /**
     * Refunded amount.
     *
     * @return float
     */
    public function getAmount(): float;
}