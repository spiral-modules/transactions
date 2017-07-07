<?php

namespace Spiral\Transactions;

use Spiral\Transactions\PaymentSources\CreditCardSource;
use Spiral\Transactions\PaymentSources\TokenSource;

interface GatewayInterface
{
    /**
     * Gateway name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @param float       $amount
     * @param string      $currency
     * @param TokenSource $source
     * @param array       $params
     *
     * @return GatewayTransactionInterface
     * @throws Exceptions\GatewayException
     */
    public function payWithToken(float $amount, string $currency, TokenSource $source, array $params = []): GatewayTransactionInterface;

    /**
     * @param float            $amount
     * @param string           $currency
     * @param CreditCardSource $source
     * @param array            $params
     *
     * @return GatewayTransactionInterface
     * @throws Exceptions\GatewayException
     */
    public function payWithCreditCard(float $amount, string $currency, CreditCardSource $source, array $params = []): GatewayTransactionInterface;
}