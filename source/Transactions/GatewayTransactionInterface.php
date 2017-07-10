<?php

namespace Spiral\Transactions;

interface GatewayTransactionInterface
{
    /**
     * Internal gateway ID.
     *
     * @return string
     */
    public function getTransactionID(): string;

    /**
     * Calculated fee amount.
     *
     * @return float
     */
    public function getFeeAmount(): float;

    /**
     * Paid amount.
     *
     * @return float
     */
    public function getPaidAmount(): float;

    /**
     * Refunded amount.
     *
     * @return float
     */
    public function getRefundedAmount(): float;

    /**
     * Transaction currency.
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * Transaction payment source.
     *
     * @return GatewayPaymentSourceInterface
     */
    public function getSource(): GatewayPaymentSourceInterface;
}