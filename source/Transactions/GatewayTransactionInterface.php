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
     * Refunds Amount
     *
     * @return GatewayRefundInterface[]
     */
    public function getRefunds(): array;

    /**
     * Transaction currency.
     *
     * @return string
     */
    public function getCurrency(): string;

    /**
     * If transaction is partially refunded.
     *
     * @return bool
     */
    public function isPartiallyRefunded(): bool;

    /**
     * If transaction is fully refunded.
     *
     * @return bool
     */
    public function isRefunded(): bool;

    /**
     * Transaction payment source.
     *
     * @return GatewaySourceInterface
     */
    public function getSource(): GatewaySourceInterface;
}