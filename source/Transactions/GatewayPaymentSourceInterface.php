<?php
/**
 * Created by PhpStorm.
 * User: Valentin
 * Date: 10.07.2017
 * Time: 9:49
 */

namespace Spiral\Transactions;


interface GatewayPaymentSourceInterface
{
    /**
     * Internal gateway ID.
     *
     * @return string
     */
    public function getSourceID(): string;

    /**
     * Payment source type (Visa, Maestro, etc.)
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Card holder name.
     *
     * @return string
     */
    public function getCardHolder(): string;

    /**
     * Card expiration month.
     *
     * @return string
     */
    public function getExpMonth(): string;

    /**
     * Card expiration year.
     *
     * @return string
     */
    public function getExpYear(): string;

    /**
     * Card number ending.
     *
     * @return string
     */
    public function getNumberEnding(): string;
}