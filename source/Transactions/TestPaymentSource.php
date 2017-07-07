<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 07.07.2017
 * Time: 12:28
 */

namespace Spiral\Transactions;

class TestPaymentSource implements PaymentSourceInterface
{
    public function getSourceID()
    {
    }

    /**
     * Gateway internal customer ID (if exists) - customer should be stored in the payment gateway system.
     *
     * @return string|null
     */
    public function getCustomerID()
    {
    }

    /**
     * Card holder name (John Doe).
     *
     * @return string
     */
    public function getCardHolder(): string
    {
    }

    /**
     * Card number.
     *
     * @return string
     */
    public function getCardNumber(): string
    {
    }

    /**
     * Card expiration year (2017).
     *
     * @return int
     */
    public function getExpYear(): int
    {
    }

    /**
     * Card expiration month (12).
     *
     * @return int
     */
    public function getExpMonth(): int
    {
    }

    /**
     * Card security code (CVC).
     *
     * @return int
     */
    public function getSecurityCode(): int
    {
    }
}