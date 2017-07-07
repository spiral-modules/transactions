<?php

namespace Spiral\Transactions;

use Spiral\Transactions\Database\Transaction;

interface GatewayInterface
{
    /**
     * @param Database\Transaction   $transaction
     * @param PaymentSourceInterface $paymentSource
     * @param array                  $metadata
     *
     * @return GatewayTransactionInterface
     * @throws Exceptions\GatewayException
     */
    public function createTransaction(
        Transaction $transaction,
        PaymentSourceInterface $paymentSource,
        array $metadata = []
    ): GatewayTransactionInterface;
}