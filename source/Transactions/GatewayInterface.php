<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Sources\CreditCardSource;
use Spiral\Transactions\Sources\TokenSource;

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
     * @throws Exceptions\ClientException|Exceptions\InternalException
     */
    public function payWithToken(float $amount, string $currency, TokenSource $source, array $params = []): GatewayTransactionInterface;

    /**
     * @param float            $amount
     * @param string           $currency
     * @param CreditCardSource $source
     * @param array            $params
     *
     * @return GatewayTransactionInterface
     * @throws Exceptions\ClientException|Exceptions\InternalException
     */
    public function payWithCreditCard(float $amount, string $currency, CreditCardSource $source, array $params = []): GatewayTransactionInterface;

    /**
     * @param string $id
     *
     * @return GatewayTransactionInterface
     * @throws Exceptions\InternalException
     */
    public function updateTransaction(string $id): GatewayTransactionInterface;

    /**
     * @param string $id
     *
     * @return string
     */
    public function transactionGatewayURI(string $id): string;
}