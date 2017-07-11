<?php

namespace Spiral\Transactions\Processors;

use Spiral\Transactions\Database\Sources\RefundSource;
use Spiral\Transactions\Database\Transaction;
use Spiral\Transactions\GatewayInterface;
use Spiral\Transactions\GatewayTransactionInterface;

class UpdateProcessor
{
    /** @var GatewayInterface */
    protected $gateway;

    /** @var RefundSource */
    protected $refunds;

    /**
     * UpdateProcessor constructor.
     *
     * @param GatewayInterface $gateway
     * @param RefundSource     $refunds
     */
    public function __construct(GatewayInterface $gateway, RefundSource $refunds)
    {
        $this->gateway = $gateway;
        $this->refunds = $refunds;
    }

    /**
     * @param Transaction $transaction
     *
     * @return Transaction
     */
    public function update(Transaction $transaction)
    {
        $gatewayTransaction = $this->gateway->updateTransaction($transaction->getGatewayID());
        $transaction->setRefundedAmount($gatewayTransaction->getRefundedAmount());
        $transaction->setFeeAmount($gatewayTransaction->getFeeAmount());

        if ($gatewayTransaction->isRefunded()) {
            $transaction->setRefundedStatus();
        } elseif ($gatewayTransaction->isPartiallyRefunded()) {
            $transaction->setPartiallyRefunded();
        }

        $this->fillRefunds($transaction, $gatewayTransaction);
        $transaction->save();

        return $transaction;
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    protected function existingRefunds(Transaction $transaction): array
    {
        $ids = [];
        /** @var Transaction\Refund $refund */
        foreach ($transaction->refunds as $refund) {
            $ids[] = $refund->getGatewayID();
        }

        return $ids;
    }

    /**
     * @param Transaction                 $transaction
     * @param GatewayTransactionInterface $gatewayTransaction
     */
    protected function fillRefunds(Transaction $transaction, GatewayTransactionInterface $gatewayTransaction)
    {
        $refunds = array_diff_key($gatewayTransaction->getRefunds(), array_flip($this->existingRefunds($transaction)));

        /** @var \Spiral\Transactions\Gateways\Stripe\Entities\Refund $retrieved */
        foreach ($refunds as $retrieved) {
            /** @var Transaction\Refund $refund */
            $refund = $this->refunds->create();
            $refund->setGatewayID($retrieved->getGatewayID());
            $refund->setAmount($retrieved->getAmount());
            $refund->setDatetime($retrieved->getDatetime());

            $transaction->refunds->add($refund);
        }
    }
}