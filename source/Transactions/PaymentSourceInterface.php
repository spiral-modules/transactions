<?php

namespace Spiral\Transactions;

interface PaymentSourceInterface
{
    /**
     * Gateway internal card ID (if exists) - card should be stored in the payment gateway system.
     *
     * @return string|null
     */
    public function getGatewayID();

    /**
     * Card type (Maestro, Visa, AMEX, etc).
     *
     * @return string
     */
    public function getCardType(): string;

    /**
     * Card holder name (John Doe).
     *
     * @return string
     */
    public function getCardHolder(): string;

    /**
     * Card expiration year (2017).
     *
     * @return int
     */
    public function getExpYear(): int;

    /**
     * Card expiration month (12).
     *
     * @return int
     */
    public function getExpMonth(): int;

    /**
     * Card number last digits (1234).
     *
     * @return int
     */
    public function getNumberEnding(): int;
}